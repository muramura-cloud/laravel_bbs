@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    {{-- formをformで括ると大変なことになる。jsでform要素を全て取得できなかった。 --}}
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col"><input class="btn btn-danger" type="button" style="width: 80px;" id="all_check_btn"
                        value="全選択"></th>
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
                <th scope="row"><input type="checkbox" name="delete_checkbox" value="{{$post->id}}"></th>
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
                    <a href="/admin_comment/{{$post->id}}" class="btn">コメント一覧へ</a>
                    @else
                    コメント無し
                    @endif
                </td>
                <td>{{$post->created_at}}</td>
                <td>
                    <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                        @csrf

                        <input type="hidden" name="post_id" value="{{$post->id}}">
                        <button name="admin_delete_btn" class="btn btn-danger">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <form id="admin_mult_delete_form" style="display: inline-block;" method="post" action="/admin_mult_delete">
        @csrf

        <input type="hidden" id="ids" name="post_ids[]" value="">
        <button id="mult_delete_btn" class="btn btn-danger">チェックした投稿を削除</button>
    </form>
</div>
<div class="d-flex justify-content-center mb-5">
    {{ $posts->links() }}
</div>
{{-- sweetalert.jsライブラリ --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@endsection
