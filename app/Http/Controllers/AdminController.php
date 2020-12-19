<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private $per_page = 10;

    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.index', ['posts' => $posts]);
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
        // なんとかこれでうまくいった。なんかページネーターオブジェクト見てみたら、現在ページがなぜか１ページじゃなかったから
        $comments = $post->comments()->paginate($this->per_page, ['*'], 'page', 1);

        return view('admin.showComments', ['post' => $post, 'comments' => $comments]);
    }

    // 投稿が消された時に、例えば、２ページ目の投稿を見てた時に、そのにページ全ての投稿が表示されることになる。
    // ではなく、まず、キーワードに一致する全ての投稿を取得する。そして、現在のページを取得する。そして、現在のページに適した量の投稿を表示するようにするべき。
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

        // 投稿を削除した後も元々のページにあったキーワードで再度レコードを取得してビューに流す。
        $keywords = [
            'title' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['title']),
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];

        $posts = Post::where(function ($post_query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $post_query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', (int) $request['current_page']);

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

        return response()->json($posts);
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

        $keywords = [
            'title' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['title']),
            'body' => preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['body']),
        ];

        $posts = Post::where(function ($post_query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $post_query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', (int) $request['current_page']);

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

        return response()->json($posts);
    }

    public function commentDestroy(Request $request)
    {
        $comment = Comment::findOrFail((int) $request['comment_id']);
        $post = $comment->getPost();

        $comment->delete();

        $comments = $post->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']);

        return response()->json($comments);
    }

    public function commentMultDestroy(Request $request)
    {
        $comment_ids = $request['comment_ids'];
        $post = Post::findOrFail($request['post_id']);

        foreach ($comment_ids as $comment_id) {
            $comment = Comment::findOrFail((int) $comment_id);

            DB::transaction(function () use ($comment) {
                $comment->delete();
            });
        }

        $comments = $post->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']);

        return response()->json($comments);
    }

    public function search(Request $request)
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

        // コメント一覧から投稿一覧を表示する
        if ($request['ajax'] === 'false') {
            return view('admin.index', ['posts' => $posts, 'keywords' => $keywords, 'page' => $request['page']]);
        }

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

        return response()->json($posts);
    }
}
