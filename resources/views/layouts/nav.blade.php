@php
use App\Http\Controllers\ChangesController;
$changes = floor((time() - strtotime(exec('git log -1 --format="%ad"'))) / (60 * 60 * 24)) < ChangesController::NEW_DAYS;
@endphp
<nav>
    <a href="{{ route('home') }}"{{ Route::currentRouteName() === 'home' ? " class=is-active" : '' }}>Home</a>
    @auth
        <a href="{{ route('profile.edit') }}"{{
            Route::currentRouteName() === 'profile.edit' ? " class=is-active" : ''
        }}>Profile</a>
        <a href="{{ route('summary', ['callsign' => str_replace('/', '-', Auth::user()->call)]) }}"{{
            Route::currentRouteName() === 'summary' && request()->route('callsign') === str_replace('/', '-', Auth::user()->call) ? " class=is-active" : ''
        }}>Summary</a>
        <a href="{{ route('dashboard') }}"{{
            Route::currentRouteName() === 'dashboard' ? " class=is-active" : ''
        }}>Dashboard</a>
        @if (Auth::user()->is_visible)
            <a href="{{ route('logs.page', ['callsign' => str_replace('/', '-', Auth::user()->call)]) }}"{{
                Route::currentRouteName() === 'logs.page'
                && isset(Route::current()->parameters()['callsign'])
                && Route::current()->parameters()['callsign'] === str_replace('/', '-', Auth::user()->call) ? " class=is-active" : ''
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
    }}>Changes
        @if($changes)<span class="new">&#9673;</span>@endif
    </a>
</nav>
