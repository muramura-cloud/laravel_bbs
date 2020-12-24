@extends('layouts.user')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">ユーザー情報</h2>
    <table class="table">
        <tbody>
            <tr>
                <th>名前</th>
                <td>{{$user->name}}</td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>{{$user->email}}</td>
            </tr>
            <tr>
                <th>登録日</th>
                <td>{{$user->created_at}}</td>
            </tr>
        </tbody>
    </table>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#my_posts" class="nav-link active" data-toggle='tab'>自分の投稿</a>
        </li>
        <li class="nav-item">
            <a href="#my_loved_posts" class="nav-link" data-toggle='tab'>イイねした投稿</a>
        </li>
        <li class="nav-item">
            <a href="#my_comments" class="nav-link" data-toggle='tab'>自分のコメント</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="my_posts" class="tab-pane active">
            @forelse ($posts as $post)
            <div class="card mb-4">
                @if (!empty($post->img))
                <div class="text-center">
                    <img src="{{asset('storage/' . $post->img)}}" class="card-img-top"
                        style="width:400px; height:300px">
                </div>
                @endif
                <div class="card-body">
                    <h4 class="card-title">{{$post->title}}</h4>
                    <p class="card-text post-body">{{$post->body}}</p>
                    <div class="mb-1">
                        <form style="display: inline-block;" method="get"
                            action="{{ route('posts.show', ['post' => $post->id]) }}">
                            @csrf

                            <input type="hidden" name="page" value="{{$posts->currentPage()}}">
                            <button class="btn btn-success">続きを読む</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
                        <span
                            class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
                        <span class="ml-2 badge badge-info">{{$post->category ? $post->category : ''}}</span>
                    </div>
                    <div class="fav">
                        @if($user && $like->like_exist($user->id,$post->id))
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon loved"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @elseif($user && !$like->like_exist($user->id,$post->id))
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @else
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="not_login" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p>投稿はまだありません。</p>
            @endforelse
        </div>
        <div id="my_loved_posts" class="tab-pane">
            @forelse ($loved_posts as $post)
            <div class="card mb-4">
                @if (!empty($post->img))
                <div class="text-center">
                    <img src="{{asset('storage/' . $post->img)}}" class="card-img-top"
                        style="width:400px; height:300px">
                </div>
                @endif
                <div class="card-body">
                    <h4 class="card-title">{{$post->title}}</h4>
                    <p class="card-text post-body">{{$post->body}}</p>
                    <div class="mb-1">
                        <form style="display: inline-block;" method="get"
                            action="{{ route('posts.show', ['post' => $post->id]) }}">
                            @csrf

                            <input type="hidden" name="page" value="{{$posts->currentPage()}}">
                            <button class="btn btn-success">続きを読む</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
                        <span
                            class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
                        <span class="ml-2 badge badge-info">{{$post->category ? $post->category : ''}}</span>
                        <span class="ml-2 badge">{{$post->user ? ' 投稿者 : ' . $post->user->name : ''}}</span>
                    </div>
                    <div class="fav">
                        @if($user && $like->like_exist($user->id,$post->id))
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon loved"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @elseif($user && !$like->like_exist($user->id,$post->id))
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @else
                        <span class="favorite-marke">
                            <a href="" class="js_like_toggle" data-user="not_login" data-postid="{{ $post->id }}"><i
                                    class="fas fa-heart likesIcon"></i></a>
                            <span class="likesCount">{{$post->likes_count}}</span>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <br>
            <p>イイねした投稿はありません。</p>
            @endforelse
        </div>
        <div id="my_comments" class="tab-pane">
            @forelse($comments as $comment)
            <div class="card mb-4">
                <div class="card-body">
                    <time
                        class="text-secondary">{{ $comment->created_at ? $comment->created_at->format('Y.m.d H:i') : '' }}</time>
                    <p class="mt-2">{{$comment->body}}</p>
                </div>
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
