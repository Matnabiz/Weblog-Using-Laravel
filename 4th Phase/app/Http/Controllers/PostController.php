<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Notification;
use App\Mail\PostNotificationMail;
use App\Jobs\PublishPostJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class PostController extends Controller
{

    public function store(Request $request){

        if (!($request->user()->role == 'author' || $request->user()->role == 'admin'))
            return response()->json(['error' => 'Unauthorized: Only authors can submit posts'], 403);

        $postLimit = 5;
        $postsToday = Post::where('user_id', $request->user()->id)
        ->whereDate('created_at', now()->toDateString())
        ->count();


        if ($postsToday >= $postLimit) {
            return response()->json([
                'error' => 'You have reached the daily limit of posts.'],
                403);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string'
        ]);

        $post = Post::create([
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'user_id' => $request->user()->id
        ]);

        if (isset($validatedData['tags'])) {
            $tags = collect($validatedData['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $post->tags()->sync($tags->pluck('id')->toArray());
        }

        $author = $request->user();
        $users = User::where('id', '!=', $author->id)->get();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new PostNotificationMail($post, $author));
        }
        $this->sendPostNotification($post);
        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ]);
    }

    public function sendPostNotification($post){
        $users = User::where('id', '!=', $post->user_id)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'subject' => 'New Post: ' . $post->title,
                'message' => 'A new post has been created by ' . $post->user->name . '. Check it out!',
            ]);
        }
    }

    public function publish(Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'published_at' => 'nullable|date',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
        ]);

        $post = Post::create([
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'user_id' => $request->user()->id,
            'published_at' => $validatedData['published_at'] ?? null,
        ]);

        if (isset($validatedData['tags'])) {
            $tags = collect($validatedData['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $post->tags()->sync($tags->pluck('id')->toArray());
        }

        if ($post->published_at) {
            $publishTime = Carbon::parse($post->published_at);

            if ($publishTime->isFuture()) {
                $delay = $publishTime->diffInSeconds(Carbon::now());
                PublishPostJob::dispatch($post)->delay($delay);
            } else {
                return response()->json(['error' => 'Publish time must be in the future.'], 400);
            }
        }

        return response()->json(['message' => 'Post created successfully', 'post' => $post]);
    }

    public function update(Request $request, $id){

        if ($request->user()->role !== 'author')
            return response()->json(['error' => 'Unauthorized: Only authors can submit posts'], 403);

        $post = Post::findOrFail($id);
        if ($request->user()->id !== $post->user_id)
            return response()->json(['error' => 'Unauthorized'], 403);
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string'
        ]);
        $post->update($validatedData);
        if (isset($validatedData['tags'])) {
            $tags = collect($validatedData['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });
            $post->tags()->sync($tags->pluck('id')->toArray());
        }
    }

    public function destroy(Request $request, $id){

        $post = Post::findOrFail($id);
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }

    public function index(Request $request){
        $user = $request->user();

        $query = Post::query();

        if (!$user->isAdmin()) {
            $query->where(function ($query) {

                $query->whereNull('published_at')  // Unpublished posts
                      ->orWhere('published_at', '<=', now()); // Published posts
            });
        }

        $posts = $query->with(['tags', 'likes'])->get();

        return response()->json([
            'posts' => $posts,
        ]);
    }

    public function userPosts(){
        $user = auth()->user();

        $posts = Post::where('user_id', $user->id)
                    ->with(['tags', 'likes'])
                    ->get()
                    ->map(function ($post) {
                        $post->like_count = $post->likes->count();

                        $post->liked_by_user = $post->likes->where('user_id', auth()->id())->isNotEmpty();

                        return $post;
                    });

        return response()->json([
            'posts' => $posts
        ]);
    }

    public function show(Request $request, $id){
        $post = Post::findOrFail($id);

        if (!$request->user()->isAdmin() && (!$post->published_at || $post->published_at->isFuture())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['post' => $post]);
    }

    public function search(Request $request, $query){
        $posts = Post::where('title', 'LIKE', "%{$query}%")
        ->orWhere('body', 'LIKE', "%{$query}%")
        ->orWhereHas('user', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })
        ->with('user')
        ->get();
        return response()->json([
        'posts' => $posts
        ]);
    }

    public function likePost(Request $request, $postId){
        $user = $request->user();
        $alreadyLiked = Like::where('user_id', $user->id)
                            ->where('likeable_id', $postId)
                            ->where('likeable_type', Post::class)
                            ->exists();

        if ($alreadyLiked)
            return response()->json(['message' => 'You have already liked this post.'], 400);

        $like = Like::create([
            'user_id'      => $user->id,
            'likeable_id'  => $postId,
            'likeable_type' => Post::class,
        ]);

        return response()->json(['message' => 'Post liked successfully.'], 201);
    }

    public function unlikePost(Request $request, $postId){
        $user = $request->user();

        $like = Like::where('user_id', $user->id)
                    ->where('likeable_id', $postId)
                    ->where('likeable_type', Post::class)
                    ->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Post unliked successfully.'], 200);
        }

        return response()->json(['message' => 'You have not liked this post.'], 400);
    }

    public function showLikes($id){
        $post = Post::with('likedByUsers')->findOrFail($id);

        $likedByUsers = $post->likedByUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        $likeCount = $post->likedByUsers->count();

        return response()->json([
            'post' => $post,
            'liked_by_users' => $likedByUsers,
            'like_count' => $likeCount
        ]);
    }


}

