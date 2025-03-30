@extends('layouts.app')

@section('title', 'API tests endpoint')

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
                    Betano Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-betano</span>
                </a>
                <a href="{{ route('api-tests-superbet') }}" class="btn btn-primary mt-2">
                    Superbet Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-superbet</span>
                </a>
                <a href="{{ route('api-tests-casapariurilor') }}" class="btn btn-primary mt-2">
                    Casapariurilor Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-casapariurilor</span>
                </a>
            </td>
            <td>
                <pre class="text-left">{{ json_encode($scrapedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </td>
            <td>
                <pre class="text-left">{{ $apiStructureJson }}</pre>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

