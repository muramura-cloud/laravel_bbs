<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Like;
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
        $posts = Post::with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate(10);

        $params = [
            'posts' => $posts,
            'user' => $user,
            'page' => $posts->currentPage(),
            'like' => new Like,
        ];

        return view('posts.index', $params);
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

        $post = Post::withCount('likes')->findOrFail($post_id);

        $params = [
            'post' => $post,
            'page' => $request->page,
            'user' => $user,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'like' => new Like,
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
            'category' => $request->category,
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

        $params = [
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search
        ];

        if (!empty($request->keyword)) {
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

        // これってコメントとかwithで取得しないの？
        if (!empty($request['category'])) {
            if (!empty($keyword)) {
                if ($request->do_name_search === '1') {
                    $users = $user_query->where('name', 'LIKE', "%{$keyword}%")->get();

                    $posts = Post::withCount('likes')->where('category', $request['category'])
                        ->where(function ($query) use ($users) {
                            foreach ($users as $user) {
                                $query->orWhere('user_id', $user->id);
                            }
                        })->orderBy('created_at', 'desc')->paginate(10);
                } else {
                    $posts = Post::withCount('likes')->where('category', $request['category'])
                        ->where(function ($query) use ($keyword) {
                            $query
                                ->where('title', 'LIKE', "%{$keyword}%")
                                ->orWhere('body', 'LIKE', "%{$keyword}%");
                        })->orderBy('created_at', 'desc')->paginate(10);
                }
            } else {
                $posts = $post_query->withCount('likes')->where('category', $request['category'])->orderBy('created_at', 'desc')->paginate(10);
            }
        } elseif (!empty($keyword)) {
            if ($request->do_name_search === '1') {
                $users = $user_query->withCount('likes')->where('name', 'LIKE', "%{$keyword}%")->get();

                $posts = Post::withCount('likes')->where(function ($query) use ($users) {
                    foreach ($users as $user) {
                        $query->orWhere('user_id', $user->id);
                    }
                })->orderBy('created_at', 'desc')->paginate(10);
            } else {
                $posts = $post_query->withCount('likes')->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('body', 'LIKE', "%{$keyword}%")->orderBy('created_at', 'desc')->paginate(10);
            }
        }

        $params = [
            'posts' => $posts,
            'user' => $user,
            'page' => (int) $request->page,
            'keyword' => $keyword,
            'category' => $request['category'],
            'like' => new Like,
            'do_name_search' => $request->do_name_search,
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
