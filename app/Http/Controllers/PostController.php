<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
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

        $this->updatePostsCache($user);
        $this->publishPostsToCentrifugo($user);

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
        $posts = $this->getPostsCache($user);
        $this->publishPostsToCentrifugo($user);

        return response()->json($posts, 200);
    }

    public function getMyPosts(Request $request)
    {
        $user = $request->user();
        $posts = $this->getPostsCache($user);
        $this->publishPostsToCentrifugo($user);

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
        $this->updatePostsCache($user);
        $this->publishPostsToCentrifugo($user);

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

        return response()->json($posts, 200);
    }

    protected function getPostsCache($user)
    {
        return json_decode(Redis::get('user_'.$user->id.'_posts'));
    }

    protected function updatePostsCache($user)
    {
        $posts = $user->posts()->without("comments")->get();
        Redis::set('user_'.$user->id.'_posts', $posts);
        return $posts;
    }

    protected function publishPostsToCentrifugo($user)
    {
        $posts = $this->getPostsCache($user);
        $this->centrifugo->publish('user_'.$user->id.'_feed', ['posts' => $posts]);
        return $posts;
    }
}
