@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-9">
            <h2>アプリの説明</h2>
            <br>
            <p>大学の課題でわからない箇所を「<strong>投稿</strong>」してみんなと共有しよう。</p>
            <p>課題についての意見やアドバイスなどは「<strong>コメント</strong>」で教えてあげよう。</p>
            <p>ユーザー登録をすると、投稿に対して「<strong>いいね</strong>」が押せたり、自分の投稿・コメントに対して<strong>編集・削除</strong>出来るようになります。</p>
            <div class="mt-5">
                <a class="btn btn-secondary" href="{{ route('top') }}">戻る</a>
            </div>
        </div>
        <div class="col-3">
            <br><br><br>
            @include('components.ranking',['posts'=>$ranking_loved_posts])
            @include('components.categories',['categories' => $categories])
        </div>
    </div>
</div>

@endsection
