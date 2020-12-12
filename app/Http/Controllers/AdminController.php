<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\lib\My_func;

class AdminController extends Controller
{
    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.index', ['posts' => $posts]);
    }

    public function destroy(Request $request)
    {
        $post = Post::findOrFail($request['post_id']);

        if (!empty($post->img)) {
            Storage::delete($post->img);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->delete();
        });


        return redirect()->route('admin_top');
    }

    public function multDestroy(Request $request)
    {
        $post_ids = $request['post_ids'];

        foreach ($post_ids as $post_id) {
            $post = Post::findOrFail((int) $post_id);

            if (!empty($post->img)) {
                Storage::delete($post->img);
            }

            DB::transaction(function () use ($post) {
                $post->comments()->delete();
                $post->delete();
            });
        }

        return redirect()->route('admin_top');
    }

    public function showComments($post_id)
    {
        $post = Post::findOrFail($post_id);

        return view('admin.showComments', ['post' => $post]);
    }

    public function commentDestroy(Request $request)
    {
        $comment = Comment::findOrFail($request['comment_id']);

        $comment->delete();

        return view('admin.showComments', ['post' => $comment->getPost()]);
    }

    public function commentMultDestroy(Request $request)
    {
        $post = Post::findOrFail($request['post_id']);

        $comment_ids = $request['comment_ids'];

        foreach ($comment_ids as $comment_id) {
            $comment = Comment::findOrFail((int) $comment_id);

            DB::transaction(function () use ($comment) {
                $comment->delete();
            });
        }

        return view('admin.showComments', ['post' => $post]);
    }

    public function search(Request $request)
    {
        $post_query = Post::query();

        $keywords = [
            'title' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['title']),
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];

        $posts = Post::where(function ($post_query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $post_query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->get();

        foreach ($posts as $post) {
            $post['has_comments'] = false;
            $post['_token'] = $request['_token'];

            if ($post->comments->count()) {
                $post['has_comments'] = true;
            }

            if (!empty($post->img)) {
                $post['img'] = asset('storage/' . $post->img);
            }
        }

        return response()->json($posts);
    }
}
