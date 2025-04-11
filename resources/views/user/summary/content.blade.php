<script>
function copyToClipboard(text) {
    console.log(text);
    var temp = $("<textarea>");
    $("body").append(temp);
    temp.val(text).select();
    document.execCommand("copy");
    temp.remove();
    return false;
}
</script>
<div id="qthinfo">
    <h2>{!! $title !!}
        <a class="btn r" style="margin-left: 2em" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Reload</a>
        @if($hidestats)
            <a class="btn g" href="{{ $url }}">Show Stats</a>
        @else
            <a class="btn g" href="{{ $url }}?hidestats=1">Hide Stats</a>
        @endif
        <a class="btn b" target="_blank" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Print</a>
        <a class="btn o" target="_blank" href="{{ route('summaryMap', ['callsign' => str_replace('/', '-', $user->call)]) }}" title="View Locations Map">Map</a>
    </h2>
    @if (!$hidestats && count($qths) > 1)
        <p>@if(count($qths) === 2)Both @else All <b>{{count($qths)}}</b>@endif locations are situated within a radius of <b>{{ ceil($qth_bounds['radius'] / 1000) }} Km</b> ({{ ceil(0.6213712 * ($qth_bounds['radius'] / 1000)) }} Miles)</p>
        <p>Last log was made at <b>{{ substr($user['last_log'], 11, 5) }}</b> on <b>{{ substr($user['last_log'], 0, 10) }}</b>
            from <a class="url" href="#lastQth"><b>{{ $user['lastQth'] }}</b></a>
        </p>
    @endif
    <p>Click the links below to view live logs and an interactive gridsquares map.</p>
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Grid</th>
            <th class="qth">Location (Click for logs)</th>
            @if($user->pota)<th>POTA</th>@endif
            @if(!$hidestats)
                <th class="dates">Dates</th>
                <th title="Days actively logging">*Days</th>
                <th>Bands</th>
                <th>Logs</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @if(Auth::user() && (Auth::user()->admin || $user->call === Auth::user()->call))
            <tr>
                <form method="get" action="{{ $url }}">
                    <td colspan="{{ ($user->pota ? 1 : 0) + ($hidestats ? 2 : 6) }}">
                        <input type="hidden" name="action" value="testgsq">
                        @if($hidestats)
                            <input type="hidden" name="hidestats" value="1">
                        @endif
                        <input type="text" class="test" name="testgsq" value="{{ request('testgsq') ?? '' }}"
                               pattern="^(?:[a-rA-R]{2}[0-9]{2}|[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}|[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}[0-9]{2})$"
                               title="Valid 4, 6 or 8 character grid square">
                        &lt;-- Test a gridsquare here
                        <input style="float:right" type="submit" class="btn b" value="Test">
                    </td>
                </form>
            </tr>
        @endif
        @foreach ($qths as $label => $q)
            @if(!isset($q['gsq']))
                @continue
            @endif

            @php
                $id =    '';
                $class = [];
                $title = [];
                if ($label === 'Test Location') {
                    $class[] =  'testGsq';
                }
                if (!$hidestats && ($user['lastQth'] === $label)) {
                    $id =       'lastQth';
                    $title[] =  'Last location logs were recorded at.';
                }
                if ($q['pota'] && $q['logBands']>=10) {
                    $class[] =  'bandsTen';
                    $title[] =  'Qualifies towards the POTA N1CC Award - ten bands at ten parks.';
                }
            @endphp
            <tr
                @if($id) id="{{ $id }}" @endif
                @if($class) class="{{ implode(' ', $class) }}" @endif
                @if($title) title="{{ implode("\n", $title) }}" @endif
            >
                <td class="gsq" title="Lat: {{ $q['lat'] }}, Lon: {{ $q['lon'] }} - click for Map">
                    <a target="_blank" href="https://k7fry.com/grid/?qth={{ $q['gsq'] }}">{{ $q['gsq'] }}</a>
                </td>
                <td @if(!$hidestats) class="qth" @endif title="{{ $label }}">
                    @if($label === 'Test Location')
                        {{ $label }}
                    @else
                        <a href="{{ route('home') }}/logs/{{ str_replace('/', '-', $user->call) }}/?q[]=myQth|{{ $label }}" target="_blank">{{ $label }}</a>
                    @endif
                </td>
                @if($user->pota)
                    <td>
                        @if($q['pota'])
                            <a href='https://google.com/maps/place/{{ $q['lat'] }},{{ $q['lon'] }}' class='btn o' target='_blank'>Goto</a><a class='btn g' target="_blank" href="https://pota.app/#/park/{{ explode(' ', $label)[1] }}">View</a><a href='#' title="Get Potashell command for this location" class='btn blk' target='_blank' onclick="return copyToClipboard('potashell {{ explode(' ', $label)[1] }} {{ $q['gsq'] }}')">PS</a>
                        @endif
                    </td>
                @endif
                @if(!$hidestats)
                    @if(isset($q['logFirst']))
                        <td class="dates">{{ $q['logFirst'] }}@if($q['logDays'] === 2), {{ $q['logLast'] }}@endif
                            @if($q['logDays'] > 2) - {{ $q['logLast'] }}@endif</td>
                        <td class="r"><strong>{{ $q['logDays'] }}</strong></td>
                        <td class="bandnames"><div class="bandCount">{{ $q['logBands'] }}</div>
                            @foreach(explode(',', $q['logBandNames']) as $band)<span title="{{ explode('|', $band)[1] }} log{{ explode('|', $band)[1]==='1' ? '' : 's' }}" class="band band{{ explode('|', $band)[0] }}">{{ explode('|', $band)[0] }}</span>@endforeach
                        </td>
                        <td class="r"><strong>{{ $q['logs'] }}</strong></td>
                    @else
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    @endif
                @endif
            </tr>
        @endforeach
        @if (!$hidestats && count($qths) > 1)
            <tr class="totals">
                <td><a href="{{ route('summaryMap', ['callsign' => str_replace('/', '-', $user->call)]) }}" title="Show map" target="_blank">Totals</a></td>
                <td{{ ($user->pota ? ' colspan=2' : '') }}><a href="{{ route('home') }}/logs/{{ str_replace('/', '-', $user->call) }}" style="font-weight: normal" target="_blank">({{ count($qths) ===2 ? 'Both' : 'All ' . count($qths) }} locations)</a></td>
                <td class="dates">{{ substr($user['first_log'], 0, 10) }}@if(substr($user['first_log'], 0, 10) !== substr($user['last_log'], 0, 10)) - {{ substr($user['last_log'], 0, 10) }}@endif</td>
                <td class="r"><strong>{{ $user['log_days'] ?: 0 }}</strong></td>
                <td>&nbsp;</td>
                <td class="r"><strong>{{ $user['log_count'] }}</strong></td>
            </tr>
        @endif
        </tbody>
    </table>
    <p><b>*Days</b> means days with recorded logs.</p>
    @if($cta)
        <p class="cta">Share your <a href="https://qrz.com">QRZ.com</a> live logs, maps and stats at <a href="https://logs.classaxe.com" target="_blank">https://logs.classaxe.com</a></p>
    @endif
</div>
