<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post){
 
        return response()->json($post->comments()->with('user')->get());
    }

    public function store(Request $request, Post $post){
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

   
        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $commentId){
        $comment = Comment::findOrFail($commentId);

        if ($request->user()->id !== $comment->user_id)
            return response()->json(['error' => 'Unauthorized'], 403);

        $validatedData = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $validatedData['body'],
        ]);
        

        return response()->json([
            'message' => 'Comment updated successfully.',
            'comment' => $comment,
        ]);
    }

    public function delete(Request $request, $commentId){
        $comment = Comment::findOrFail($commentId);

        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }

    public function storeLike(Request $request, $commentId){
        $user = $request->user();
        $comment = Comment::findOrFail($commentId);

        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already liked this comment'], 400);
        }

        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $comment->id,
            'likeable_type' => Comment::class,
        ]);

        return response()->json(['message' => 'Comment liked successfully']);
    }

    public function destroyLike(Request $request, $commentId){
        $user = $request->user();
        $comment = Comment::findOrFail($commentId);

        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Comment unliked successfully']);
        }

        return response()->json(['message' => 'You have not liked this comment'], 400);
    }

    public function showLike($commentId){
        $comment = Comment::with('likes')->findOrFail($commentId);
        $likeCount = $comment->likes->count();

        return response()->json([
            'comment' => $comment,
            'likes_count' => $likeCount,
        ]);
    }
}

