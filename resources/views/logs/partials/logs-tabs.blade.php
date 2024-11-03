<div id="tabs">
    <div style="text-align: center">
        <a title="Listing" class="button icon is-active float-left" id="show_list">
            <img class="tabicon" src="/images/icon_list.png" alt="list">
            <span class="tabtext">Listing</span>
        </a>
        <a title="Map" class="button icon is-inactive float-left" id="show_map">
            <img class="tabicon" src="/images/icon_map.png" alt="map">
            <span class="tabtext">Map</span>
        </a>
        <a title="Stats" class="button icon is-inactive float-left" id="show_stats">
            <img class="tabicon" src="/images/icon_stats.png" alt="stats">
            <span class="tabtext">Stats</span>
        </a>
        <h1 style="display: inline-block">Showing logs for <a href="{{ url('/logs', ['callsign' => str_replace('/', '-', $user['call'])]) }}">{{ $user['call'] }}</a></h1>
        <h2 style="display: inline-block; margin-left: 2em"><strong>{{ $user['name'] }}</strong>, {{ $user['gsq'] }} {{ $user['sp'] }} {{ $user['itu' ]}}</h2>
        <h3 style="display: inline-block; margin-left: 2em"><span id="logCount">Showing all <b>{{ $user['log_count']}}</b> logs</span> (updated: <span id="logUpdated">{{ $user->getLastQrzPull() }}</span>)</h3><br>
    </div>
</div>
