<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
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
        // createメソッドを呼ぶことで、インスタンスの作成→属性の代入→データの保存を一気通貫でやってくれる
        // ただし、createメソッドを使う場合はモデルのプロパティにguarded(ブラックリスト)あるいはfillable（ホワイトリスト）を設定する必要がある。
        $post->comments()->create($params);

        $params = [
            'post' => $post,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
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
        ];

        return redirect()->route('posts.show', $params);
    }

    public function destroy($comment_id, Request $request)
    {
        $comment = Comment::findOrFail($comment_id);

        $comment->delete();

        $params = [
            'post' => $comment->getPost(),
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
        ];

        return redirect()->route('posts.show', $params);
    }
}
