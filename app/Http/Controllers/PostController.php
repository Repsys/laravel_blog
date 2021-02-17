<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private $centrifugo;

    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100',
            'text' => 'required|max:1000',
        ]);

        $user = $request->user();
        $post = new Post($request->all());
        $user->posts()->save($post);
        $this->centrifugo->publish('my_posts', ['posts' => $user->posts()->get()]);

        $response = [
            'message' => 'Post created successfully',
            'data'    => $post,
        ];
        return response()->json($response, 201);
    }

    public function getPosts(Request $request, $user_id)
    {
        Validator::validate([
            'user_id' => $user_id
        ],[
            'user_id' => 'integer|exists:users,id',
        ]);

        $user = User::query()->find($user_id);
        $posts = $user->posts()->get();
        $this->centrifugo->publish('user_'.$user_id.'_posts', ['posts' => $posts]);

        return response()->json($posts, 200);
    }

    public function getMyPosts(Request $request)
    {
        $user = $request->user();
        $posts = $user->posts()->with("comments")->get();
        $this->centrifugo->publish('my_posts', ['posts' => $posts]);
        return response()->json($posts, 200);
    }

    public function deletePost(Request $request, $post_id)
    {
        Validator::validate([
            'post_id' => $post_id
        ],[
            'post_id' => 'integer|exists:posts,id',
        ]);

        $post = Post::query()->find($post_id);
        $user = $request->user();

        if ($post->user()->isNot($user)) {
            $response = [
                'message' => 'You dont have permissions to delete this post'
            ];
            return response()->json($response, 403);
        }

        $post->delete();
        $this->centrifugo->publish('my_posts', ['posts' => $user->posts()->get()]);

        $response = [
            'message' => 'Post deleted successfully'
        ];
        return response()->json($response, 200);
    }

    public function getFeed(Request $request)
    {
        $user = $request->user();

        $posts = collect();
        $user->subscriptions()->get()
            ->each(function ($subscription, $key) use (&$posts) {
            $posts = $posts->concat($subscription->posts()->get());
        });
        $posts = $posts->sortByDesc('created_at')->take(50)->values();
        $this->centrifugo->publish('feed', ['posts' => $posts]);

        return response()->json($posts, 200);
    }
}
