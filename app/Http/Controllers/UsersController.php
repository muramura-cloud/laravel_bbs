<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
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

        $posts = $user->posts()->with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate($this->per_page);

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

        $params = [
            'user' => $auth,
            'posts' => $posts,
            'loved_posts' => $loved_posts,
            'comments' => $comments,
            'like' => new Like,
        ];

        return view('user.index', $params);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('top');
    }
}
