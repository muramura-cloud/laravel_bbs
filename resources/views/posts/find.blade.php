@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターを作る。 --}}
@php
$url = parse_url(request()->fullUrl());
parse_str($url['query'], $params);
@endphp

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <h2>検索結果</h2><br>
            @forelse ($posts as $post)
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
                            <input type="hidden" name="keyword" value="{{$keyword}}">
                            <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
                            <input type="hidden" name="category" value="{{$category}}">
                            <button class="btn btn-success">続きを読む</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    @include('components.post_footer',['post' => $post])
                    @include('components.like',['post' => $post, 'user' => request()->user, 'like' => request()->like])
                </div>
            </div>
            @empty
            <p>検索に一致する投稿はありません。</p>
            @endforelse

            @if (!empty($posts))
            <div class="d-flex justify-content-center mb-5">
                {{ $posts->appends(['category'=>$category,'keyword'=>$keyword,'do_name_search'=>$do_name_search])->links() }}
            </div>
            @endif
            <div class="my-5">
                <a class="btn btn-secondary" href="{{ route('top',['page'=>$page]) }}">戻る</a>
            </div>
        </div>
        <div class="col-md-3">
            <br><br><br>
            @include('components.ranking',['posts'=>request()->ranking_loved_posts])
            @include('components.categories',['categories' => request()->categories])
        </div>
    </div>
</div>
@endsection
