<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The bet Armagedon')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-wrapper {
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <img src="{{ asset('betarmagedon-512x512.png')}}" alt="The bet Armagedon" style="height: 50px; width: auto;"/>
                <a class="navbar-brand" href="/" style="margin-left: 5px;">The bet Armagedon</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route("api-tests")}}">API Tests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route("scraped-live")}}">Scraped Data</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Bookkeeping</a></li>
                    </ul>
                </div>
            </div>
        </nav>

    <div class="content-wrapper mt-4 ">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

