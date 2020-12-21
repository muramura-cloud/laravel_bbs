<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class PostsController extends Controller
{
    public function index()
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        // withでn+1問題を解決
        // paginate()での返り値はLengthAwarePaginatorオブジェクトらしい。
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('posts.index', ['posts' => $posts, 'user' => $user, 'page' => $posts->currentPage()]);
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
            $path = $request->file('img')->store('');
            $params['img'] = basename($path);
        }

        Post::create($params);

        return redirect()->route('top');
    }

    public function show($post_id, Request $request)
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
            'do_name_search' => $request->do_name_search
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
            'do_name_search' => $request->do_name_search
        ];

        return view('posts.edit', $params);
    }

    public function update($post_id, PostRequest $request)
    {
        $params = [
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
            'category' => $request->category,
        ];

        $post = Post::findOrFail($post_id);

        if (!empty($request->del_img)) {
            Storage::delete($post->img);
            $params['img'] = null;
        } elseif (!empty($request->file('img'))) {
            $path = $request->file('img')->store('');
            $params['img'] = basename($path);

            if (!empty($post->img)) {
                Storage::delete($post->img);
            }
        }

        $post->fill($params)->save();

        $params = [
            'post' => $post,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'do_name_search' => $request->do_name_search
        ];

        return redirect()->route('posts.show', $params);
    }

    public function destroy($post_id, Request $request)
    {
        $post = Post::findOrFail($post_id);

        if (!empty($post->img)) {
            Storage::delete($post->img);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->delete();
        });

        if (!empty($request->keyword)) {
            return redirect()->route('search', ['page' => (int) $request->page, 'keyword' => $request->keyword]);
        }

        return redirect()->route('top', ['page' => $request->page]);
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

        $posts = [];

        if (!empty($keyword)) {
            if ($request->do_name_search === '1') {
                $users = $user_query->where('name', 'LIKE', "%{$keyword}%")->get();

                foreach ($users as $user) {
                    $post_query->where('user_id', $user->id)->get();
                }

                $posts = $post_query->orderBy('created_at', 'desc')->paginate(10);
            } else {
                $post_query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('body', 'LIKE', "%{$keyword}%")->get();

                $posts = $post_query->orderBy('created_at', 'desc')->paginate(10);
            }
        }

        $params = [
            'posts' => $posts,
            'user' => $user,
            'page' => (int) $request->page,
            'keyword' => $keyword,
            'do_name_search' => $request->do_name_search,
        ];

        return view('posts.find', $params);
    }
}
