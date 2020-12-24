@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターの配列を作る。 --}}
{{-- あるいはjsでうまくパラメータをカスタマイズする方法もある。 --}}
@php
$url=parse_url(request()->fullUrl());
parse_str($url['query'], $params);
$params['post'] = $post->id;
$params['category'] = $category;
$params['target'] = 'posts';
print_r($params);
@endphp


@section('content')
<div class="container mt-4">
    <div class="border p-4">
        {{-- ここら辺もっと簡単にまとめられない？ --}}
        @if (!empty($post->img))
        <div class="d-flex">
            <div class="p-2 col-6">
                <h1 class="h5 mb-4"><strong>{{ $post->title }}</strong></h1>
                <p class="mb-5">{!! nl2br(e($post->body)) !!}</p>
                <p class="mb-2 badge">カテゴリー : {{$post->category ? $post->category:'無し'}}</p>
            </div>
            <div class="p-2 col-6">
                <a href="{{asset('storage/' . $post->img)}}"><img src="{{asset('storage/' . $post->img)}}"
                        class="img-fluid"></a>
            </div>
        </div>
        @else
        <h1 class="h5 mb-4"><strong>{{ $post->title }}</strong></h1>
        <p class="mb-5">{!! nl2br(e($post->body)) !!}</p>
        <p class="mb-2 badge">カテゴリー : {{$post->category ? $post->category:'無し'}}</p>
        @endif

        <div class="fav mb-5">
            @if($user && $like->like_exist($user->id,$post->id))
            <span class="favorite-marke">
                <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><strong
                        class="badge">イイね</strong><i class="fas fa-heart  likesIcon loved"></i></a>
                <span class="likesCount">{{$post->likes_count}}</span>
            </span>
            @elseif($user && !$like->like_exist($user->id,$post->id))
            <span class="favorite-marke">
                <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><strong
                        class="badge">イイね</strong><i class="fas fa-heart  likesIcon "></i></a>
                <span class="likesCount">{{$post->likes_count}}</span>
            </span>
            @else
            <span class="favorite-marke">
                <a href="" class="js_like_toggle" data-user="not_login" data-postid="{{ $post->id }}"><strong
                        class="badge">イイね</strong><i class="fas fa-heart  likesIcon "></i></a>
                <span class="likesCount">{{$post->likes_count}}</span>
            </span>
            @endif
        </div>

        <div class="mb-4 text-left">
            <a class="btn btn-outline-dark" href="{{ route('report_create',$params) }}">報告</a>
            @auth
            @if ($post->user_id === $user->id)
            <a class="btn btn-primary" href="{{ route('posts.edit', $params) }}">編集</a>
            <form style="display: inline-block;" method="POST"
                action="{{ route('posts.destroy', ['post' => $post->id]) }}">
                <input type="hidden" name="page" value="{{$page}}">
                <input type="hidden" name="keyword" value="{{$keyword}}">
                <input type="hidden" name="category" value="{{$category}}">
                <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger">削除</button>
            </form>
            @endif
            @endauth
        </div>

        <form action="{{ route('comments.store') }}" method="post">
            @csrf

            <input type="hidden" name="post_id" value="{{$post->id}}">
            <input type="hidden" name="page" value="{{$page}}">
            <input type="hidden" name="keyword" value="{{$keyword}}">
            <input type="hidden" name="category" value="{{$category}}">
            <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
            <div class="form-group">
                <label for="body"><strong>本文(必須)</strong></label>
                <textarea name="body" class="form-control {{$errors->has('body') ? 'is-invalid' : ''}}" rows="4">{{old('body')}}
                </textarea>
                @if ($errors->has('body'))
                <div class="invalid-feedback">
                    {{ $errors->first('body') }}
                </div>
                @endif
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">コメントする</button>
            </div>
        </form>
        <br>

        <section>
            <h2 class="h5 mb-4"><strong>コメント</strong></h2>

            @forelse($post->comments as $comment)
            <div class="border-top p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <time
                            class="text-secondary">{{ $comment->created_at ? $comment->created_at->format('Y.m.d H:i') : '' }}</time>
                        <span class="badge">{{$comment->user ? $comment->user->name : ''}}</span>
                        <p class="mt-2">{{$comment->body}}</p>
                    </div>
                    <a id="comment_report_btn" style="height: 30px;" class="btn btn-outline-dark text-right"
                        href="{{ route('report_create',$params) }}" value="{{$comment->id}}"><small>報告</small></a>
                </div>
                @auth
                @if ($comment->user_id === $user->id)
                <div class="mb-4 text-right">
                    {{-- もうちょっとここら辺スッキリさせたいな。 --}}
                    <a class="btn btn-primary p-1"
                        href="{{ route('comments.edit', ['comment' => $comment->id,'page'=>$page,'keyword'=>$keyword,'category'=>$category ,'do_name_search'=>$do_name_search]) }}"><small>編集</small></a>
                    <form style="display: inline-block;" method="POST"
                        action="{{ route('comments.destroy', ['comment' => $comment->id]) }}">
                        <input type="hidden" name="page" value="{{$page}}">
                        <input type="hidden" name="keyword" value="{{$keyword}}">
                        <input type="hidden" name="category" value="{{$category}}">
                        <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger p-1"><small>削除</small></button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse
        </section>
    </div>

    @if (!empty($keyword || !empty($params['category'])))
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('search',$params) }}">戻る</a>
    </div>
    @else
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('top',['page'=>$page]) }}">戻る</a>
    </div>
    @endif
    <br>
</div>
@endsection
