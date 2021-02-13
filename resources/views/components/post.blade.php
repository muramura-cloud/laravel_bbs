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
            <form style="display: inline-block;" method="get" action="{{ route('posts.show', ['post' => $post->id]) }}">
                @csrf

                <input type="hidden" name="page" value="{{$page}}">
                <button class="btn btn-success">続きを読む</button>
            </form>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        @include('components.post_footer',['post' => $post])
        @include('components.like',['post' => $post, 'user' => $user, 'like' => $like])
    </div>
</div>
