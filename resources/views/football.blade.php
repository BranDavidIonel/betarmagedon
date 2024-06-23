<!-- resources/views/selenium.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>The bet Armagedon</title>
</head>
<body>
    @foreach ($returnAllMathcesData as $keyLeagueName => $matchesData)
    <h1>{{$keyLeagueName}}</h1>
        @if(isset($matchesData['betano_matches']))
            <h2>Betano</h2>
            @php $indexBetano=0; @endphp
            @foreach($matchesData['betano_matches'] as $keyMatchName => $match)
                @if(!empty($match['1']))
                    @php $indexBetano++; @endphp
                    <p>{{ $indexBetano }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }} , date -> {{ $match['startTime'] }} {{ !empty($match['isLive']) ? ', is live now' : ""}}</p>
                @endif
            @endforeach
        @endif
        @if(isset($matchesData['suberbet_matches']))
            <h2>Superbet</h2>
            @php $indexSuperbet=0; @endphp
            @foreach($matchesData['suberbet_matches'] as $keyMatchName => $match)
                @if(!empty($match['1']))
                    @php $indexSuperbet++; @endphp
                    <p>{{ $indexSuperbet }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }} , date -> {{ $match['startTime'] }} {{ !empty($match['isLive']) ? ', is live now' : ""}}</p>
                @endif
            @endforeach
        @endif
        @if(isset($matchesData['casapariurilor_matches']))
            @php $indexCasaPariurilor=0; @endphp
            <h2>Casa Pariurilor</h2>
            @foreach($matchesData['casapariurilor_matches'] as $keyMatchName => $match)
                @if(!empty($match['1']))
                    @php $indexCasaPariurilor++; @endphp
                    <p>{{ $indexCasaPariurilor }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }} , date -> {{ $match['startTime'] }} {{ !empty($match['isLive']) ? ', is live now' : ""}}</p>
                @endif
            @endforeach
        @endif
    @endforeach
</body>
</html>