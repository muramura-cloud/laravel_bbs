@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <div class="d-flex">
        <a class="btn btn-secondary mr-2" href="{{ route('admin_top') }}">戻る</a>
        <form class="form-inline my-2 my-lg-0" method="get" action="{{route('search')}}">
            @csrf

            <input id="keyword_input" class="form-control mr-sm-2" type="text" name="keyword" placeholder="検索ワード"
                aria-label="Search">
            <button id="comment_search_btn" class="btn btn-outline-success my-2 my-sm-0 mr-3" type="button">検索</button>
        </form>
    </div>
    <br>

    <table id="posts_table" class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col"><input type="button" class="btn btn-danger" style="width: 80px;" id="all_check_btn"
                        value="全選択"></th>
                <th scope="col">ID</th>
                <th scope="col">コメント本文</th>
                <th scope="col">日付</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody id="posts_tbody">
            @forelse ($comments as $comment)
            <tr>
                <th scope="row"><input type="checkbox" name="delete_checkbox" value="{{$comment->id}}"></th>
                <td>{{$comment->id}}</td>
                <td>{{$comment->body}}</td>
                <td>{{$comment->created_at}}</td>
                <td>
                    <form name="admin_delete_form" style="display: inline-block;" method="post"
                        action="/admin_comment_delete">
                        @csrf

                        <input type="hidden" name="comment_id" value="{{$comment->id}}">
                        <button name="admin_delete_btn" class="btn btn-danger">削除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td>コメントはありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <form id="admin_mult_delete_form" style="display: inline-block;" method="post" action="/admin_mult_delete">
        @csrf

        <input type="hidden" id="ids" name="post_ids" value="">
        <button id="mult_delete_btn" class="btn btn-danger">チェックした投稿を削除</button>
    </form>

    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('admin_top') }}">管理者トップへ</a>
    </div>
</div>

{{-- $posts->links()はpagination/tailwind.blade.phpを表示している。 --}}
<div id="pagination_btns" class="d-flex justify-content-center mb-5">
    {{ $comments->links() }}
</div>
{{-- 現在ページを --}}
<input type="hidden" id="current_page" value="{{$comments->currentPage()}}">
@endsection
