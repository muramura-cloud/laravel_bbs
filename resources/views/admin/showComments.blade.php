@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <table class="table table-bordered">
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
        {{-- これ名前tbodyの方がよくね？ --}}
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
    <form id="admin_mult_delete_form" style="display: inline-block;" method="post" action="/admin_mult_comment_delete">
        @csrf

        <input type="hidden" id="ids" name="comment_ids" value="">
        <input type="hidden" name="post_id" value="{{$post->id}}">
        <button id="mult_delete_btn" class="btn btn-danger">チェックしたコメントを削除</button>
    </form>
    <div class="mt-5 d-flex" id="back_btns">
        <a id="back_search_result_btn" class="btn btn-secondary mr-2">戻る</a>
        <a class="btn btn-secondary" href="{{ route('admin_top') }}">管理者トップへ戻る</a>
    </div>
</div>
<div id="pagination_btns" class="d-flex justify-content-center mb-5">
    {{ $comments->links() }}
</div>
{{-- 現在ページ--}}
<input type="hidden" id="current_page" value="{{$comments->currentPage()}}">
@endsection

<script>
    window.onload = function(){
        if(location.search) {
            document.getElementById("back_search_result_btn").setAttribute('href',`/admin/search/${location.search}&ajax=false`)
        }else {
            document.getElementById("back_search_result_btn").remove();
        }
    };
</script>
