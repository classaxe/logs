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
                    <th>QRZ</th>
                </tr>
            </thead>
            <tbody>
        @foreach($users as $u)
            <tr>
                <td><a href="{{ route('logs.page', ['callsign' => $u['call']]) }}">{{ $u['call'] }}</a></td>
                <td>{{ $u['name'] }}</td>
                <td>{{ $u['city'] }}</td>
                <td>{{ $u['sp'] }}</td>
                <td>{{ $u['itu'] }}</td>
                <td>{{ $u['gsq'] }}</td>
                <td>{{ $u['log_count'] }}</td>
                <td><a target="_blank" href="https://www.qrz.com/db/{{ $u['call'] }}">LINK</a></td>
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
