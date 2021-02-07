<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('api')->group(function() {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group( function () {
        Route::get('/users/{login}', [UserController::class, 'getUser']);
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'editProfile']);

        Route::put('/blacklist/{login}', [UserController::class, 'blacklistUser']);
        Route::put('/subscribe/{login}', [UserController::class, 'subscribeToUser']);

        Route::post('/posts', [PostController::class, 'createPost']);
        Route::get('/users/{login}/posts', [PostController::class, 'getPosts']);
        Route::get('/profile/posts', [PostController::class, 'getMyPosts']);
        Route::delete('/posts/{id}', [PostController::class, 'deletePost']);

        Route::post('/posts/{id}/comments', [CommentController::class, 'createComment']);
        Route::delete('/posts/{post_id}/comments/{id}', [CommentController::class, 'deleteComment']);

        Route::get('/feed', [PostController::class, 'getFeed']);
    });
});
