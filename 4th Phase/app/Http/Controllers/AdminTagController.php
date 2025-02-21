<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminTagController extends Controller
{
    public function index(){
        $tags = Tag::all();
        return response()->json($tags);
    }
    
    public function store(Request $request){
        $tag = Tag::create($request->all());
        return response()->json(['message' => 'Tag created successfully', 'tag' => $tag]);
    }
    
    public function destroy($id){
        $tag = Tag::findOrFail($id);
        $tag->delete();
    
        return response()->json(['message' => 'Tag deleted successfully']);
    }
    
}
