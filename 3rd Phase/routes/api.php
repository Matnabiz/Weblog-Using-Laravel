<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/posts', [PostController::class, 'store']);
Route::middleware('auth:sanctum')->put('/posts/{id}', [PostController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/posts/{id}', [PostController::class, 'destroy']);
Route::get('/user/posts', [PostController::class, 'userPosts'])->middleware('auth:api');
Route::middleware('auth:sanctum')->get('/posts', [PostController::class, 'index']);
Route::middleware('auth:sanctum')->get('/posts/{id}', [PostController::class, 'show']);
Route::get('/posts/search/{query}', [PostController::class, 'search']);
Route::middleware('auth:api')->post('/posts/{id}/like', [PostController::class, 'likePost']);
Route::middleware('auth:api')->delete('/posts/{id}/like', [PostController::class, 'unlikePost']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/posts/{id}/likes', [PostController::class, 'showLikes']);

