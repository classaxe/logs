<div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="bands mt-6 text-center">
        <h1 style="display: inline-block">Showing logs for <a href="{{ url('/logs', ['callsign' => $user['call']]) }}">{{ $user['call'] }}</a></h1>
        <h2 style="display: inline-block; margin-left: 2em"><strong>{{ $user['name'] }}</strong>, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}}</h2>
        <h3 style="display: inline-block; margin-left: 2em">{{ $user['log_count' ]}} logs (updated: {{ \Carbon\Carbon::parse($user['qrz_last_data_pull'])->diffForHumans() }})</h3><br>
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
                <input type="text" name="call" size="8" value="">
            </label>
            <label class="b">S/P:
                <input type="text" name="sp" size="2" value="">
            </label>
            <label class="b">ITU:
                <input type="text" name="itu" size="20">
            </label>
            <label class="b">GSQ:
                <input type="text" name="gsq" size="4">
            </label>
            <button id='reset' title="Clears form and shows all logs" class="ml-5 bg-red-200 hover:bg-gray-100 text-gray-800 font-semibold px-2 border border-gray-400 rounded shadow">Reset</button>
            <button id='submit' title="Filters on form criteria" class="bg-green-200 hover:bg-gray-100 text-green-800 font-semibold px-2 border border-gray-400 rounded shadow">Submit</button>
        </fieldset>
    </div>
    <p class="text-sm bg-blue-100 border border-gray-500 px-1 py-0.5">
        <strong>Tip:</strong>
        Click on a listed Callsign, Band, Mode, State / Province (SP), Country (ITU) or Gridsquare (GSQ) to filter on that value.
    </p>
</div>
