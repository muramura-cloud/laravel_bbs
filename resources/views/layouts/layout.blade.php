<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>Laravel BBS</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="{{ url('') }}">Laravel BBS</a>
            {{-- jsが必要 --}}
            {{-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> --}}

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/">トップページ<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="{{route('dashboard')}}">Disabled</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="{{route('admin_top')}}">管理者はこちら</a>
                    </li>
                </ul>
                <div>
                    @auth
                    <span class="navbar-text mr-1">ユーザー名 : {{$user->name}}</span>
                    <a href="/logout">
                        <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">ログアウト</button>
                    </a>
                    @else
                    <a href="{{route('login')}}">
                        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">ログイン</button>
                    </a>
                    <a href="{{route('register')}}">
                        <button class="btn btn-outline-warning my-2 my-sm-0" type="submit">新規登録</button>
                    </a>
                    @endauth
                </div>
            </div>
        </nav>

        <div>
            @yield('content')
        </div>
    </body>

</html>
