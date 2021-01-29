@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <div class="mb-4">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    投稿を新規作成する
                </a>
            </div>

            @foreach ($posts as $post)
            <div class="card mb-4">
                @if (!empty($post->img))
                <div class="text-center">
                    <img src="{{Storage::disk('s3')->url($post->img)}}" class="card-img-top post-img">
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
            @endforeach
            <div class="d-flex justify-content-center mb-5">
                {{ $posts->links() }}
            </div>
        </div>
        <div class="col-md-3">
            <br><br><br>
            @include('components.ranking',['posts' => $ranking_loved_posts])
            @include('components.categories',['categories' => $categories])
        </div>
    </div>
</div>
@endsection
