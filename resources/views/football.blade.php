<!-- resources/views/selenium.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>The bet Armagedon</title>
    <!-- Link to the favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('betarmagedon-512x512.png') }}" alt="Bet Armagedon Logo" style="width: 150px; height: auto;">
    </div>
    @foreach ($returnAllMathcesData as $keyLeagueName => $matchesData)
    <h1>{{$keyLeagueName}}</h1>
        @if(isset($matchesData['betano_matches']))
            <h2>Betano</h2>
            @php $indexBetano=0; @endphp
            @foreach($matchesData['betano_matches'] as $keyMatchName => $match)
                @if(!empty($match->odds['1']))
                    @php $indexBetano++; @endphp
                    <p>{{ $indexBetano }} Match {{ $keyMatchName  }} [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->start_time }} </p>
                @endif
            @endforeach
        @endif
        @if(isset($matchesData['suberbet_matches']))
            <h2>Superbet</h2>
            @php $indexSuperbet=0; @endphp
            @foreach($matchesData['suberbet_matches'] as $keyMatchName => $match)
                @if(!empty($match->odds['1']))
                    @php $indexSuperbet++; @endphp
                    <p>{{ $indexSuperbet }} Match {{ $keyMatchName  }} [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->start_time }} </p>
                @endif
            @endforeach
        @endif
        @if(isset($matchesData['casapariurilor_matches']))
            @php $indexCasaPariurilor=0; @endphp
            <h2>Casa Pariurilor</h2>
            @foreach($matchesData['casapariurilor_matches'] as $keyMatchName => $match)
                @if(!empty($match->odds['1']))
                    @php $indexCasaPariurilor++; @endphp
                    <p>{{ $indexCasaPariurilor }} Match {{ $keyMatchName  }} [1] -> {{ $match->odds['1'] }} [x] -> {{ $match->odds['x'] }} [2]-> {{ $match->odds['2'] }} , date -> {{ $match->start_time }} </p>
                @endif
            @endforeach
        @endif
    @endforeach
</body>
</html>
