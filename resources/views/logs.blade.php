<x-app-layout>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

        <div class="bands mt-6 text-center">
            <h1 style="display: inline-block">Showing logs for <a href="{{ url('/logs', ['callsign' => $user['call']]) }}">{{ $user['call'] }}</a></h1>
            <h2 style="display: inline-block; margin-left: 2em"><strong>{{ $user['name'] }}</strong>, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}}</h2>
            <h3 style="display: inline-block; margin-left: 2em">{{ $user['log_count' ]}} logs (updated: {{ \Carbon\Carbon::parse($user['qrz_last_data_pull'])->diffForHumans() }})</h3>
            <br>
            <fieldset class="logs">
                <div class="group">
                <label class="b">Bands:</label>
                @foreach($bands as $n => $b)
                    @if ($n % 4 === 0 && $n > 0)
                        </div>
                        <div class="group">
                    @endif
                    <label class="band band{{ $b }}"><input type="checkbox" name="band" data-band="{{ $b }}" checked>{{ $b }}</label>
                @endforeach
                    <label><input type="checkbox" name="band" checked class="bandsAll"> All</label>
                </div><br>
                <div class="group">
                    <label class="b">Modes:</label>
                    @foreach($modes as $m)
                        <label class="mode m{{ $m }}"><input type="checkbox" name="mode" data-mode="{{ $m }}" checked>{{ $m }}</label>
                    @endforeach
                    <label><input type="checkbox" name="mode" checked class="modesAll"> All</label>
                </div>
                <div class="group">
                    <label class="b" style="margin-left: 2em">Confirmed:</label>
                    <label><input type="radio" id="conf_Y" name="conf" value="Y">Y</label>
                    <label><input type="radio" id="conf_N" name="conf" value="N">N</label>
                    <label><input type="radio" id="conf_All" name="conf" value="" checked="checked">All</label>
                </div><br>
                <label class="b">Call:
                    <input type="text" name="call" size="8">
                </label>
                <label class="b">S/P:
                    <input type="text" name="sp" size="2">
                </label>
                <label class="b">ITU:
                    <input type="text" name="itu" size="20">
                </label>
                <label class="b">GSQ:
                    <input type="text" name="gsq" size="4">
                </label>
                <button id='reset' class="bg-white hover:bg-gray-100 text-gray-800 font-semibold px-2 border border-gray-400 rounded shadow">Reset</button>
            </fieldset>
        </div>
        <p class="text-sm bg-blue-100 border border-gray-500 px-1 py-0.5">
            <strong>Tip:</strong>
            Click on a listed Callsign, Band, Mode, State / Province (SP), Country (ITU) or Gridsquare (GSQ) to filter on that value.
        </p>
    </div>
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
</x-app-layout>
