<div>
    <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
    <span
        class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
    <span class="ml-2 badge badge-info">{{$post->category ? $post->category : ''}}</span>
    <span class="ml-2 badge">{{$post->user ? ' 投稿者 : ' . $post->user->name : ''}}</span>
</div>
