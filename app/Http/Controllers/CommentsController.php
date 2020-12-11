<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\lib\My_func;

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
        // createメソッドを呼ぶことで、インスタンスの作成→属性の代入→データの保存を一気通貫でやってくれる
        // ただし、createメソッドを使う場合はモデルのプロパティにguarded(ブラックリスト)あるいはfillable（ホワイトリスト）を設定する必要がある。
        $post->comments()->create($params);

        return redirect()->route('posts.show', ['post' => $post, 'page' => $request->page, 'keyword' => $request->keyword]);
    }

    public function edit($comment_id, Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        // $userがnullだったらトップページにリダイレクトさせた用が良くない?

        $comment = Comment::findOrFail($comment_id);

        $params = [
            'comment' => $comment,
            'post' => $comment->getPost(),
            'user' => $user,
            'page' => $request->page,
            'keyword' => $request->keyword
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

        return redirect()->route('posts.show', ['post' => $comment->getPost(), 'page' => $request->page, 'keyword' => $request->keyword]);
    }

    public function destroy($comment_id, Request $request)
    {
        $comment = Comment::findOrFail($comment_id);

        $comment->delete();

        return redirect()->route('posts.show', ['post' => $comment->getPost(), 'page' => $request->page, 'keyword' => $request->keyword]);
    }
}
