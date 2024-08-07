<!DOCTYPE html>
@php $gitTag = exec('git describe --tags') @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />

        <!-- Images -->
        <link rel="prefetch" href="/images/loader.gif" />

        <!-- Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/css/logs.css',
            'resources/js/app.js',
            'resources/js/logs.js'
        ])
        <script src="/js/nite-overlay/nite-overlay.js"></script>
        <script src="/js/map_common.js"></script>
        <script src="/js/lmap.js"></script>
        <script src="/js/cookies.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 pt-1">
            <header>
                @if (Route::has('login'))
                    <nav>
                        <a href="{{ route('callsigns') }}"{{ Route::currentRouteName() === 'callsigns' ? "class=is-active" : '' }}>Home</a>
                        @auth
                            <a href="{{ route('profile.edit') }}"{{ Route::currentRouteName() === 'profile.edit' ? "class=is-active" : '' }}>Profile</a>
                            <form id="logout" method="POST" action="{{ route('logout') }}">@csrf</form>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout').submit();">{{ __('Log Out') }}</a>
                        @else
                            <a href="{{ route('login') }}"{{ Route::currentRouteName() === 'login' ? "class=is-active" : '' }}>Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"{{ Route::currentRouteName() === 'register' ? "class=is-active" : ''}}>Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>
            <main>
                {{ $slot }}
            </main>
            <footer class="text-center text-sm text-gray-500 not-compact">
                Logs <a href="https://github.com/classaxe/logs" target="_blank">v{{ $gitTag }}</a> |
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}
            </footer>
        </div>
        <div class="overlay"></div>
    </body>
</html>
