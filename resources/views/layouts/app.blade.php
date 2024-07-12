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
            'resources/js/logs.js',
            'resources/js/nite-overlay/nite-overlay.js',
            'resources/js/map.js'
        ])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @if (Auth::user())
            @include('layouts.navigation')
            @endif

            <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <header>
                    @if (Route::has('login'))
                        <nav class="-mx-3 flex flex-1 justify-end">
                            <a
                                href="/"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >Home</a>
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>
                <!-- Page Content -->
            </div>
            <main style="margin: 0.5em">
                {{ $slot }}
            </main>
            <footer class="text-center text-sm text-gray-500">
                Logs <a href="https://github.com/classaxe/logs" target="_blank">v{{ $gitTag }}</a> |
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}
            </footer>
        </div>
        <div class="overlay"></div>
    </body>
</html>
