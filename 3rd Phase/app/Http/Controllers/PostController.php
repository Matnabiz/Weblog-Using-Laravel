<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    public function store(Request $request){
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

        return response()->json(['message' => 'Post created successfully', 'post' => $post]);
    }

    public function update(Request $request, $id){
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
        $posts = Post::with(['tags', 'likes'])->get();
    
        $posts = $posts->map(function($post) use ($request) {
            return [
                'post' => $post,
                'tags' => $post->tags,
                'likes_count' => $post->likes->count(),
                'user_has_liked' => $post->likes()->where('user_id', $request->user()->id)->exists(), 
            ];
        });
    
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
    
    public function show($id, Request $request){
        $post = Post::with(['tags', 'likes'])->findOrFail($id);
    
        $userHasLiked = $post->likes()->where('user_id', $request->user()->id)->exists();
    
        return response()->json([
            'post' => $post,
            'tags' => $post->tags, 
            'likes_count' => $post->likes->count(), 
            'user_has_liked' => $userHasLiked,
        ]);
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

        if (Like::where('user_id', $user->id)->where('post_id', $postId)->exists()) {
            return response()->json(['message' => 'You have already liked this post.'], 400);
        }

        Like::create([
            'user_id' => $user->id,
            'post_id' => $postId,
        ]);

        return response()->json(['message' => 'Post liked successfully.'], 201);
    }

    public function unlikePost(Request $request, $postId){
        $user = $request->user();

        $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();
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

