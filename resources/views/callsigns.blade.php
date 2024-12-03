<x-app-layout>
@if(Auth::user() && Auth::user()->admin)<?php ob_start() ?>
    @vite([ 'resources/js/callsigns.js'])
            <?php echo str_replace(' />', ">\n", ob_get_clean()) ?>
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
            at <a target="_blank" href="https://qrz.com">QRZ.com</a> to share their entire log with live stats and an
            interactive Grid Square map without visitors having to log in or create an account at either site.
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
                    <th class="pc">Name</th>
                    <th class="mobile">Name / Home QTH</th>
                    @if(Auth::user() && Auth::user()->admin)
                        <th class="pc">Email</th>
                        <th class="pc">Email Verified</th>
                    @endif
                    <th class="pc">Main QTH</th>
                    <th class="pc">S/P</th>
                    <th class="pc">ITU</th>
                    <th class="pc">GSQ</th>
                    <th class="pc"@if(Auth::user() && Auth::user()->admin) colspan="2" style="text-align: center"@endif>Logs</th>
                    <th class="mobile">Logs</th>
                    <th>QTHs</th>
                    <th class="mobile">Log Stats</th>
                    <th class="pc">First Log</th>
                    <th class="pc">Last Log</th>
                    <th class="pc">Last Fetch</th>
                    <th>Summary</th>
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
                <td>@if($u->log_count)<a href="{{ route('logs.page', ['callsign' => str_replace('/', '-', $u->call)]) }}" title="View Logbook">{{ $u->call }}</a>@else<b>{{ $u->call }}</b>@endif</td>
                <td class="pc">
                    @if(Auth::user() && Auth::user()->admin)
                        <a href="{{ route('user.edit', ['id' => $u->id]) }}" title="Edit User Profile">{{ $u->name }}</a>
                    @else
                        {{ $u->name }}
                    @endif
                </td>
                <td class="mobile">
                    @if(Auth::user() && Auth::user()->admin)
                        <a href="{{ route('user.edit', ['id' => $u->id]) }}" title="Edit User Profile">{{ $u->name }}</a>
                    @else
                        {{ $u->name }}
                    @endif
                    <br>
                    {{ $u->city }} {{ $u->sp }} {{ $u->itu }}
                </td>
                @if(Auth::user() && Auth::user()->admin)
                    <td class="pc">{{ $u->email }}</td>
                    <td class="pc">{{ $u->email_verified_at }}</td>
                @endif
                <td class="pc">{{ $u->city }}</td>
                <td class="pc">{{ $u->sp }}</td>
                <td class="pc">{{ $u->itu }}</td>
                <td class="pc">{{ $u->gsq }}</td>
                <td class="pc r">{{ $u->log_count }}</td>
                @if(Auth::user() && Auth::user()->admin)
                    <td class="pc c u_logs_purge"><a href="#">Purge</a></td>
                @endif
                <td class="mobile r">
                    {{ $u->log_count }}
                    @if(Auth::user() && Auth::user()->admin)<a class="u_logs_purge" href="#" title="Removes all logs for {{ $u->call }}">Purge</a>@endif
                </td>
                <td class="r">
                    @if($u->qth_count>0)<a href="{{ route('summaryMap', ['callsign' => str_replace('/', '-', $u->call)]) }}" title="View Locations Map" target="_blank">{{ $u->qth_count }}</a>
                    @else{{ $u->qth_count }}
                    @endif
                </td>
                <td class="mobile">
                    <span style="white-space: nowrap">From: {{ substr($u->first_log, 0, 10) }}<br>
                    To: {{ substr($u->last_log, 0, 10) }}<br>
                    Fetch: @if($u->qrz_last_result !== 'OK'){{ $u->qrz_last_result }} @else {{ $u->getLastQrzPull() }} @endif
                    </span>
                </td>
                <td class="pc r">{{ substr($u->first_log, 0, 10) }}</td>
                <td class="pc r">{{ substr($u->last_log, 0, 10) }}</td>
                <td class="pc r" @if(Auth::user() && Auth::user()->admin && $u->qrz_last_data_pull_debug)
                    style="cursor:pointer;text-decoration: underline" title="{{ $u->qrz_last_data_pull_debug }}"
                @endif>@if($u->qrz_last_result !== 'OK'){{ $u->qrz_last_result }} @else {{ $u->getLastQrzPull() }} @endif </td>
                <td class="c">@if($u->log_count)<a href="/summary/{{ str_replace('/', '-', $u->call) }}" title="View summary for {{ $u->call }}">VIEW</a>@endif</td>
                <td class="c"><a target="_blank" href="https://www.qrz.com/db/{{ $u->call }}" title="View QRZ Page for {{ $u->call }}">LINK</a></td>
                @if(Auth::user() && Auth::user()->admin)
                    <td class="c u_active"><a href="#" title="Toggle Active status for {{ $u->call }}">{{ $u->active ? 'Yes' : 'No' }}</a></td>
                    <td class="c u_admin"><a href="#" title="Toggle Admin status for {{ $u->call }}">{{ $u->admin ? 'Yes' : 'No' }}</a></td>
                    <td class="c u_is_visible"><a href="#" title="Toggle Visibility for {{ $u->call }}">{{ $u->is_visible ? 'Yes' : 'No' }}</a></td>
                @endif
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
 <style>
 @media screen and (max-width: 800px) {
     tbody td {
         vertical-align: top;
     }
     .mobile {
         display: table-cell;
     }
     .pc {
         display: none;
     }
 }
 @media screen and (min-width: 801px) {
     .mobile {
         display: none;
     }
     .pc {
         display: table-cell;
     }
 }</style>
</x-app-layout>
