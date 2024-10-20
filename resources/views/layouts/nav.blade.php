<nav>
    <a href="{{ route('home') }}"{{ Route::currentRouteName() === 'home' ? " class=is-active" : '' }}>Home</a>
    @auth
        <a href="{{ route('profile.edit') }}"{{
            Route::currentRouteName() === 'profile.edit' ? " class=is-active" : ''
        }}>Profile</a>
        <a href="{{ route('summary', ['callsign' => Auth::user()->call]) }}"{{
            Route::currentRouteName() === 'summary' && request()->route('callsign') === Auth::user()->call ? " class=is-active" : ''
        }}>Summary</a>
        <a href="{{ route('dashboard') }}"{{
            Route::currentRouteName() === 'dashboard' ? " class=is-active" : ''
        }}>Dashboard</a>
        @if (Auth::user()->is_visible)
            <a href="{{ route('logs.page', ['callsign' => Auth::user()->call]) }}"{{
                Route::currentRouteName() === 'logs.page'
                && isset(Route::current()->parameters()['callsign'])
                && Route::current()->parameters()['callsign'] === Auth::user()->call ? " class=is-active" : ''
            }}>Your Logs</a>
            <a href="{{ route('logs.fetch') }}" id="fetch" title="Reloads your own logs from QRZ.com"{{
                Route::currentRouteName() === 'logs.fetch' ? " class=is-active" : ''
            }}>Fetch Logs</a>
        @endif
        <form id="logout" method="POST" action="{{ route('logout') }}">@csrf</form>
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout').submit();">{{ __('Sign Out') }}</a>
    @else
        <a href="{{ route('login') }}"{{
            Route::currentRouteName() === 'login' ? " class=is-active" : ''
        }}>Sign in</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}"{{
                Route::currentRouteName() === 'register' ? " class=is-active" : ''
            }}>Register</a>
        @endif
    @endauth
    <a href="{{ route('changes') }}"{{
        Route::currentRouteName() === 'changes' ? " class=is-active" : ''
    }}>Changes</a>
</nav>
