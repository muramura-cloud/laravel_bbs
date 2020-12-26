<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class AdminController extends Controller
{
    private $per_page = 10;

    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.index', ['posts' => $posts]);
    }

    public function comment()
    {
        $comments = Comment::paginate(10);

        return view('admin.comment', ['comments' => $comments]);
    }

    public function showComments($post_id = null, Request $request)
    {
        // コメント一覧のページネーションで必要。
        if ($request['ajax'] === 'true') {
            $post = Post::findOrFail((int) $request['post_id']);
            $comments = $post->comments()->paginate($this->per_page, ['*'], 'page', (int) $request['page']);

            return response()->json($comments);
        }

        $post = Post::with(['comments'])->findOrFail((int) $post_id);
        $comments = $post->comments()->paginate($this->per_page, ['*'], 'page', 1);

        return view('admin.showComments', ['post' => $post, 'comments' => $comments]);
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

        return response()->json($this->getSearchedPosts($request));
    }

    public function multDestroy(Request $request)
    {
        foreach ($request['post_ids'] as $post_id) {
            $post = Post::findOrFail((int) $post_id);

            if (!empty($post->img)) {
                Storage::delete($post->img);
            }

            DB::transaction(function () use ($post) {
                $post->comments()->delete();
                $post->delete();
            });
        }

        return response()->json($this->getSearchedPosts($request));
    }

    public function commentDestroy(Request $request)
    {
        $comment = Comment::findOrFail((int) $request['comment_id']);

        if ($request['show_comment_list']) {
            $comment->delete();

            return response()->json($this->getSearchedComments($request));
        }

        $post = $comment->getPost();
        $comment->delete();

        // マルチをそうだけど、これ$request['current_page']ってちゃんと値取れてる？$request['page']な気がするけど。多分最初のトップページに行った時にバグる気がする。
        return response()->json($post->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']));
    }

    public function commentMultDestroy(Request $request)
    {
        foreach ($request['comment_ids'] as $comment_id) {
            $comment = Comment::findOrFail((int) $comment_id);

            DB::transaction(function () use ($comment) {
                $comment->delete();
            });
        }

        if ($request['show_comment_list']) {
            return response()->json($this->getSearchedComments($request));
        }

        $post = Post::findOrFail($request['post_id']);

        return response()->json($post->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']));
    }

    public function search(Request $request)
    {
        $posts = $this->getSearchedPosts($request);

        $keywords = [
            'title' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['title']),
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];
        // コメント一覧から投稿一覧を表示する
        if ($request['ajax'] === 'false') {
            return view('admin.index', ['posts' => $posts, 'keywords' => $keywords, 'page' => $request['page']]);
        }

        return response()->json($posts);
    }

    public function commentSearch(Request $request)
    {
        $comments = $this->getSearchedComments($request);

        return response()->json($comments);
    }

    public function showReported(Request $request)
    {
        $ids = array_column(Report::where('table_name', $request->table_name)->get('target_id')->toArray(), 'target_id');

        $model = new Post;
        if ($request->table_name === 'comments') {
            $model = new Comment;
        }

        $contents = $model::where(function ($post_query) use ($ids) {
            foreach ($ids as $id) {
                $post_query->orWhere('id', $id);
            }
        })->orderBy('created_at', 'desc')->paginate($this->per_page, ['*'], 'page', (int) $request['page']);

        return response()->json($contents);
    }

    // これ多分引数でキーワードを受け取ったほうが汎用的になる。
    private function getSearchedPosts($request)
    {
        $keywords = [
            'title' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['title']),
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];

        $posts = Post::where(function ($post_query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $post_query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->paginate($this->per_page, ['*'], 'page', (int) $request['page']);

        // jsにおくるデータにページ遷移に必要なデータとトークンを加える。
        foreach ($posts as $post) {
            $post['has_comments'] = false;
            $post['_token'] = $request['_token'];
            $post['keywords'] = $keywords;

            if ($post->comments->count()) {
                $post['has_comments'] = true;
            }

            if (!empty($post->img)) {
                $post['img'] = asset('storage/' . $post->img);
            }
        }

        return $posts;
    }

    // どのテーブル引数に指定するだけで良いのでは？
    private function getSearchedComments($request)
    {
        $keywords = [
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];

        $comments = Comment::where(function ($post_query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $post_query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->paginate($this->per_page, ['*'], 'page', (int) $request['page']);

        // jsにおくるデータにページ遷移に必要なデータとトークンを加える。
        foreach ($comments as $comment) {
            $comment['_token'] = $request['_token'];
            $comment['keywords'] = $keywords;
        }

        return $comments;
    }
}
