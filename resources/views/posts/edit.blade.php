@extends('layouts.layout')

@section('content')
<div class="container">
    <div class="border p-4">
        <h1 class="h5 mb-4">投稿の編集</h1>

        {{-- htmlのフォームがput送信に対応していないからpostになっているけど、laravelでプtに直している。 --}}
        <form action="{{route('posts.update',['post'=>$post->id])}}" method="post">
            @csrf
            @method('put')

            <fieldset>
                <div class="form-group">
                    <label for="title">タイトル</label>
                    <input id="title" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                        value="{{ old('title') ?: $post->title }}" type="text">
                    @if ($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="body">本文</label>
                    <textarea id="body" name="body" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"
                        rows="4">{{ old('body') ?: $post->body }}</textarea>
                    @if ($errors->has('body'))
                    <div class="invalid-feedback">
                        {{ $errors->first('body') }}
                    </div>
                    @endif
                </div>

                <div class="mt-5">
                    <a class="btn btn-secondary" href="{{ route('posts.show', ['post' => $post]) }}">キャンセル</a>
                    <button type="submit" class="btn btn-primary">更新する</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection
