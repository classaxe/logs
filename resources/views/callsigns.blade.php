<x-app-layout>
@if(Auth::user() && Auth::user()->admin)
    @vite([ 'resources/js/callsigns.js'])
    <form id="form" method="POST" action="/user/patch">
        @csrf
        <input type="hidden" name="action" value="">
        <input type="hidden" name="target" value="">
        <input type="hidden" name="value" value="">
    </form>
@endif
    <div class="callsigns w-full max-w-fit mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style="margin: 3em auto 0.5em auto">
        <h1>QRZ Log and Map Viewer</h1>
        <p>This site enables Radio Amateurs who are <a target="_blank" href="https://shop.qrz.com/collections/subscriptions">XML Subscribers</a>
            at <a target="_blank" href="https://qrz.com">QRZ.com</a> to allow visitors to view their entire log with an
            interactive Grid Square map without having to log in or create an account at either site.
        </p>
        <p>To add your own logs to this system, click  <a href="{{ route('register') }}">Register</a> above to set up a profile.</p>
        <p>You will need to provide your QRZ.com API Key to use this facility, and a moderator will need to approve your
            submission in order to make it active on this site.</p>
        <p>Your logs from QRZ.com will be downloaded to this site automatically every 60 minutes to avoid overburdening
            the QRZ.com servers, but registered users may log in to refresh their logs manually at any time.</p>
        <h2>Registered Users</h2>
        <p> Click on any callsign listed below to view that users logs and map.</p>
        @if(Session::get('status'))
            <div id="status">{!! Session::get('status') !!}</div>
        @endif
        <table class="list">
            <thead>
                <tr>
                    <th>Callsign</th>
                    <th>Name</th>
                    @if(Auth::user() && Auth::user()->admin)
                        <th>Email</th>
                        <th>Email Verified</th>
                    @endif
                    <th>Main QTH</th>
                    <th>S/P</th>
                    <th>ITU</th>
                    <th>GSQ</th>
                    <th>Logs</th>
                    <th>QTHs</th>
                    <th>First Log</th>
                    <th>Last Log</th>
                    <th>Last Fetch</th>
                    <th>QRZ</th>
                    @if(Auth::user() && Auth::user()->admin)
                        <th>Active</th>
                        <th>Admin</th>
                        <th>Visible</th>
                     @endif
                </tr>
            </thead>
            <tbody>
        @foreach($users as $u)
            <?php
                $isYou = Auth::user() && $u['id'] === Auth::user()->id;
                $class = ($u->is_visible ? '' : 'invisible ')
                    . ($u->active ? '' : ' inactive ')
                    . ($isYou ? 'current ' : '')
                    . (Auth::user() && Auth::user()->admin && $u->admin ? 'admin ' : '');
            ?>
            <tr data-user="{{ $u->id }}" class="<?= $class ?>"
                @if($isYou)
                    title="This is you"
                @endif
                @if(!$u->active)
                    title="This log is not actively maintained"
                @endif
            >
                <td><a href="{{ route('logs.page', ['callsign' => $u->call]) }}" title="View Logbook">{{ $u->call }}</a></td>
                <td>
                    @if(Auth::user() && Auth::user()->admin)
                        <a href="{{ route('user.edit', ['id' => $u->id]) }}" title="Edit User Profile">{{ $u->name }}</a>
                    @else
                        {{ $u->name }}
                    @endif
                </td>
                @if(Auth::user() && Auth::user()->admin)
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->email_verified_at }}</td>
                @endif
                <td>{{ $u->city }}</td>
                <td>{{ $u->sp }}</td>
                <td>{{ $u->itu }} <span class="fi fi-{{ $u->itu }}"></span></td>
                <td>{{ $u->gsq }}</td>
                <td class="r">{{ $u->log_count }}</td>
                <td class="r">{{ $u->qth_count }}</td>
                <td class="r">{{ substr($u->first_log, 0, 10) }}</td>
                <td class="r">{{ substr($u->last_log, 0, 10) }}</td>
                <td class="r">@if($u->qrz_last_result !== 'OK'){{ $u->qrz_last_result }} @else {{ $u->getLastQrzPull() }} @endif </td>
                <td><a target="_blank" href="https://www.qrz.com/db/{{ $u->call }}">LINK</a></td>
                @if(Auth::user() && Auth::user()->admin)
                    <td class="c u_active"><a href="#">{{ $u->active ? 'Yes' : 'No' }}</a></td>
                    <td class="c u_admin"><a href="#">{{ $u->admin ? 'Yes' : 'No' }}</a></td>
                    <td class="c u_is_visible"><a href="#">{{ $u->is_visible ? 'Yes' : 'No' }}</a></td>
                @endif
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
