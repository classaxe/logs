<x-app-layout>
    <h1>Showing logs for <a href="{{ url('/callsign', ['callsign' => $user['call']]) }}">{{ $user['call'] }}</a></h1>
    <h2>{{ $user['name'] }}, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}} ({{ $user['log_count' ]}} logs)</h2>

    <div class="bands">
        @foreach($bands as $n => $b)
            <label class="band band{{ $b }}"><input type="checkbox" data-band="{{ $b }}" checked>{{ $b }}</label>
        @endforeach
            <label><input type="checkbox" checked class="bandsAll"> All</label>
    </div>
    <table class="list">
        <thead>
            <tr>
                <td>&nbsp;</td>
                <th>Date</th>
                <th>UTC</th>
                <th>Call</th>
                <th>Band</th>
                <th>Mode</th>
                <th class="num">RX</th>
                <th class="num">TX</th>
                <th class="num">Pwr</th>
                <th>QTH</th>
                <th>S/P</th>
                <th>ITU</th>
                <th>GSQ</th>
                <th class="num">Km</th>
                <th>Conf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $n=>$log)
                <tr class="b{{ $log['band'] }} m{{ $log['mode'] }}">
                    <td>{{ $n+1 }}</td>
                    <td class="nowrap">{{ $log['date'] }}</td>
                    <td class="nowrap">{{ $log['time'] }}</td>
                    <td>{{ $log['call'] }}</td>
                    <td><span class="band band{{ $log['band'] }}">{{ $log['band'] }}</span></td>
                    <td>{{ $log['mode'] }}</td>
                    <td class="num">{{ $log['rx'] }}</td>
                    <td class="num">{{ $log['tx'] }}</td>
                    <td class="num">{{ $log['pwr'] }}</td>
                    <td>{{ $log['qth'] }}</td>
                    <td>{{ $log['sp'] }}</td>
                    <td>{{ $log['itu'] }}</td>
                    <td>{{ $log['gsq'] }}</td>
                    <td class="num">{{ number_format($log['km']) }}</td>
                    <td class="r">{{$log['conf']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
