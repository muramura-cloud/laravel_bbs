<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Like;
use App\Models\Category;
use App\Models\Read;
use App\Http\Requests\PostRequest;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    // トップページを表示
    public function index(Request $request)
    {
        $params = [
            'posts' => Post::with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate(10),
            'user' => $request->user,
            'like' => new Like,
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'categories' => Category::all(),
        ];

        return view('posts.index', $params);
    }

    // アプリ説明ページを表示
    public function introduce(Request $request)
    {
        $params = [
            'user' => $request->user,
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'categories' => Category::all(),
        ];

        return view('posts.introduce', $params);
    }

    // 投稿の新規作成ページを表示
    public function create(Request $request)
    {
        return view('posts.create', ['user' => $request->user, 'page' => $request->page]);
    }

    // 投稿保存
    public function store(PostRequest $request)
    {
        $params = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
            'category' => $request->category,
        ];

        // 画像があるならS3へ保存
        if (!empty($request->file('img'))) {
            $path = Storage::disk('s3')->put('/', $request->file('img'), 'public');
            $params['img'] = $path;
        }

        Post::create($params);

        return redirect()->route('top');
    }

    // 投稿詳細ページを表示
    public function show($post_id, Request $request)
    {
        // 既読処理 表示している投稿が自分が投稿したものだった場合、コメントを既読したことにする。
        if (!empty($request->user)) {
            if (Post::findOrFail($post_id)->user_id === $request->user->id) {
                Read::where('post_id', $post_id)->delete();
            }
        }

        $params = [
            'post' => Post::withCount('likes')->findOrFail($post_id),
            'page' => $request->page,
            'user' => $request->user,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'like' => new Like,
            'from' => $request->from
        ];

        return view('posts.show', $params);
    }

    // 投稿編集ページを表示
    public function edit($post_id, Request $request)
    {
        $params = [
            'user' => $request->user,
            'post' => Post::findOrFail($post_id),
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return view('posts.edit', $params);
    }

    // 投稿編集
    public function update($post_id, PostRequest $request)
    {
        // 更新するデータ
        $params = [
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
            'category' => $request->edit_category,
        ];

        $post = Post::findOrFail($post_id);

        // 「画像削除ボタン」が押されていたら、画像を削除して、新しい画像がセットされていたら、画像を差し替える
        if (!empty($request->del_img)) {
            Storage::disk('s3')->delete($post->img);
            $params['img'] = null;
        } elseif (!empty($request->file('img'))) {
            $params['img'] = Storage::disk('s3')->put('/', $request->file('img'), 'public');

            if (!empty($post->img)) {
                Storage::disk('s3')->delete($post->img);
            }
        }

        $post->fill($params)->save();

        // ページ遷移ようのパラメーター
        $params = [
            'post' => $post,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return redirect()->route('posts.show', $params);
    }

    // 投稿削除
    public function destroy($post_id, Request $request)
    {
        $post = Post::findOrFail($post_id);

        if (!empty($post->img)) {
            Storage::disk('s3')->delete($post->img);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->likes()->delete();
            $post->delete();
        });

        $params = [
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        // ユーザーぺージから投稿を削除した場合はユーザーページへ戻る
        if (strpos($request->from, 'user') !== false) {
            return redirect()->route('user_top', $params);
        }

        // 検索結果ページからの削除をした場合は検索結果ページへ戻る
        if (!empty($request->keyword) || !empty($request->category)) {
            return redirect()->route('search', $params);
        }

        // トップページからの削除をした場合はトップページへ戻る
        return redirect()->route('top', ['page' => (int) $request->page]);
    }

    // 投稿検索
    public function search(Request $request)
    {
        $post = new Post;
        $keyword = Helper::mbTrim($request['keyword']);
        $users = User::where('name', 'LIKE', "%{$keyword}%")->get();
        $posts = [];

        // カテゴリーだけ指定して、キーワードが未入力の場合の検索
        if (!empty($request['category']) && empty($keyword)) {
            $posts = $post->getPostsByCategory($request['category']);
        }

        // カテゴリーとキーワードが指定されている場合
        if (!empty($request['category']) && !empty($keyword)) {
            if ($request->do_name_search === '1' && $users->count()) {
                $posts = $post->getPostsByCategoryAndUser($request['category'], $users);
            } else {
                $posts = $post->getPostsByCategoryAndKeyword($request['category'], $keyword);
            }
        } elseif (!empty($keyword)) {
            if ($request->do_name_search === '1' && $users->count()) {
                $posts = $post->getPostsByUser($users);
            } else {
                $posts = $post->getPostsByKeyword($keyword);
            }
        }

        $params = [
            'posts' => $posts,
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'user' => $request->user,
            'page' => (int) $request->page,
            'keyword' => $keyword,
            'category' => $request['category'],
            'do_name_search' => $request->do_name_search,
            'like' => new Like,
            'categories' => Category::all(),
        ];

        return view('posts.find', $params);
    }

    // イイネ処理
    public function ajaxlike(Request $request)
    {
        $user_id = Auth::user()->id;
        $post = Post::findOrFail($request->post_id);
        $like = new Like;

        // イイネがすでにされているならイイネを外す。されてないならイイネを追加する
        if ($like->like_exist($user_id, $post->id)) {
            $like = Like::where('user_id', $user_id)->where('post_id', $post->id)->delete();
        } else {
            $like->user_id = $user_id;
            $like->post_id = $post->id;
            $like->save();
        }

        return response()->json(['postLikesCount' => $post->loadCount('likes')->likes_count]);
    }
}
