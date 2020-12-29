<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>立教 BBS</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="{{asset('css/post/like.css')}}" rel="stylesheet">
        <link href="{{asset('css/post/post.css')}}" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="{{ url('') }}">立教 BBS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{route('top')}}">トップ<span class="sr-only">(current)</span></a>
                    </li>
                </ul>
                <div>
                    @auth
                    <span class="navbar-text mr-1">{{$user->name}}</span>
                    <a href="/logout">
                        <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">ログアウト</button>
                    </a>
                    @else
                    <a href="{{route('login')}}">
                        <button class="btn btn-outline-primary my-2 my-sm-0 mr-1" type="submit">ログイン</button>
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

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
            integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
        </script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="{{ asset('js/btn.js') }}"></script>
        <script src="{{ asset('js/like.js') }}"></script>
        <script src="{{ asset('js/report.js') }}"></script>
        <script src="{{ asset('js/functions.js') }}"></script>
    </body>

</html>
