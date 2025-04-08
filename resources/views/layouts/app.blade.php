<!DOCTYPE html>
@php $gitTag = exec('git describe --tags') @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="prefetch" href="/images/loader.gif">
        <link href="/flag-icons/css/flag-icons.css" rel="stylesheet">

        <!-- Scripts -->
<?php ob_start() ?>
        @vite([
            'resources/css/app.css',
            'resources/css/logs.css',
            'resources/css/print-elements.css',
            'resources/js/app.js',
        ])
        <?php echo str_replace(' /><', ">\n        <", ob_get_clean()) ?>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 print:bg-transparent pt-1">
            <header>
                @include('layouts.nav')
            </header>
            <main>
                {{ $slot }}
            </main>
            <footer class="text-center text-sm text-gray-500 not-compact">
                ©{{ date('Y') }} Martin Francis <a href="{{ route('logs.page', ['callsign' => 'VA3PHP']) }}">VA3PHP</a> <b>|</b>
                Logs <a href="https://github.com/classaxe/logs" target="_blank">v{{ $gitTag }}</a> <b>|</b>
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} <b>|</b>
                PHP v{{ PHP_VERSION }}
            </footer>
        </div>
        <div class="overlay"></div>
    </body>
</html>
