<h2 class="heading bg-dark text-center rounded">カテゴリー</h2>
<ol class="category">
    @foreach ($categories as $category)
    <li class="ranking-item border-bottom">
        <a href="{{route('search',['category'=>$category->name,'page' => 1])}}">
            <p class="text text-center">{{$category->name}}</p>
        </a>
    </li>
    @endforeach
</ol>
