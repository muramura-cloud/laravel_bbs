<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Read;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function store(CommentRequest $request)
    {
        $params = [
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ];

        $post = Post::findOrFail($params['post_id']);
        $post->comments()->create($params);

        // コメントされた投稿を未読テーブルに追加
        if (!empty($post->user_id)) {
            if (empty(Auth::id()) || Auth::id() !== $post->user->id) {
                Read::create([
                    'post_id' => $request->post_id,
                    'comment_id' => Comment::all()->last()->id,
                    'is_read' => 0,
                ]);
            }
        }

        $params = [
            'post' => $post,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from,
        ];

        return redirect()->route('posts.show', $params);
    }

    public function edit($comment_id, Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $comment = Comment::findOrFail($comment_id);

        $params = [
            'comment' => $comment,
            'post' => $comment->getPost(),
            'user' => $user,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from,
        ];

        return view('comments.edit', $params);
    }

    public function update($comment_id, CommentRequest $request)
    {
        $comment = Comment::findOrFail($comment_id);

        $params = [
            'body' => $request->body,
        ];

        $comment->fill($params)->save();

        $params = [
            'post' => $comment->getPost(),
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from,
        ];

        return redirect()->route('posts.show', $params);
    }

    public function destroy($comment_id, Request $request)
    {
        $comment = Comment::findOrFail($comment_id);

        $comment->delete();

        Read::where('comment_id', $comment_id)->delete();

        $params = [
            'post' => $comment->getPost(),
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from,
        ];

        return redirect()->route('posts.show', $params);
    }
}
