<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $posts = Post::with(['comments'])->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.index', ['posts' => $posts]);
    }

    public function destroy(Request $request)
    {
        $post = Post::findOrFail($request['post_id']);

        if (!empty($post->img)) {
            Storage::delete($post->img);
        }

        DB::transaction(function () use ($post) {
            $post->comments()->delete();
            $post->delete();
        });


        return redirect()->route('admin_top');
    }

    public function multDestroy(Request $request)
    {
        $post_ids = explode(",", $request['post_ids']);

        if (!empty($post_ids)) {
            foreach ($post_ids as $post_id) {
                $post = Post::findOrFail((int) $post_id);

                if (!empty($post->img)) {
                    Storage::delete($post->img);
                }

                DB::transaction(function () use ($post) {
                    $post->comments()->delete();
                    $post->delete();
                });
            }
        }

        return redirect()->route('admin_top');
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
