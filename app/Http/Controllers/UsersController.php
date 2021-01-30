<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Read;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class UsersController extends Controller
{
    private $per_page = 10;

    public function index()
    {
        $auth = null;
        if (Auth::check()) {
            $auth = Auth::user();
        }

        $user = User::find($auth->id);

        $posts = $user->posts()->with(['comments'])->withCount('likes')->orderBy('created_at', 'desc');

        $loved_post_ids = Like::where('user_id', $auth->id)->get('post_id')->toArray();
        $loved_posts = [];
        if (!empty($loved_post_ids)) {
            $loved_posts = Post::where(function ($post_query) use ($loved_post_ids) {
                foreach ($loved_post_ids as $id) {
                    $post_query->orWhere('id', $id);
                }
            })->with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate($this->per_page, ['*'], 'page', 1);
        }

        $comments = Comment::where('user_id', $auth->id)->paginate($this->per_page);

        // readsテーブルからこのユーザーの投稿したpost_idを含んでいるものを取得する。
        $post_ids = $user->posts()->get('id')->toArray();

        $unread_post_ids = [];
        if (!empty($post_ids)) {
            $post_ids = array_column($post_ids, 'id');
            $unread_post_ids = Read::where(function ($read_query) use ($post_ids) {
                foreach ($post_ids as $id) {
                    $read_query->orWhere('post_id', $id)->where('is_read', false);
                }
            })->get('post_id')->toArray();

            // Helper::dump($unread_post_ids);
            // exit;
        }

        $params = [
            'user' => $auth,
            'posts' => $posts->paginate($this->per_page),
            'loved_posts' => $loved_posts,
            'comments' => $comments,
            'like' => new Like,
            'unread_post_ids' => !empty($unread_post_ids) ? array_column($unread_post_ids, 'post_id') : [],
        ];

        return view('user.index', $params);
    }

    // コメントが削除された時とかも既読処理を考えたないとな。
    public function read(Request $request)
    {
        Read::where('post_id', $request['post_id'])->delete();

        $params = [
            '_token' => $request['_token'],
            'post_id' => $request['post_id'],
            'page' => $request['page'],
            'from' => $request['from'],
        ];

        return response()->json($params);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('top');
    }
}
