@extends('layouts.user')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">ユーザー情報</h2>
    <table class="table">
        <tbody>
            <tr>
                <th>名前</th>
                <td>{{$user->name}}</td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>{{$user->email}}</td>
            </tr>
            <tr>
                <th>登録日</th>
                <td>{{$user->created_at}}</td>
            </tr>
        </tbody>
    </table>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#my_posts" class="nav-link active" data-toggle='tab'>自分の投稿
                <span class="ml-2 badge badge-warning">{{!empty($unread_post_ids) ? '未読':''}}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#my_loved_posts" class="nav-link" data-toggle='tab'>イイねした投稿</a>
        </li>
        <li class="nav-item">
            <a href="#my_comments" class="nav-link" data-toggle='tab'>自分のコメント</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="my_posts" class="tab-pane active">
            @forelse ($posts as $post)
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
                        <form style="display: inline-block;" method="get"
                            action="{{ route('posts.show', ['post' => $post->id]) }}">
                            @csrf

                            <input type="hidden" name="page" value="{{$posts->currentPage()}}">
                            <input type="hidden" name="from" value="user_my_posts">
                            <button class="btn btn-success details_btn" data-postid="{{ $post->id }}"
                                data-page="{{$posts->currentPage()}}" data-from="user_my_posts">続きを読む</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
                        <span
                            class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
                        <span class="ml-2 badge badge-info">{{$post->category ? $post->category : ''}}</span>
                        <span
                            class="ml-2 badge badge-warning">{{in_array($post->id,$unread_post_ids,true) ? 'コメント未読' : ''}}</span>
                    </div>
                    @include('components.like',['post' => $post, 'user' => $user, 'like' => $like])
                </div>
            </div>
            @empty
            <p>投稿はまだありません。</p>
            @endforelse
            <div class="d-flex justify-content-center mb-5">
                {{ $posts->links() }}
            </div>
        </div>
        <div id="my_loved_posts" class="tab-pane">
            @forelse ($loved_posts as $post)
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
                        <form style="display: inline-block;" method="get"
                            action="{{ route('posts.show', ['post' => $post->id]) }}">
                            @csrf

                            <input type="hidden" name="page" value="{{$loved_posts->currentPage()}}">
                            <input type="hidden" name="from" value="user_my_loved_posts">
                            <button class="btn btn-success">続きを読む</button>
                        </form>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <span class="mr-2">投稿日時 : {{$post->created_at ? $post->created_at->format('Y.m.d') : ''}}</span>
                        <span
                            class="badge badge-primary">{{$post->comments->count() ? 'コメント'.$post->comments->count().'件':''}}</span>
                        <span class="ml-2 badge badge-info">{{$post->category ? $post->category : ''}}</span>
                        <span class="ml-2 badge">{{$post->user ? ' 投稿者 : ' . $post->user->name : ''}}</span>
                    </div>
                    @include('components.like',['post' => $post, 'user' => $user, 'like' => $like])
                </div>
            </div>
            @empty
            <br>
            <p>イイねした投稿はありません。</p>
            @endforelse
            <div class="d-flex justify-content-center mb-5">
                {{ $loved_posts->links() }}
            </div>
        </div>
        <div id="my_comments" class="tab-pane">
            @forelse($comments as $comment)
            <div class="card mb-4">
                <div class="card-body">
                    <time
                        class="text-secondary">{{ $comment->created_at ? $comment->created_at->format('Y.m.d H:i') : '' }}</time>
                    <p class="mt-2">{{$comment->body}}</p>
                    <div class="mb-1">
                        <form style="display: inline-block;" method="get"
                            action="{{ route('posts.show', ['post' => $comment->getPost()->id]) }}">
                            @csrf

                            <input type="hidden" name="page" value="{{$comments->currentPage()}}">
                            <input type="hidden" name="from" value="user_my_comments">
                            <button class="btn btn-success">投稿を読む</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse
            <div class="d-flex justify-content-center mb-5">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    window.onload = function(){
        let queries=getUrlQueries();
        if(!jQuery.isEmptyObject(queries)) {
            let tab_id = queries.from;
            let nav_links = document.getElementsByClassName('nav-link');
            for(let i = 0; i < nav_links.length; i++) {
                nav_links[i].classList.remove('active');
                if(nav_links[i].getAttribute('href') === `#${tab_id.replace('user_','')}`) {
                    console.log(tab_id);
                    nav_links[i].classList.add('active');
                }
            }

            let tab_panes = document.getElementsByClassName('tab-pane');
            for(let i = 0; i < tab_panes.length; i++) {
                tab_panes[i].classList.remove('active');
                if(tab_panes[i].getAttribute('id') === tab_id.replace('user_','')) {
                    console.log(tab_id);
                    tab_panes[i].classList.add('active');
                }
            }
        }
    };
</script>
