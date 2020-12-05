<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class CommentsController extends Controller
{
    public function store(Request $request)
    {
        $params = $request->validate([
            // exists:posts,idはpost_idががpostsテーブルのidカラムに存在するかどうかをチェック。            
            'post_id' => 'required|exists:posts,id',
            'body' => 'required|max:2000',
        ]);

        $post = Post::findOrFail($params['post_id']);
        // createメソッドを呼ぶことで、インスタンスの作成→属性の代入→データの保存を一気通貫でやってくれる
        // ただし、createメソッドを使う場合はモデルのプロパティにguarded(ブラックリスト)あるいはfillable（ホワイトリスト）を設定する必要がある。
        $post->comments()->create($params);

        return redirect()->route('posts.show', ['post' => $post]);
    }
}
