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
    public function index()
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $posts = Post::with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate(10);

        $params = [
            'posts' => $posts,
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'user' => $user,
            'page' => $posts->currentPage(),
            'like' => new Like,
            'categories' => Category::all(),
        ];

        return view('posts.index', $params);
    }

    public function introduce()
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $params = [
            'user' => $user,
            'categories' => Category::all(),
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
        ];

        return view('posts.introduce', $params);
    }

    public function create(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        return view('posts.create', ['user' => $user, 'page' => $request->page]);
    }

    public function store(PostRequest $request)
    {
        $params = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
            'category' => $request->category,
        ];

        if (!empty($request->file('img'))) {
            $path = Storage::disk('s3')->put('/', $request->file('img'), 'public');
            $params['img'] = $path;
        }

        Post::create($params);

        return redirect()->route('top');
    }

    public function show($post_id, Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();

            // 既読処理
            if (Post::findOrFail($post_id)->user_id === $user->id) {
                Read::where('post_id', $post_id)->delete();
            }
        }

        $post = Post::withCount('likes')->findOrFail($post_id);

        $params = [
            'post' => $post,
            'page' => $request->page,
            'user' => $user,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'like' => new Like,
            'from' => $request->from
        ];

        return view('posts.show', $params);
    }

    public function edit($post_id, Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $post = Post::findOrFail($post_id);

        $params = [
            'post' => $post,
            'page' => $request->page,
            'user' => $user,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return view('posts.edit', $params);
    }

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

        // ルーティングデータ
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

        if (strpos($request->from, 'user') !== false) {
            return redirect()->route('user_top', $params);
        }

        // キーワードが送られてきたり、カテゴリーが送られてきたら再度検索して表示する。
        if (!empty($request->keyword) || !empty($request->category)) {
            return redirect()->route('search', $params);
        }

        return redirect()->route('top', ['page' => (int) $request->page]);
    }

    public function search(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $post_query = Post::query();
        $user_query = User::query();

        $keyword = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request['keyword']);

        // 空検索の場合
        $posts = [];

        if (!empty($request['category'])) {
            if (!empty($keyword)) {
                if ($request->do_name_search === '1') {
                    $users = $user_query->where('name', 'LIKE', "%{$keyword}%")->get();

                    if ($users->count()) {
                        $posts = Post::with(['comments'])->withCount('likes')->where('category', $request['category'])
                            ->where(function ($query) use ($users) {
                                foreach ($users as $user) {
                                    $query->orWhere('user_id', $user->id);
                                }
                            })->orderBy('created_at', 'desc')->paginate(10);
                    }
                } else {
                    $posts = Post::with(['comments'])->withCount('likes')->where('category', $request['category'])
                        ->where(function ($query) use ($keyword) {
                            $query
                                ->where('title', 'LIKE', "%{$keyword}%")
                                ->orWhere('body', 'LIKE', "%{$keyword}%");
                        })->orderBy('created_at', 'desc')->paginate(10);
                }
            } else {
                $posts = $post_query->with(['comments'])->withCount('likes')->where('category', $request['category'])->orderBy('created_at', 'desc')->paginate(10);
            }
        } elseif (!empty($keyword)) {
            if ($request->do_name_search === '1') {
                $users = $user_query->where('name', 'LIKE', "%{$keyword}%")->get();

                if ($users->count()) {
                    $posts = Post::with(['comments'])->withCount('likes')->where(function ($query) use ($users) {
                        foreach ($users as $user) {
                            $query->orWhere('user_id', $user->id);
                        }
                    })->orderBy('created_at', 'desc')->paginate(10);
                }
            } else {
                $posts = $post_query->with(['comments'])->withCount('likes')->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('body', 'LIKE', "%{$keyword}%")->orderBy('created_at', 'desc')->paginate(10);
            }
        }

        $params = [
            'posts' => $posts,
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'user' => $user,
            'page' => (int) $request->page,
            'keyword' => $keyword,
            'category' => $request['category'],
            'like' => new Like,
            'do_name_search' => $request->do_name_search,
            'categories' => Category::all(),
        ];

        return view('posts.find', $params);
    }

    public function ajaxlike(Request $request)
    {
        $user_id = Auth::user()->id;
        $like = new Like;
        $post = Post::findOrFail($request->post_id);

        if ($like->like_exist($user_id, $post->id)) {
            $like = Like::where('user_id', $user_id)->where('post_id', $post->id)->delete();
        } else {
            $like->user_id = $user_id;
            $like->post_id = $post->id;
            $like->save();
        }

        $post_likes_count = $post->loadCount('likes')->likes_count;

        return response()->json(['postLikesCount' => $post_likes_count]);
    }
}
