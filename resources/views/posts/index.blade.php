@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <div class="mb-4">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">投稿を新規作成する</a>
            </div>

            @foreach ($posts as $post)
            @include('components.post',['post' => $post, 'user' => request()->user, 'like' => request()->like, 'page' =>
            $posts->currentPage()])
            @endforeach

            <div class="d-flex justify-content-center mb-5">
                {{ $posts->links() }}
            </div>
        </div>
        <div class="col-md-3">
            <br><br><br>
            @include('components.ranking',['posts' => request()->ranking_loved_posts])
            @include('components.categories',['categories' => request()->categories])
        </div>
    </div>
</div>
@endsection
