@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <h1 class="mb-4">違反報告フォーム</h1>
    <form method="post" action="/report">
        @csrf

        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="target" value="{{$target}}">
        <fieldset class="mb-4">
            <div class="form-group">
                <label for="report_category"><strong>カテゴリー</strong></label>
                <select class="form-control" id="report_category" name="report_category_id">
                    <option value="100">暴力的なコンテンツ</option>
                    <option value="200">差別的なコンテンツ</option>
                    <option value="300">性的なコンテンツ</option>
                    <option value="400">有害なコンテンツ</option>
                    <option value="500">スパム的なコンテンツ</option>
                    <option value="999">その他</option>
                </select>
            </div>

            <div class="form-group">
                <label><strong>メッセージ</strong></label>
                <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" name="comment">
                    @if ($errors->has('comment'))
                    <div class="invalid-feedback">
                        {{ $errors->first('comment') }}
                    </div>
                    @endif
                </textarea>
            </div>

            <pre>
                @php
                print_r($errors);
                @endphp    
            </pre>

            <div class="form_group text-right">
                <button id="report_btn" type="submit" class="btn btn-danger">報告する</button>
            </div>
        </fieldset>
    </form>
</div>
@endsection
