<p class="mt-2 text-center">Showing <span id="logsShown">all <strong>{{ count($logs) }}</strong> log{{ count($logs) === 1 ? '' : 's'}}</span></p>
<table class="list">
    <thead>
    <tr>
        <th class="az">&nbsp;</th>
        <th>Date</th>
        <th>UTC</th>
        <th>Callsign</th>
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
        <tr class="b{{
                        $log['band']
                    }} m{{
                        $log['mode']
                    }} g{{
                        $log['gsq']
                    }} c{{
                        $log['conf'] ? 'Y' : 'N'
                    }} i{{
                        str_replace(' ', '', $log['itu'])
                    }} s{{
                        $log['sp']
                    }} cs{{
                        $log['call']
                    }}">
            <td>{{ $n+1 }}</td>
            <td class="nowrap">{{ $log['date'] }}</td>
            <td class="nowrap">{{ $log['time'] }}</td>
            <td data-link="call">{{ $log['call'] }}</td>
            <td data-link="band"><span class="band band{{ $log['band'] }}">{{ $log['band'] }}</span></td>
            <td data-link="mode"><span class="mode m{{ $log['mode'] }}">{{ $log['mode'] }}</span></td>
            <td class="num">{{ $log['rx'] }}</td>
            <td class="num">{{ $log['tx'] }}</td>
            <td class="num">{{ $log['pwr'] }}</td>
            <td>{{ $log['qth'] }}</td>
            <td data-link="sp">{{ $log['sp'] }}</td>
            <td data-link="itu">{{ $log['itu'] }}</td>
            <td data-link="gsq">{{ $log['gsq'] }}</td>
            <td class="num">{{ number_format($log['km']) }}</td>
            <td class="r">{{$log['conf']}}</td>
        </tr>
    @endforeach

    </tbody>
</table>
