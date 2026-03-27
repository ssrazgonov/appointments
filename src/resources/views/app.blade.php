<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RecordToMaster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @if($app === 'landing')
        @vite(['resources/js/landing/main.js'])
    @elseif($app === 'dashboard')
        @vite(['resources/js/dashboard/main.js'])
    @elseif($app === 'booking')
        @vite(['resources/js/booking/main.js'])
    @elseif($app === 'client')
        @vite(['resources/js/client/main.js'])
    @endif
</head>
<body class="font-sans antialiased">
    @if($app === 'landing')
        <div id="landing-app"></div>
    @elseif($app === 'dashboard')
        <div id="dashboard-app"></div>
    @elseif($app === 'booking')
        <div id="booking-app"></div>
    @elseif($app === 'client')
        <div id="client-app"></div>
        <script>
            console.log('Client app loading...');
            window.addEventListener('DOMContentLoaded', () => {
                console.log('DOM loaded, app div exists:', !!document.getElementById('client-app'));
            });
        </script>
    @endif
</body>
</html>
