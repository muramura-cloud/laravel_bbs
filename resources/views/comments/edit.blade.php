@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターを作る。 --}}
@php
$url=parse_url(request()->fullUrl());
parse_str($url['query'], $params);
$params['post']=$post->id;
print_r($params);
@endphp

@section('content')
<div class="container">
    <div class="border p-4">
        <h1 class="h5 mb-4">コメントの編集</h1>

        {{-- ここ$params使えないの？ --}}
        <form action="{{route('comments.update',['comment'=>$comment->id])}}" method="post">
            <input type="hidden" name="post_id" value="{{$comment->post_id}}">
            <input type="hidden" name="page" value="{{$page}}">
            <input type="hidden" name="keyword" value="{{$keyword}}">
            <input type="hidden" name="do_name_search" value="{{$do_name_search}}">
            @csrf
            @method('put')

            <fieldset>
                <div class="form-group">
                    <label for="body"><strong>本文</strong>(必須)</label>
                    <textarea id="body" name="body" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"
                        rows="4">{{ old('body') ?: $comment->body }}</textarea>
                    @if ($errors->has('body'))
                    <div class="invalid-feedback">
                        {{ $errors->first('body') }}
                    </div>
                    @endif
                </div>

                <div class="mt-5">
                    <a class="btn btn-secondary" href="{{ route('posts.show', $params) }}">キャンセル</a>
                    <button type="submit" class="btn btn-primary">更新する</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection
