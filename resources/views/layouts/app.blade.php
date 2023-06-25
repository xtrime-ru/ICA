<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ mix('build/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('build/app.css') }}" rel="stylesheet">
    <link href="{{ mix('build/global.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <transition name="fade" mode="out-in">
        <app></app>
    </transition>
</div>
</body>
</html>
