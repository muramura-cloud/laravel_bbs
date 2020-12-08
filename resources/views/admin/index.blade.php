@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <form action="/admin" method="post">
        @csrf

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">全✅<input type="checkbox" name="" value=""></th>
                    <th scope="col">ID</th>
                    <th scope="col">タイトル</th>
                    <th scope="col">本文</th>
                    <th scope="col">画像</th>
                    <th scope="col">コメント</th>
                    <th scope="col">日付</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                <tr>
                    <th scope="row"><input type="checkbox" name="" value=""></th>
                    <td>{{$post->id}}</td>
                    <td>{{$post->title}}</td>
                    <td>{{$post->body}}</td>
                    <td>
                        @if (!empty($post->img))
                        <a href="{{asset('storage/' . $post->img)}}"><img src="{{asset('storage/' . $post->img)}}"
                                style="width: 40px; height: 30px;"></a>
                        @else
                        画像なし
                        @endif
                    </td>
                    <td>
                        @if ($post->comments->count())
                        <table class="table table-bordered">
                            <tr>
                                <th class="thead-light">
                                    コメント一覧
                                </th>
                            </tr>
                            @foreach ($post->comments as $comment)
                            <tr>
                                <td>{{$comment->body}}</td>
                            </tr>
                            @endforeach
                        </table>
                        @else
                        コメントなし
                        @endif
                    </td>
                    <td>{{$post->created_at}}</td>
                    <td>
                        <form style="display: inline-block;" method="post" action="/admin">
                            @csrf

                            <input type="hidden" name="post_id" value="{{$post->id}}">
                            <button class="btn btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-danger">チェックした投稿を削除</button>
    </form>
</div>
<div class="d-flex justify-content-center mb-5">
    {{ $posts->links() }}
</div>
@endsection
