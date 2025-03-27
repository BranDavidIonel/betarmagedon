@extends('layouts.app')

@section('title', 'Selenium Page')

@section('content')
    <table class="table table-bordered text-left"> {{-- Align text to the left --}}
        <thead class="table-dark">
        <tr>
            <th style="width: 320px;">API Name/Route</th>
            <th>Sample Data</th>
            <th>API Structure</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a href="{{ route('api-tests-betano') }}" class="btn btn-primary">
                    Betano Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">Get Live data</span>
                </a>
{{--                <a href="{{ route('api-tests-betano') }}" class="btn btn-primary mt-2">--}}
{{--                    Superbet Test Ligue 1 <span class="badge bg-danger ms-2">Get Live data</span>--}}
{{--                </a>--}}
            </td>
            <td>
                <pre class="text-left">{{ json_encode($betanoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </td>
            <td>
                <pre class="text-left">{{ $betanoJson }}</pre>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

