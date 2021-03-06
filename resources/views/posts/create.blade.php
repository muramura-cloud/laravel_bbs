@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="border p-4">
        <h1 class="h5 mb-4">
            投稿の新規作成
        </h1>

        <form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data">
            @csrf

            <fieldset class="mb-4">
                <div class="form-group">
                    <label for="title"><strong>タイトル</strong>(必須)</label>
                    <input id="title" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                        value="{{ old('title') }}" type="text">
                    @if ($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="body"><strong>本文</strong>(必須)</label>
                    <textarea id="body" name="body" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"
                        rows="4">{{ old('body') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="img"><strong>画像</strong>(任意)</label><br>
                    <input type="file" name="img" id="img"
                        class="form-control {{ $errors->has('img') ? 'is-invalid' : '' }}">
                    @if ($errors->has('img'))
                    <div class="invalid-feedback">
                        {{ $errors->first('img') }}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="category"><strong>カテゴリー</strong>(任意)</label><br>
                    <select class="form-control" id="category" name="category">
                        <option value="{{null}}">指定なし</option>
                        <option>マクロ経済学</option>
                        <option>ミクロ経済学</option>
                        <option>社会経済学</option>
                        <option>統計学</option>
                        <option>経済数学</option>
                        <option>経営学</option>
                        <option>会計学</option>
                        <option>経済史</option>
                        <option>経済史</option>
                        <option>簿記</option>
                        <option>金融</option>
                        <option>その他</option>
                    </select>
                </div>
                <div class="mt-5">
                    <a class="btn btn-secondary" href="{{ route('top') }}">キャンセル</a>
                    <button type="submit" class="btn btn-primary">投稿する</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection
