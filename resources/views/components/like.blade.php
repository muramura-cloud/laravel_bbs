<div class="fav">
    @if($user && $like->like_exist($user->id,$post->id))
    <span class="favorite-marke">
        <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                class="fas fa-heart likesIcon loved"></i></a>
        <span class="likesCount">{{$post->likes_count}}</span>
    </span>
    @elseif($user && !$like->like_exist($user->id,$post->id))
    <span class="favorite-marke">
        <a href="" class="js_like_toggle" data-user="{{$user}}" data-postid="{{ $post->id }}"><i
                class="fas fa-heart likesIcon"></i></a>
        <span class="likesCount">{{$post->likes_count}}</span>
    </span>
    @else
    <span class="favorite-marke">
        <a href="" class="js_like_toggle" data-user="not_login" data-postid="{{ $post->id }}"><i
                class="fas fa-heart likesIcon"></i></a>
        <span class="likesCount">{{$post->likes_count}}</span>
    </span>
    @endif
</div>
