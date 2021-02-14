<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Read;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private $per_page = 10;

    // トップページ(投稿一覧ページを表示)
    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.index', ['posts' => $posts]);
    }

    // コメント一覧ページを表示
    public function comment()
    {
        return view('admin.comment', ['comments' => Comment::orderBy('created_at', 'desc')->paginate(10)]);
    }

    // 「コメント一覧へ」ボタンを押したとき,投稿に紐づくコメントを表示
    public function showComments($post_id = null, Request $request)
    {
        // 投稿の紐づくコメントのページネーションに対応。
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
            Storage::disk('s3')->delete($post->img);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->likes()->delete();
            $post->delete();
        });

        return response()->json($this->getSearchedPosts($request));
    }

    public function multDestroy(Request $request)
    {
        foreach ($request['post_ids'] as $post_id) {
            $post = Post::findOrFail((int) $post_id);

            if (!empty($post->img)) {
                Storage::disk('s3')->delete($post->img);
            }

            DB::transaction(function () use ($post) {
                $post->comments()->delete();
                $post->likes()->delete();
                $post->delete();
            });
        }

        return response()->json($this->getSearchedPosts($request));
    }

    public function commentDestroy(Request $request)
    {
        Read::where('comment_id', $request['comment_id'])->delete();
        $comment = Comment::findOrFail((int) $request['comment_id']);
        $comment->delete();

        // コメント一覧ページから削除した場合
        if ($request['show_comment_list']) {
            return response()->json($this->getSearchedComments($request));
        }

        return response()->json($comment->getPost()->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']));
    }

    public function commentMultDestroy(Request $request)
    {
        foreach ($request['comment_ids'] as $comment_id) {
            Read::where('comment_id', (int) $comment_id)->delete();
            Comment::findOrFail((int) $comment_id)->delete();
        }

        // コメント一覧ページから削除した場合
        if ($request['show_comment_list']) {
            return response()->json($this->getSearchedComments($request));
        }

        $post = Post::findOrFail($request['post_id']);

        return response()->json($post->comments()->paginate(10, ['*'], 'page', (int) $request['current_page']));
    }

    // 投稿検索
    public function search(Request $request)
    {
        $posts = $this->getSearchedPosts($request);

        $keywords = [
            'title' => Helper::mbTrim($request['title']),
            'body' => Helper::mbTrim($request['body']),
        ];

        // 投稿に紐づくコメント一覧からトップページ(投稿一覧)を表示する場合
        if ($request['ajax'] === 'false') {
            return view('admin.index', ['posts' => $posts, 'keywords' => $keywords, 'page' => $request['page']]);
        }

        return response()->json($posts);
    }

    // コメント検索
    public function commentSearch(Request $request)
    {
        return response()->json($this->getSearchedComments($request));
    }

    // 報告された投稿あるいはコメントを表示する。
    public function showReported(Request $request)
    {
        $report = new Report;
        $model = new Post;
        if ($request->table_name === 'comments') {
            $model = new Comment;
        }

        return response()->json($report->getReportedContents($model, $report->getReportedContentIds($request['table_name']), $request['page']));
    }

    private function getSearchedPosts($request)
    {
        $keywords = [
            'title' => Helper::mbTrim($request['title']),
            'body' => Helper::mbTrim($request['body']),
        ];

        $posts = Post::with(['comments'])->where(function ($post_query) use ($keywords) {
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

            if (!empty($post->img) && $request['ajax'] !== 'false') {
                $post['img'] = Storage::disk('s3')->url($post->img);
            }
        }

        return $posts;
    }

    private function getSearchedComments($request)
    {
        $keywords = [
            'body' => Helper::mbTrim($request['body']),
        ];

        $comment = new Comment;
        $comments = $comment->getCommentsByKeyword($keywords, (int) $request['page']);

        // jsにおくるデータにページ遷移に必要なデータとトークンを加える。
        foreach ($comments as $comment) {
            $comment['_token'] = $request['_token'];
            $comment['keywords'] = $keywords;
        }

        return $comments;
    }
}
