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
            @if($user->pota)<th>POTA</th>@endif
            <th>Map</th>
            @if(!$hidestats)
                <th>Dates</th>
                <th title="Days actively logging">*Days</th>
                <th>Logs</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($qths as $label => $q)
            @if(!isset($q['gsq']))
                @continue
            @endif
            <tr>
                <td class="gsq" title="Lat: {{ $q['lat'] }}, Lon: {{ $q['lon'] }}"> {{ $q['gsq'] }}</td>
                <td><a href="{{ route('home') }}/logs/{{ str_replace('/', '-', $user->call) }}/?q[]=myQth|{{ $label }}" target="_blank">{{ $label }}</a></td>
                @if($user->pota)<td>@if(substr($label, 0, 4) === 'POTA')<a target="_blank" href="https://pota.app/#/park/{{ explode(' ', $label)[1] }}">View</a>@endif</td>@endif
                <td><a target="_blank" href="https://k7fry.com/grid/?qth={{ $q['gsq'] }}">Map</a></td>
                @if(!$hidestats)
                    <td>{{ $q['logFirst'] }}@if($q['logDays'] > 1) - {{ $q['logLast'] }}@endif</td>
                    <td class="r">{{ $q['logDays'] }}</td>
                    <td class="r">{{ $q['logs'] }}</td>
                @endif
            </tr>
        @endforeach
        @if(Auth::user() && (Auth::user()->admin || $user->call === Auth::user()->call))
            <tr>
                <form method="get" action="{{ $url }}">
                <td class="test">
                        <input type="hidden" name="action" value="testgsq">
                    @if($hidestats)
                        <input type="hidden" name="hidestats" value="1">
                    @endif
                        <input type="text" name="testgsq" value="{{ request('testgsq') ?? '' }}">
                </td>
                <td colspan="{{ ($user->pota ? 1 : 0) + ($hidestats ? 3 : 5) }}">&lt;-- Test a new gridsquare to find the resulting radius <input style="float:right" type="submit" class="btn b" value="Test"></td>
                </form>
            </tr>
        @endif
        @if (!$hidestats && count($qths) > 1)
            <tr class="totals">
                <td colspan="{{ ($user->pota ? 4 : 3) }}">Totals</td>
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
