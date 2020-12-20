@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <div class="d-flex">
        <button type="button" class="btn btn-outline-success mr-2" data-toggle="modal"
            data-target="#exampleModalCenter">投稿を検索する</button>
        <a href="{{route('admin_comment_list')}}" class="btn btn-outline-info">コメント一覧へ</a>
    </div>
    <br>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="form-inline my-2 my-lg-0" method="get" action="{{route('search')}}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">検索条件を指定</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <fieldset class="mb-4">
                            <div class="form-group mb-4 d-flex justify-content-between">
                                <label for="keyword_title"><strong>タイトル</strong></label>
                                <input id="keyword_title" class="form-control mr-sm-2" type="text" name="keyword_title"
                                    placeholder="タイトル" aria-label="Search">
                            </div>
                            <div class="form-group mb-4 d-flex justify-content-between">
                                <label for="keyword_body"><strong>本文</strong></label>
                                <input id="keyword_body" class="form-control mr-sm-2" type="text" name="keyword_body"
                                    placeholder="本文" aria-label="Search">
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                        <button id="admin_search_btn" class="btn btn-outline-success" type="button">検索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table id="posts_table" class="table table-bordered">
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
        <tbody id="posts_tbody">
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
                    <a href="/admin_comment/{{$post->id}}" name="show_comments_btn" class="btn">コメント一覧へ</a>
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

        <input type="hidden" id="ids" name="post_ids" value="">
        <button id="mult_delete_btn" class="btn btn-danger">チェックした投稿を削除</button>
    </form>

    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('admin_top') }}">管理者トップへ</a>
    </div>
</div>

{{-- $posts->links()はpagination/tailwind.blade.phpを表示している。 --}}
<div id="pagination_btns" class="d-flex justify-content-center mb-5">
    {{ $posts->links() }}
</div>
{{-- 現在ページを --}}
<input type="hidden" id="current_page" value="{{$posts->currentPage()}}">
@endsection
