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
        <div class="my-5">
            <h2 class="mb-4 text-primary">{{ $leagueName }}</h2>

            <p><strong>Search Has Profit:</strong>
                @if ($leagueData['searhHasProfit'])
                    <span class="badge bg-success">Yes</span>
                @else
                    <span class="badge bg-danger">No</span>
                @endif
            </p>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                @foreach ($leagueData['detailsProfit'] as $index => $match)
                    <div class="col">
                        <div class="card shadow-sm rounded">
                            <div class="card-header bg-light">
                                <h5>Match {{ $index + 1 }}</h5>
                            </div>

                            <div class="card-body">
                                @foreach ($match['matchesData'] as $bookmaker => $matchData)
                                    <div class="mb-3">
                                        <div class="p-3 rounded bg-light border">
                                            <h6><strong>{{ ucfirst($bookmaker) }}</strong></h6>

                                            <!-- Link Bookmaker -->
                                            <p>
                                                <a href="{{ $matchData['linkLeague'] }}" target="_blank" class="btn btn-outline-primary btn-sm mt-1 mb-2">
                                                    View Link
                                                </a>
                                            </p>

                                            <!-- Teams Information -->
                                            <p><strong>Teams:</strong> {{ $matchData['team1Name'] }} - {{ $matchData['team2Name'] }}</p>

                                            <!-- Match Time -->
                                            <p><strong>Match Time:</strong> {{ $matchData['startTime'] }}</p>

                                            <!-- Last Scraped Information -->
                                            <p><strong>Last Scraped:</strong> {{ $matchData['lastScrapedTime'] }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                <hr>

                                <!-- Profit Data -->
                                <p><strong>Profit Information</strong></p>

                                <table class="table table-sm table-borderless mt-2">
                                    <tr>
                                        <th>Revers Odds</th>
                                        <th>Is Profit</th>
                                        <th>Max Bets</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $match['profitData']['reversOdds'] }}</td>
                                        <td>
                                            @if ($match['profitData']['isProfit'])
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($match['profitData']['maxBets'] as $betType => $maxBet)
                                                <p>{{ $betType }}: {{ $maxBet }}</p>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>

                            </div>

                            <div class="card-footer bg-light text-muted">
                                <small>Data Updated Recently</small>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>

        </div>
    @endforeach

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
