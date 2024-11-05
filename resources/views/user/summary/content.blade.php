<div id="qthinfo">
    <h2>{{ $title }}
        <a class="btn r" style="margin-left: 2em" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Reload</a>
        @if($hidestats)
            <a class="btn g" href="{{ $url }}">Show Stats</a>
        @else
            <a class="btn g" href="{{ $url }}?hidestats=1">Hide Stats</a>
        @endif
        <a class="btn b" target="_blank" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Print</a>
    </h2>
    @if (!$hidestats && count($qths) > 1)
        <p>@if(count($qths) === 2)Both @else All <b>{{count($qths)}}</b>@endif locations are situated within a radius of <b>{{ ceil($qth_bounds['radius'] / 1000) }} Km</b> ({{ ceil(0.6213712 * ($qth_bounds['radius'] / 1000)) }} Miles)</p>
    @endif
    <p>Click the links below to view live logs and an interactive gridsquares map.</p>
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Grid</th>
            <th>Location</th>
            @if(!$hidestats)
                <th>Dates</th>
                <th title="Days actively logging">*Days</th>
                <th>Logs</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($qths as $label => $q)
            <tr>
                <td> {{ $q['gsq'] }}</td>
                <td><a href="{{ route('home') }}/logs/{{ str_replace('/', '-', $user->call) }}/?presets[]=myQth|{{ $label }}" target="_blank">{{ $label }}</a></td>
                @if(!$hidestats)
                    <td>{{ $q['logFirst'] }}@if($q['logDays'] > 1) - {{ $q['logLast'] }}@endif</td>
                    <td class="r">{{ $q['logDays'] }}</td>
                    <td class="r">{{ $q['logs'] }}</td>
                @endif
            </tr>
        @endforeach
        @if (!$hidestats && count($qths) > 1)
            <tr class="totals">
                <td colspan="2">Totals</td>
                <td>{{ substr($user['first_log'], 0, 10) }}@if(substr($user['first_log'], 0, 10) !== substr($user['last_log'], 0, 10)) - {{ substr($user['last_log'], 0, 10) }}@endif</td>
                <td class="r">{{ $user['log_days'] ?: 0 }}</td>
                <td class="r">{{ $user['log_count'] }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    <p><b>*Days</b> means days with recorded logs.</p>
    @if($cta)
        <p class="cta">Share your <a href="https://qrz.com">QRZ.com</a> live logs, maps and stats at <a href="https://logs.classaxe.com" target="_blank">https://logs.classaxe.com</a></p>
    @endif
</div>
