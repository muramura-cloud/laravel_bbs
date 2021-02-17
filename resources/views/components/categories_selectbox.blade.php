@foreach ($categories as $category)
@if ($loop->first)
<option value="{{null}}">指定なし</option>
@endif
<option value="{{$category->name}}">{{$category->name}}</option>
@endforeach
