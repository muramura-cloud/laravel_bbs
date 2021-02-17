@extends('layouts.layout')

{{-- 本当はヘルパークラス（Helpers/Helper.php）をパラメーター生成で使いたいけど、なぜか、独自ヘルパークラスがうまく読み込めなくて、仕方なくここでパラメーターを作る。 --}}
@php
$url=parse_url(request()->fullUrl());
parse_str($url['query'], $params);
$params['post']=$post->id;
$params['category']=$category;
$params['from']=$from;
@endphp

@section('content')
<div class="container">
    <div class="border p-4">
        <h1 class="h5 mb-4">投稿の編集</h1>

        <form action="{{route('posts.update',$params)}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')

            <fieldset>
                <div class="form-group">
                    <label for="title"><strong>タイトル</strong>(必須)</label>
                    <input id="title" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                        value="{{ old('title') ?: $post->title }}" type="text">
                    @if ($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="body"><strong>本文</strong>(必須)</label>
                    <textarea id="body" name="body" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"
                        rows="4">{{ old('body') ?: $post->body }}</textarea>
                    @if ($errors->has('body'))
                    <div class="invalid-feedback">
                        {{ $errors->first('body') }}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="edit_category"><strong>カテゴリー</strong>(任意)</label><br>
                    <select class="form-control" id="edit_category" name="edit_category"
                        data-category="{{$post->category}}">
                        @include('components.categories_selectbox',['categories'=>request()->categories])
                    </select>
                </div>

                <div class="d-flex">
                    <div class="form-group col-6">
                        <label for="img"><strong>画像</strong>(任意)</label><br>
                        <input type="file" name="img" id="img"
                            class="form-control {{ $errors->has('img') ? 'is-invalid' : '' }}">
                        @if ($errors->has('img'))
                        <div class="invalid-feedback">
                            {{ $errors->first('img') }}
                        </div>
                        @endif
                    </div>
                    @if (!empty($post->img))
                    <div class="p-2 col-6">
                        <a href="{{Storage::disk('s3')->url($post->img)}}"><img
                                src="{{Storage::disk('s3')->url($post->img)}}" class="img-fluid"></a>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="del_img" value="1" class="custom-control-input" id="del_img">
                            <label class="custom-control-label" for="del_img">画像を削除</label>
                        </div>
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

<script>
    window.onload = function(){
        let category = document.getElementById('edit_category');
        if (category.dataset.category) {
            for (let i = 0; i < category.options.length; i++) {
                if (category.options.item(i).value === category.dataset.category) {
                    category.options.item(i).selected = true;
                }
            }
        }
    };
</script>
