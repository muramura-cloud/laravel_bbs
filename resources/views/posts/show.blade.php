@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターの配列を作る。 --}}
@php
$url=parse_url(request()->fullUrl());
parse_str($url['query'], $params);
$params['post']=$post->id;
@endphp


@section('content')
<div class="container mt-4">
    <div class="border p-4">
        @if (!empty($post->img))
        <div class="d-flex">
            <div class="p-2 col-6">
                <h1 class="h5 mb-4">{{ $post->title }}</h1>
                <p class="mb-5">{{$post->body}}</p>
                <p class="mb-5 badge">カテゴリー : {{$post->category ? $post->category:'無し'}}</p>
            </div>
            <div class="p-2 col-6">
                <a href="{{asset('storage/' . $post->img)}}"><img src="{{asset('storage/' . $post->img)}}"
                        class="img-fluid"></a>
            </div>
        </div>
        @else
        <h1 class="h5 mb-4">{{ $post->title }}</h1>
        <p class="mb-5">{{$post->body}}</p>
        <p class="mb-5 badge">カテゴリー : {{$post->category ? $post->category:'無し'}}</p>
        @endif

        @auth
        @if ($post->user_id === $user->id)
        <div class="mb-4 text-left">
            <a class="btn btn-primary" href="{{ route('posts.edit', $params) }}">編集</a>
            <form style="display: inline-block;" method="POST"
                action="{{ route('posts.destroy', ['post' => $post->id]) }}">
                <input type="hidden" name="page" value="{{$page}}">
                <input type="hidden" name="keyword" value="{{$keyword}}">
                <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger">削除</button>
            </form>
        </div>
        @endif
        @endauth

        <form action="{{ route('comments.store') }}" method="post">
            @csrf

            <input type="hidden" name="post_id" value="{{$post->id}}">
            <input type="hidden" name="page" value="{{$page}}">
            <input type="hidden" name="keyword" value="{{$keyword}}">
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
            <h2 class="h5 mb-4">コメント</h2>

            {{-- モデルでリレーションを設定しているからcommentsプロパティを使用できる。 --}}
            @forelse($post->comments as $comment)
            <div class="border-top p-4">
                <time class="text-secondary">{{ $comment->created_at ? $comment->created_at->format('Y.m.d H:i') : '' }}
                    {{$comment->user ? ' コメント主 : ' . $comment->user->name : ''}}</time>
                <p class="mt-2">{{$comment->body}}</p>
                @auth
                @if ($comment->user_id === $user->id)
                <div class="mb-4 text-right">
                    {{-- もうちょっとここら辺スッキリさせたいな。 --}}
                    <a class="btn btn-primary p-1"
                        href="{{ route('comments.edit', ['comment' => $comment->id,'page'=>$page,'keyword'=>$keyword,'do_name_search'=>$do_name_search]) }}"><small>編集</small></a>
                    <form style="display: inline-block;" method="POST"
                        action="{{ route('comments.destroy', ['comment' => $comment->id]) }}">
                        <input type="hidden" name="page" value="{{$page}}">
                        <input type="hidden" name="keyword" value="{{$keyword}}">
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

    {{-- ここの条件分岐もいらないかもな --}}
    @if (!empty($keyword))
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('search',$params) }}">戻る</a>
        {{-- <a class="btn btn-secondary" href="{{ route('search',['page'=>$page,'keyword'=>$keyword]) }}">戻る</a> --}}
    </div>
    @else
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('top',['page'=>$page]) }}">戻る</a>
    </div>
    @endif
</div>
@endsection
