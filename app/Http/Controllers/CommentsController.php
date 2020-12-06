<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\lib\My_func;

class CommentsController extends Controller
{
    public function store(CommentRequest $request)
    {
        $params = [
            'post_id' => $request->post_id,
            'body' => $request->body,
        ];

        $post = Post::findOrFail($params['post_id']);
        // createメソッドを呼ぶことで、インスタンスの作成→属性の代入→データの保存を一気通貫でやってくれる
        // ただし、createメソッドを使う場合はモデルのプロパティにguarded(ブラックリスト)あるいはfillable（ホワイトリスト）を設定する必要がある。
        $post->comments()->create($params);

        return redirect()->route('posts.show', ['post' => $post]);
    }

    public function edit($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        return view('comments.edit', ['comment' => $comment, 'post' => $comment->getPost()]);
    }

    public function update($comment_id, CommentRequest $request)
    {
        $params = [
            'body' => $request->body,
        ];

        $comment = Comment::findOrFail($comment_id);

        $comment->fill($params)->save();

        return redirect()->route('posts.show', ['post' => $comment->getPost()]);
    }

    public function destroy($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        $comment->delete();

        return redirect()->route('posts.show', ['post' => $comment->getPost()]);
    }
}
