@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターを作る。 --}}
@php
$url = parse_url(request()->fullUrl());
parse_str($url['query'], $params);
@endphp

@section('content')

<div class="container mt-4">
    <h2>検索結果</h2><br>

    @forelse ($posts as $post)
    <div class="card mb-4">
        @if (!empty($post->img))
        <div class="text-center">
            <img src="{{asset('storage/' . $post->img)}}" class="card-img-top" style="width:400px; height:300px">
        </div>
        @endif
        <div class="card-body">
            <h4 class="card-title">{{$post->title}}</h4>
            <p class="card-text">{{$post->body}}</p>
            <div class="mb-1">
                {{-- ['post'=>$post->id]のpostは勝手に変えちゃダメっぽい。多分モデルの名前かもしれない。 --}}
                <form style="display: inline-block;" method="get"
                    action="{{ route('posts.show', ['post' => $post->id]) }}">
                    @csrf

                    <input type="hidden" name="page" value="{{$posts->currentPage()}}">
                    <input type="hidden" name="keyword" value="{{$keyword}}">
                    <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
                    <input type="hidden" name="category" value="{{$category}}">
                    <button class="btn btn-success">続きを読む</button>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
            <span
                class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
            <span class="ml-2 badge badge-info">{{$post->category ? 'カテゴリー :'. $post->category : ''}}</span>
            <span class="ml-2 badge">{{$post->user ? ' 投稿者 : ' . $post->user->name : ''}}</span>
        </div>
    </div>
    @empty
    <p>検索に一致する投稿はありません。</p>
    @endforelse

    @if (!empty($posts))
    <div class="d-flex justify-content-center mb-5">
        {{ $posts->links() }}
    </div>
    @endif
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('top',['page'=>$page]) }}">戻る</a>
    </div>
    <br>
</div>
@endsection
