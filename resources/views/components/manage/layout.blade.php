<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('assets/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css') }}">
    <title>{{ $title ?? 'Hello!' }}</title>
    {{$styles ?? ''}}
</head>
<body>
{{ $slot }}

<script src="{{ asset('assets/npm/jquery@3.5.1/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js') }}"></script>
{{ $scripts ?? '' }}
</body>
</html>
