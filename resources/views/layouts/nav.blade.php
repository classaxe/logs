@php
use App\Http\Controllers\ChangesController;
$changes = floor((time() - strtotime(exec('git log -1 --format="%ad"'))) / (60 * 60 * 24)) < ChangesController::NEW_DAYS;
$isLogsPage =   (Auth::user() && Route::currentRouteName() === 'logs.page'  && request()->route('callsign') === str_replace('/', '-', Auth::user()->call));
$isSummary =    (Auth::user() && Route::currentRouteName() === 'summary'    && request()->route('callsign') === str_replace('/', '-', Auth::user()->call));
$isSummaryMap = (Auth::user() && Route::currentRouteName() === 'summaryMap' && request()->route('callsign') === str_replace('/', '-', Auth::user()->call));
@endphp
<nav>
    <a href="{{ route('home') }}"{{ Route::currentRouteName() === 'home' ? " class=is-active" : '' }}>Home</a>
    @auth
        <a href="{{ route('profile.edit') }}"{{
            Route::currentRouteName() === 'profile.edit' ? " class=is-active" : ''
        }}>Profile</a>
        <a href="{{ route('dashboard') }}"{{
            Route::currentRouteName() === 'dashboard' ? " class=is-active" : ''
        }}>Dashboard</a>
        @if (Auth::user()->is_visible)
            <a href="{{ route('summary', ['callsign' => str_replace('/', '-', Auth::user()->call)]) }}"{{
                $isSummary ? " class=is-active" : ''
            }}>Summary</a>
            <a href="{{ route('summaryMap', ['callsign' => str_replace('/', '-', Auth::user()->call)]) }}"{{
                $isSummaryMap ? " class=is-active" : ''
            }}>Map</a>
            <a href="{{ route('logs.page', ['callsign' => str_replace('/', '-', Auth::user()->call)]) }}"{{
                $isLogsPage ? " class=is-active" : ''
            }}>Your Logs</a>
            @if ($isSummary)
                <a href="{{ route('summary', ['callsign' => str_replace('/', '-', Auth::user()->call), 'action' => 'fetch']) }}">Fetch Logs</a>
            @elseif ($isSummaryMap)
                <a href="{{ route('summaryMap', ['callsign' => str_replace('/', '-', Auth::user()->call), 'action' => 'fetch']) }}">Fetch Logs</a>
            @elseif ($isLogsPage)
                <a href="{{ route('logs.page', ['callsign' => str_replace('/', '-', Auth::user()->call), 'action' => 'fetch']) }}">Fetch Logs</a>
            @else
                <a href="{{ route('logs.fetch') }}" id="fetch" title="Reloads your own logs from QRZ.com"{{
                Route::currentRouteName() === 'logs.fetch' ? " class=is-active" : '' }}>Fetch Logs</a>
            @endif
            <a href="{{ route('user.upload') }}" id="upload" title="Upload logs from an adi file"{{
                Route::currentRouteName() === 'user.upload' ? " class=is-active" : ''
            }}>Upload</a>
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
