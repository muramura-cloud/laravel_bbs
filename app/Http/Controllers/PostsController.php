<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Storage;
use App\lib\My_func;

class PostsController extends Controller
{
    public function index()
    {
        // withでn+1問題を解決
        // paginate()での返り値はLengthAwarePaginatorオブジェクトらしい。
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(10);

        return view('posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(PostRequest $request)
    {
        $params = [
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
        $post = Post::findOrFail($post_id);

        return view('posts.show', ['post' => $post, 'page' => $request->page]);
    }

    public function edit($post_id)
    {
        $post = Post::findOrFail($post_id);

        return    view('posts.edit', ['post' => $post]);
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

        // return redirect()->route('top');
        return redirect()->route('top', ['page' => $request->page]);
    }

    public function back()
    {
        return back();
    }
}
