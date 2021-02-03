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

    // ユーザーページを表示 自分の投稿、自分がいいねした投稿、自分のコメントを確認できる。未読のコメントがあったら通知する
    public function index()
    {
        $auth = null;
        if (Auth::check()) {
            $auth = Auth::user();
        }
        $user = User::find($auth->id);
        $post = new Post;
        $read = new Read;
        $unread_post_ids = $read->getUnreadPostIds($user->posts()->get('id')->toArray());

        $params = [
            'user' => $auth,
            'posts' => $user->posts()->with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate($this->per_page),
            'loved_posts' => $post->getLikePosts(Like::where('user_id', $auth->id)->get('post_id')->toArray()),
            'comments' => Comment::where('user_id', $auth->id)->paginate($this->per_page),
            'unread_post_ids' => !empty($unread_post_ids) ? array_column($unread_post_ids, 'post_id') : [],
            'like' => new Like,
        ];

        return view('user.index', $params);
    }

    // 既読処理
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
