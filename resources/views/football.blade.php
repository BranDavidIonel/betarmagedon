<!-- resources/views/selenium.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>The bet Armagedon</title>
</head>
<body>
    <h1>Betano</h1>
    @php $indexBetano=0; @endphp
    @foreach($betanoMatches as $keyMatchName => $match)
        @if(!empty($match['1']))
            @php $indexBetano++; @endphp
            <p>{{ $indexBetano }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }} , date -> {{ $match['startTime'] }} {{ !empty($match['isLive']) ? ', is live now' : ""}}</p>
        @endif
    @endforeach
    <h1>Superbet</h1>
    @php $indexSuperbet=0; @endphp
    @foreach($superbetMatches as $keyMatchName => $match)
        @if(!empty($match['1']))
            @php $indexSuperbet++; @endphp
            <p>{{ $indexSuperbet }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }} , date -> {{ $match['startTime'] }} {{ !empty($match['isLive']) ? ', is live now' : ""}}</p>
        @endif
    @endforeach
    @php $indexCasaPariurilor=0; @endphp
    <h1>Casa Pariurilor</h1>
    @foreach($casapariurilorMatches as $keyMatchName => $match)
        @if(!empty($match['1']))
            @php $indexCasaPariurilor++; @endphp
            <p>{{ $indexCasaPariurilor }} Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }}</p>
        @endif
    @endforeach
</body>
</html>