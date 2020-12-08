<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class AdminController extends Controller
{
    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.index', ['posts' => $posts]);
    }

    public function destroy(Request $request)
    {
        $post_id = $request['post_id'];


        echo $post_id;
        // $request->input('post_id');
        echo 'hello';
        exit;
    }

    public function show()
    {
        return view('admin.top');
    }
    public function showLoginForm()
    {
        return view('admin.login_form');
    }

    public function login()
    {
    }

    public function logout()
    {
    }

    public function showUserList()
    {
    }
}
