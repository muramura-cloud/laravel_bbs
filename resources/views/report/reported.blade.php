@extends('layouts.layout')

@php
$url=parse_url(request()->fullUrl());
parse_str($url['query'], $params);
$params['category'] = $category;
$params['page'] = (int) $page;
$params['post'] = (int) $post_id;
$params['keyword'] = (int) $keyword;
$params['from'] = $from;
@endphp

@section('content')

<div class="container mt-4">
    <h1 class="mb-4">報告しました。</h1>
    <p>ありがとうございます。</p>
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('posts.show',$params) }}">戻る</a>
    </div>
</div>

@endsection
