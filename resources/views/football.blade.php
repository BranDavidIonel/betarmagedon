@extends('layouts.app')

@section('title', 'Old scraped data')

@section('content')
    <div class="">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Betano</th>
                <th>Subertbet</th>
                <th>Casapariurilor</th>
                <th>Betting Analysis</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($returnAllMathcesData as $leagueName => $leagueData)
                <!-- League header row -->
                <tr class="table-primary">
                    <th colspan="5">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $leagueName }}</strong>
                                <span class="text-muted">
                            ({{ $leagueData['countryName'] }})
                        </span>
                            </div>

                            <div>
                                Search Has Profit:
                                @if($leagueData['searhHasProfit'])
                                    <span class="badge bg-success">YES</span>
                                @else
                                    <span class="badge bg-danger">NO</span>
                                @endif
                            </div>
                        </div>
                    </th>
                </tr>
                @foreach ($leagueData['detailsProfit'] as $index => $match)
                    <tr>
                    <td></td>
                    @foreach ($match['matchesData'] as $bookmaker => $matchData)
                        <td>
                            <div><a href="{{$matchData['linkLeague']}}" target="_blank">link league</a></div>
                            <div><b>{{ $matchData['team1Name'] }}</b> - <b>{{ $matchData['team2Name'] }}</b></div>
                            <div>match time: <b>{{ $matchData['startTime'] }}</b></div>
                            <div>odds:
                                1 -> <b>{{ $matchData['odds']['1'] }} </b>,
                                X -> <b>{{ $matchData['odds']['x'] }} </b>,
                                2 -> <b>{{ $matchData['odds']['2'] }} </b>
                            </div>
                            <div>last scraped time: {{ $matchData['lastScrapedTime'] }}</div>
                        </td>
                    @endforeach
                    <td>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th style="cursor: help;"
                                    title="Reverse Odds = 1/(1/Odds1 + 1/Odds2 + 1/Odds3). Value < 1 = profit">
                                    Reverse Odds
                                </th>
                                <th>Is Profit</th>
                                <th>
                                    <span style="cursor: help;"
                                          title="Get the best options for high-value bets with maximum winning potential">
                                        Max Bets
                                    </span>
                                </th>
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

                @endforeach
            </tbody>
        </table>
    </div>
@endsection
