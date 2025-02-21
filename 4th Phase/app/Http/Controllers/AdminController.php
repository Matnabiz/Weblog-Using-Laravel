<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class AdminController extends Controller
{
    public function dashboard(){
        $userCount = User::count();
        $postCount = Post::count();
        $tagCount = Tag::count();

        return response()->json([
            'user_count' => $userCount,
            'post_count' => $postCount,
            'tag_count' => $tagCount
        ]);
    }

    public function assignAuthor(Request $request, $id){
        $user = User::findOrFail($id);
        $user->role = 'author';
        $user->save();

        return response()->json(['message' => 'User assigned as author successfully']);
    }
}
