@extends('layouts.app')

@section('title', 'Selenium Page')

@section('content')
{{--    <div style="text-align: center; margin-bottom: 20px;">--}}
{{--        <img src="{{ asset('betarmagedon-512x512.png') }}" alt="Bet Armagedon Logo" style="width: 150px; height: auto;">--}}
{{--    </div>--}}
    <div class="my-2">
        <h1 class="mb-4">Match Data</h1>
    @foreach ($returnAllMathcesData as $leagueName => $leagueData)
            <h2>{{ $leagueName }}</h2>

            <p><strong>Search Has Profit:</strong> {{ $leagueData['searhHasProfit'] ? 'Yes' : 'No' }}</p>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>betano</th>
                    <th>subertbet</th>
                    <th>casapariurilor</th>
                    <th>Odds</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($leagueData['detailsProfit'] as $index => $match)
                    <tr>
                    <td></td>
                    @foreach ($match['matchesData'] as $bookmaker => $matchData)
                        <td>
{{--                            <span>{{ ucfirst($bookmaker) }}</span>--}}
                            <div><a href="{{$matchData['linkLeague']}}" target="_blank">link league</a></div>
                            <div>{{ $matchData['team1Name'] }} - {{ $matchData['team2Name'] }}</div>
                            <div>match time: {{ $matchData['startTime'] }}</div>
                            <div>last scraped time: {{ $matchData['lastScrapedTime'] }}</div>


                        </td>
                    @endforeach
                    <td>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Revers Odds</th>
                                <th>Is Profit</th>
                                <th>Max Bets</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ $match['profitData']['reversOdds'] }}</td>
                                <td>{!!  $match['profitData']['isProfit'] ? "<span style = 'color:green'>Yes</span>" : "<span style = 'color:red'>No</span>"  !!}</td>
                                <td>
                                    @foreach ($match['profitData']['maxBets'] as $betType => $maxBet)
                                        {{ $betType }}: {{ $maxBet }}<br>
                                    @endforeach
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endsection
