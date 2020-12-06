@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="border p-4">
        @if (!empty($post->img))
        <div class="d-flex">
            <div class="p-2 col-6">
                <h1 class="h5 mb-4">{{ $post->title }}</h1>
                <p class="mb-5">{{$post->body}}</p>
            </div>
            <div class="p-2 col-6">
                <a href="{{asset('storage/' . $post->img)}}"><img src="{{asset('storage/' . $post->img)}}"
                        class="img-fluid"></a>
            </div>
        </div>
        @else
        <h1 class="h5 mb-4">{{ $post->title }}</h1>
        <p class="mb-5">{{$post->body}}</p>
        @endif

        <div class="mb-4 text-left">
            <a class="btn btn-primary" href="{{ route('posts.edit', ['post' => $post->id]) }}">編集する</a>
            <form style="display: inline-block;" method="POST"
                action="{{ route('posts.destroy', ['post' => $post->id]) }}">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger">削除する</button>
            </form>

        </div>

        <form action="{{ route('comments.store') }}" method="post">
            @csrf

            <input type="hidden" name="post_id" value="{{$post->id}}">
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
                <time class="text-secondary">{{ $comment->created_at->format('Y.m.d H:i') }}</time>
                <p class="mt-2">{{$comment->body}}</p>
                <div class="mb-4 text-right">
                    <a class="btn btn-primary" href="{{ route('comments.edit', ['comment' => $comment->id]) }}">編集する</a>
                    <form style="display: inline-block;" method="POST"
                        action="{{ route('comments.destroy', ['comment' => $comment->id]) }}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger">削除する</button>
                    </form>

                </div>
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse
        </section>
    </div>

    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('top') }}">戻る</a>
    </div>
</div>
@endsection
