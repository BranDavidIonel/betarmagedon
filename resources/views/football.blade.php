<!-- resources/views/selenium.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>The bet Armagedon</title>
    <!-- Link to the favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
{{--    <div style="text-align: center; margin-bottom: 20px;">--}}
{{--        <img src="{{ asset('betarmagedon-512x512.png') }}" alt="Bet Armagedon Logo" style="width: 150px; height: auto;">--}}
{{--    </div>--}}
    <div class="container my-5">
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
{{--    @foreach ($returnAllMathcesData as $keyLeagueName => $matchesData)--}}
{{--    <h1>{{$keyLeagueName}}</h1>--}}
{{--        @if(isset($matchesData['betano_matches']))--}}
{{--            <h2>Betano</h2>--}}
{{--            @php $indexBetano=0; @endphp--}}
{{--            @foreach($matchesData['betano_matches'] as $keyMatchName => $match)--}}
{{--                @if(!empty($match->odds['1']))--}}
{{--                    @php $indexBetano++; @endphp--}}
{{--                    <p>{{ $indexBetano }} Match {{ $match->team1Name.'-'.$match->team2Name }} [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->startTime }} </p>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        @endif--}}
{{--        @if(isset($matchesData['suberbet_matches']))--}}
{{--            <h2>Superbet</h2>--}}
{{--            @php $indexSuperbet=0; @endphp--}}
{{--            @foreach($matchesData['suberbet_matches'] as $keyMatchName => $match)--}}
{{--                @if(!empty($match->odds['1']))--}}
{{--                    @php $indexSuperbet++; @endphp--}}
{{--                    <p>{{ $indexSuperbet }} Match {{ $match->team1Name.'-'.$match->team2Name }}  [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->startTime }} </p>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        @endif--}}
{{--        @if(isset($matchesData['casapariurilor_matches']))--}}
{{--            @php $indexCasaPariurilor=0; @endphp--}}
{{--            <h2>Casa Pariurilor</h2>--}}
{{--            @foreach($matchesData['casapariurilor_matches'] as $keyMatchName => $match)--}}
{{--                @if(!empty($match->odds['1']))--}}
{{--                    @php $indexCasaPariurilor++; @endphp--}}
{{--                    <p>{{ $indexCasaPariurilor }} Match {{ $match->team1Name.'-'.$match->team2Name }} [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->startTime }} </p>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        @endif--}}
{{--    @endforeach--}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
