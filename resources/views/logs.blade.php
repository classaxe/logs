<x-app-layout>
    <div class="bands mt-6 text-center">
        <h1 style="display: inline-block">Showing logs for <a href="{{ url('/logs', ['callsign' => $user['call']]) }}">{{ $user['call'] }}</a></h1>
        <h2 style="display: inline-block; margin-left: 2em"><strong>{{ $user['name'] }}</strong>, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}}</h2>
        <h3 style="display: inline-block; margin-left: 2em">{{ $user['log_count' ]}} logs (updated: {{ \Carbon\Carbon::parse($user['qrz_last_data_pull'])->diffForHumans() }})</h3>
        <br>
        <fieldset>
            <label class="b">Bands</label>
            @foreach($bands as $n => $b)
                <label class="band band{{ $b }}"><input type="checkbox" data-band="{{ $b }}" checked>{{ $b }}</label>
            @endforeach
            <label><input type="checkbox" checked class="bandsAll"> All</label><br>
            <div style="height: 0.5em">&nbsp;</div>

            <label class="b">Mode</label>
            @foreach($modes as $m)
                <label class="mode m{{ $m }}"><input type="checkbox" data-mode="{{ $m }}" checked>{{ $m }}</label>
            @endforeach
            <label><input type="checkbox" checked class="modesAll"> All</label>

            <label class="b" style="margin-left: 2em">Confirmed</label>
            <label><input type="radio" name="conf" value="Y">Y</label>
            <label><input type="radio" name="conf" value="N">N</label>
            <label><input type="radio" name="conf" value="" checked="checked">All</label>
        </fieldset>
    </div>
    <p>Showing <span id="logsShown"><strong>{{ count($logs) }}</strong> log{{ count($logs) === 1 ? '' : 's'}}</span></p>
    <table class="list">
        <thead>
            <tr>
                <th class="az">&nbsp;</th>
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
                <tr class="b{{ $log['band'] }} m{{ $log['mode'] }} c{{ $log['conf'] ? 'Y' : 'N' }} i{{ str_replace(' ', '', $log['itu']) }} s{{ $log['sp'] }}">
                    <td>{{ $n+1 }}</td>
                    <td class="nowrap">{{ $log['date'] }}</td>
                    <td class="nowrap">{{ $log['time'] }}</td>
                    <td>{{ $log['call'] }}</td>
                    <td><span class="band band{{ $log['band'] }}">{{ $log['band'] }}</span></td>
                    <td><span class="mode m{{ $log['mode'] }}">{{ $log['mode'] }}</span></td>
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
