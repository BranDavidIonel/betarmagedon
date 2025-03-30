@extends('layouts.app')

@section('title', 'Get data live')

@section('content')
    <div class="my-2">
        <pre class="text-left">{{ json_encode($returnAllMathcesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
@endsection

