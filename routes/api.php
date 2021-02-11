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
        Route::get('/users/{user_id}', [UserController::class, 'getUser'])
            ->middleware('check_blacklist:user');
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'editProfile']);

        Route::get('/profile/subscriptions', [UserController::class, 'getSubscriptions']);
        Route::get('/profile/subscribers', [UserController::class, 'getSubscribers']);
        Route::put('/users/{user_id}/blacklist', [UserController::class, 'blacklistUser']);
        Route::put('/users/{user_id}/subscribe', [UserController::class, 'subscribeToUser'])
            ->middleware('check_blacklist:user');

        Route::post('/posts', [PostController::class, 'createPost']);
        Route::get('/users/{user_id}/posts', [PostController::class, 'getPosts'])
            ->middleware('check_blacklist:user');
        Route::get('/profile/posts', [PostController::class, 'getMyPosts']);
        Route::delete('/posts/{post_id}', [PostController::class, 'deletePost']);

        Route::post('/posts/{post_id}/comments', [CommentController::class, 'createComment'])
            ->middleware('check_blacklist:post');
        Route::get('/posts/{post_id}/comments', [CommentController::class, 'getComments'])
            ->middleware('check_blacklist:post');
        Route::delete('/comments/{comment_id}', [CommentController::class, 'deleteComment']);

        Route::get('/feed', [PostController::class, 'getFeed']);
    });
});
