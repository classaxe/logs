<nav>
    <a href="{{ route('callsigns') }}"{{ Route::currentRouteName() === 'callsigns' ? "class=is-active" : '' }}>Home</a>
    @auth
        <a href="{{ route('profile.edit') }}"{{
            Route::currentRouteName() === 'profile.edit' ? "class=is-active" : ''
        }}>Your Profile</a>
        @if (Auth::user()->is_visible)
            <a href="{{ route('logs.page', ['callsign' => Auth::user()->call]) }}"{{
                Route::currentRouteName() === 'logs.page'
                && isset(Route::current()->parameters()['callsign'])
                && Route::current()->parameters()['callsign'] === Auth::user()->call ? "class=is-active" : ''
            }}>Your Logs</a>
            <a href="{{ route('logs.fetch') }}" title="Reloads your own logs from QRZ.com"{{
                Route::currentRouteName() === 'logs.fetch' ? "class=is-active" : ''
            }}>Refresh Your Logs</a>
        @endif
        <form id="logout" method="POST" action="{{ route('logout') }}">@csrf</form>
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout').submit();">{{ __('Log Out') }}</a>
    @else
        <a href="{{ route('login') }}"{{
            Route::currentRouteName() === 'login' ? "class=is-active" : ''
        }}>Log in</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}"{{
                Route::currentRouteName() === 'register' ? "class=is-active" : ''
            }}>Register</a>
        @endif
    @endauth
    <a href="{{ route('changes') }}"{{
        Route::currentRouteName() === 'changes' ? "class=is-active" : ''
    }}>Changes</a>
</nav>
