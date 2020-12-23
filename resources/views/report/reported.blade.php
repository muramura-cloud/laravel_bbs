@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <h1 class="mb-4">報告しました。</h1>
    <p>ありがとうございます。</p>
    <div class="mt-5">
        <a class="btn btn-secondary" href="{{ route('top') }}">トップページに戻る</a>
    </div>
</div>
@endsection
