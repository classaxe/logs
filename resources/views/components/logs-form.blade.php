<div class="bands mt-6 text-center">
    <h1 style="display: inline-block">Showing logs for <a href="{{ url('/logs', ['callsign' => $user['call']]) }}">{{ $user['call'] }}</a></h1>
    <h2 style="display: inline-block; margin-left: 2em"><strong>{{ $user['name'] }}</strong>, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}}</h2>
    <h3 style="display: inline-block; margin-left: 2em"><span id="logCount">Showing all <b>{{ $user['log_count']}}</b> logs</span> (updated: <span id="logUpdated">{{ $user->getLastQrzPull() }}</span>)</h3><br>
    <fieldset class="logs prevent-select">
        <div class="group">
            <label class="b" title="Hold the SHIFT key while you click to select just that one band" style="cursor: help">
                <span>&#9432;</span>
                Bands:
            </label>
            @foreach($bands as $n => $b)
                @if ($n % 4 === 0 && $n > 0)
        </div>
        <div class="group">
            @endif
            <label class="band band{{ $b }}"><input type="checkbox" name="band" data-band="{{ $b }}" checked>{{ $b }}</label>
            @endforeach
            <label><input type="checkbox" checked class="bandsAll"> All</label>
        </div><br>
        <div class="group">
            <label class="b" title="Hold the SHIFT key while you click to select just that one mode" style="cursor: help">
                <span>&#9432;</span>
                Modes:
            </label>
            @foreach($modes as $m)
                <label class="mode m{{ $m }}"><input type="checkbox" name="mode" data-mode="{{ $m }}" checked>{{ $m }}</label>
            @endforeach
            <label><input type="checkbox" checked class="modesAll"> All</label>
        </div>
        <div class="group">
            <label class="b" style="margin-left: 2em">Confirmed:</label>
            <label><input type="radio" id="conf_Y" name="conf" value="Y">Y</label>
            <label><input type="radio" id="conf_N" name="conf" value="N">N</label>
            <label><input type="radio" id="conf_All" name="conf" value="" checked="checked">All</label>
        </div><br>
        @if(count($qths) > 1)
        <div class="group">
            <label class="b">My QTH
                <select name="myQth">
                    <option></option>
                    @foreach($qths as $label => $count)
                        <option value="{{ $label }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        @endif
        <div class="group">
            <label class="b">Call:
                <input type="text" name="call" size="8" value="">
            </label>
            <label class="b">S/P:
                <input type="text" name="sp" size="2" value="">
            </label>
            <label class="b">ITU:
                <input type="text" name="itu" size="20">
            </label>
            <label class="b">Cont:
                <select name="cont">
                    <option value=""></option>
                    <option value="AF">Africa</option>
                    <option value="AS">Asia</option>
                    <option value="EU">Europe</option>
                    <option value="OC">Oceania</option>
                    <option value="NA">N America</option>
                    <option value="SA">S America</option>
                </select>
            </label>
            <label class="b">GSQ:
                <input type="text" name="gsq" size="4" maxlength="4">
            </label>
        </div><br>
        <div class="group">
            <label class="b">Sort By
                <select name="sortField" style="width: 10em">
                    @foreach($columns as $field => $conf)
                    <option value="{{ $field }}">{{ $conf['lbl'] }}</option>
                    @endforeach
                </select>
            </label>
            <label><input type="checkbox" name="sortZA" value="1" checked="checked"> Z-A</label>
        </div>
        <div class="group" style="margin: 0 2em">
            <label class="b">Compact View:</label>
            <label><input type="radio" id="compact_Y" name="compact" value="Y">Y</label>
            <label><input type="radio" id="compact_N" name="compact" value="N">N</label>
        </div>
        <div class="group">
            <button id='reload' title="Fetches fresh records from QRZ.com" class="ml-5 bg-red-200 hover:bg-red-400 text-red-800 font-semibold px-2 border border-gray-400 rounded shadow">Reload</button>
            <button id='reset' title="Clears form filters and shows all available logs" class="bg-yellow-200 hover:bg-yellow-400 text-yellow-800 font-semibold px-2 border border-gray-400 rounded shadow">Reset</button>
        </div>
    </fieldset>
</div>
