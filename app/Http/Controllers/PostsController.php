<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\lib\My_func;

class PostsController extends Controller
{
    // ログインしていなかったらログイン画面に飛ばす
    public function index()
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        // withでn+1問題を解決
        // paginate()での返り値はLengthAwarePaginatorオブジェクトらしい。
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('posts.index', ['posts' => $posts, 'user' => $user]);
    }

    public function create()
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        return view('posts.create', ['user' => $user]);
    }

    // ログインしていないユーザーには投稿させないような使用しなきゃダメかも
    // あるいはここでuser_idをnullにするとかそうすればその投稿はユーザーがいない投稿になる。でも、mysqlの絡むの設定を変更する必要がある。
    public function store(PostRequest $request)
    {
        $params = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
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

        // print_r($post->user);
        // echo  $post->user->name;

        return view('posts.show', ['post' => $post, 'page' => $request->page, 'user' => $user]);
    }

    public function edit($post_id)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $post = Post::findOrFail($post_id);

        return view('posts.edit', ['post' => $post, 'user' => $user]);
    }

    public function update($post_id, PostRequest $request)
    {
        $params = [
            'title' => $request->title,
            'body' => $request->body,
            'img' => $request->img,
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

        return redirect()->route('posts.show', ['post' => $post]);
    }

    // 投稿を削除するにはそれに紐づいているコメントも削除する必要がある。
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

        return redirect()->route('top', ['page' => $request->page]);
    }
}
