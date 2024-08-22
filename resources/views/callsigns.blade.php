<x-app-layout>
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
        <table class="list">
            <thead>
                <tr>
                    <th>Callsign</th>
                    <th>Name</th>
                    <th>QTH</th>
                    <th>S/P</th>
                    <th>ITU</th>
                    <th>GSQ</th>
                    <th>Logs</th>
                    <th>Last Log</th>
                    <th>Last Fetch</th>
                    <th>QRZ</th>
                    @if(Auth::user() && Auth::user()->admin)
                        <th>Visible</th>
                        <th>Admin</th>
                     @endif
                </tr>
            </thead>
            <tbody>
        @foreach($users as $u)
            <?php
                $isYou = Auth::user() && $u['id'] === Auth::user()->id;
                $class = ($u->is_visible ? '' : 'inactive ') . ($isYou ? 'current' : '');
            ?>
            <tr data-user="{{ $u->id }}" class="<?= $class ?>"
                @if($isYou)
                    title="This is you"
                @endif
            >
                <td><a href="{{ route('logs.page', ['callsign' => $u['call']]) }}">{{ $u['call'] }}</a></td>
                <td>{{ $u['name'] }}</td>
                <td>{{ $u['city'] }}</td>
                <td>{{ $u['sp'] }}</td>
                <td>{{ $u['itu'] }}</td>
                <td>{{ $u['gsq'] }}</td>
                <td class="r">{{ $u['log_count'] }}</td>
                <td>{{ substr($u['last_log'], 0, 16) }}</td>
                <td>{{ $u->getLastQrzPull() }}</td>
                <td><a target="_blank" href="https://www.qrz.com/db/{{ $u['call'] }}">LINK</a></td>
                @if(Auth::user() && Auth::user()->admin)
                    <td class="r u_is_visible"><a href="#">{{ $u->is_visible ? 'Y' : 'N' }}</a></td>
                    <td class="r u_admin"><a href="#">{{ $u->admin ? 'Y' : 'N' }}</a></td>
                @endif
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
@if(Auth::user() && Auth::user()->admin)
<form id="form" method="POST">
    @csrf
    <input type="hidden" name="action" value="">
    <input type="hidden" name="target" value="">
    <input type="hidden" name="value" value="">
</form>
<script>
window.addEventListener("DOMContentLoaded", function() {
    $('.u_admin').click((e) => {
        if (confirm($(e.target).text() === 'N' ? 'Make this user an administrator?' : 'Revoke admin access for this user?')) {
            $('[name=action]').val('setAdmin');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val($(e.target).text() === 'Y' ? '0' : '1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
    $('.u_is_visible').click((e) => {
        if (confirm($(e.target).text() === 'N' ? 'Make this user visible?' : 'Make this user invisible?')) {
            $('[name=action]').val('setVisible');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val($(e.target).text() === 'Y' ? '0' : '1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
})
</script>
@endif

</x-app-layout>
