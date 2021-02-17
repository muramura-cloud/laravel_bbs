@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <br><br>
            <h2>アプリの説明</h2>
            <br>
            <p>大学の課題でわからない箇所を「<strong>投稿</strong>」してみんなと共有できます。</p>
            <p>課題についての意見やアドバイスなどは「<strong>コメント</strong>」で発言できます。</p>
            <p>ユーザー登録をすると、投稿に対して「<strong>いいね</strong>」が押せたり、自分の投稿・コメントに対して<strong>編集・削除</strong>出来るようになります。</p>
            <div class="mt-5">
                <a class="btn btn-secondary" href="{{ route('top') }}">戻る</a>
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
