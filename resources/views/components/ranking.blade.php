<h3 class="heading bg-dark text-center rounded">ランキング</h3>
<ol class="ranking">
    @foreach ($posts as $post)
    <li class="ranking-item border-bottom">
        <a href="{{route('posts.show',['post'=>$post->id,'page' => 1])}}">
            <span class="order"></span>
            <p class="text">{{$post->title}}</p>
        </a>
    </li>
    @endforeach
</ol>
