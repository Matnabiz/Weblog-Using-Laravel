<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EndpointController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('/register', [AuthController::class, 'register'])->middleware('prevent.register');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/posts', [PostController::class, 'store']);
Route::middleware('auth:sanctum')->post('/posts/publishLater', [PostController::class, 'publish']);
Route::middleware('auth:sanctum')->put('/posts/{id}', [PostController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/posts/{id}', [PostController::class, 'destroy']);
Route::get('/user/posts', [PostController::class, 'userPosts'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/posts', [PostController::class, 'index']);
Route::middleware('auth:sanctum')->get('/posts/{id}', [PostController::class, 'show']);
Route::get('/posts/search/{query}', [PostController::class, 'search']);
Route::middleware('auth:sanctum')->post('/posts/{id}/like', [PostController::class, 'likePost']);
Route::middleware('auth:sanctum')->delete('/posts/{id}/unlike', [PostController::class, 'unlikePost']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/posts/{id}/likes', [PostController::class, 'showLikes']);

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    Route::put('/users/{id}', [AdminUserController::class, 'updateUser']);
    Route::get('/posts', [AdminPostController::class, 'index']); // Manage posts
    Route::get('/posts/export-weekly', [AdminPostController::class, 'exportWeeklyPosts']);
    Route::delete('/posts/{id}', [AdminPostController::class, 'destroy']);
    Route::get('/tags', [AdminTagController::class, 'index']);
    Route::post('/tags', [AdminTagController::class, 'store']);
    Route::delete('/tags/{id}', [AdminTagController::class, 'destroy']);
    Route::post('/users/{id}/assign-author', [AdminController::class, 'assignAuthor']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::put('/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('/comments/{commentId}', [CommentController::class, 'delete']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments/{commentId}/like', [CommentController::class, 'storeLike']);
    Route::delete('/comments/{commentId}/unlike', [CommentController::class, 'destroyLike']);
    Route::get('/comments/{commentId}/likes', [CommentController::class, 'showLike']);
});

Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'index']);

Route::get('/endpoint', [EndpointController::class, 'fetchAndTransformData'])->middleware('auth:api');
