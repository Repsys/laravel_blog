<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function createComment(Request $request, $post_id)
    {
        Validator::validate([
            'post_id' => $post_id
        ],[
            'post_id' => 'integer|exists:posts,id',
        ]);

        $request->validate([
            'text' => 'required|max:500',
        ]);

        $comment = new Comment($request->all());
        $user = $request->user();
        $comment->user()->associate($user);
        $post = Post::all()->find($post_id);
        $comment->post()->associate($post);
        $comment->save();

        $response = [
            'message' => 'Comment created successfully',
            'data'    => $comment,
        ];
        return response()->json($response, 201);
    }

    public function getComments(Request $request, $post_id)
    {
        Validator::validate([
            'post_id' => $post_id
        ],[
            'post_id' => 'integer|exists:posts,id',
        ]);

        $post = Post::all()->find($post_id);
        $comments = $post->comments()->get();

        return response()->json($comments, 200);
    }

    public function deleteComment(Request $request, $comment_id)
    {
        Validator::validate([
            'comment_id' => $comment_id
        ],[
            'comment_id' => 'integer|exists:comments,id',
        ]);

        $comment = Comment::all()->find($comment_id);
        $user = $request->user();

        if ($comment->user()->isNot($user)) {
            $response = [
                'message' => 'You dont have permissions to delete this post'
            ];
            return response()->json($response, 403);
        }

        $comment->delete();
        $response = [
            'message' => 'Comment deleted successfully'
        ];
        return response()->json($response, 200);
    }
}
