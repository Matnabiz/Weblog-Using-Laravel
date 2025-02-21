<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with('posts')->get();

        return response()->json([
            'tags' => $tags
        ]);
    }
}
