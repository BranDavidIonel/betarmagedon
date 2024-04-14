<!-- resources/views/selenium.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>The bet Armagedon</title>
</head>
<body>
    <h1>Betano</h1>
    @foreach($betanoMatches as $keyMatchName => $match)
        @if(!empty($match['1']))
            <p>Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }}</p>
        @endif
    @endforeach
    <h1>Superbet</h1>
    @foreach($superbetMatches as $keyMatchName => $match)
        @if(!empty($match['1']))
            <p>Match {{ $keyMatchName  }} [1] -> {{ $match['1'] }} [x] -> {{ $match['x'] }} [2]-> {{ $match['2'] }}</p>
        @endif
    @endforeach
</body>
</html>