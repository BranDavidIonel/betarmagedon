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
            <td style="width: 320px;">
                <a href="{{ route('api-tests-matches-betano') }}" class="btn btn-primary">
                    Betano Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-matches-betano</span>
                </a>
                <a href="{{ route('api-tests-matches-superbet') }}" class="btn btn-primary mt-2">
                    Superbet Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-matches-superbet</span>
                </a>
                <a href="{{ route('api-tests-matches-casapariurilor') }}" class="btn btn-primary mt-2">
                    Casapariurilor Test Ro -> Ligue 1 <span class="badge bg-danger ms-2">/api/scrape-matches-casapariurilor</span>
                </a>
                <a href="{{ route('api-tests-links-betano') }}" class="btn btn-warning mt-3">
                    Betano -> get ligues links <span class="badge bg-danger ms-2">/api/scrape-links/betano</span>
                </a>
                <a href="{{ route('api-tests-links-superbet') }}" class="btn btn-warning mt-3">
                    Superbet -> get ligues links <span class="badge bg-danger ms-2">/api/scrape-links/superbet</span>
                </a>
                <a href="{{ route('api-tests-links-casapariurilor') }}" class="btn btn-warning mt-3">
                    Casapariurilor -> get ligues links <span class="badge bg-danger ms-2">/api/scrape-links/casapariurilor</span>
                </a>
            </td>
            <td>
                <pre class="text-left">{{ json_encode($scrapedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </td>
            <td>
                <pre class="text-left">{{ $apiStructureJson }}</pre>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

