<?php use App\Models\Log;
$latlon = Log::convertGsqToDegrees($user['gsq']);
?>
<script>
    var msg = {
        gsq: "GSQ",
        heard_in: "Heard In",
        id: "ID",
        itu: "ITU",
        khz: "KHz",
        last_logged: "Last Logged",
        lat_lon: "Lat / Lon",
        logged: "Logged",
        name_qth: "'Name' and Location",
        no: "No",
        power: "Power",
        sec_format: "Secs / Format",
        sidebands: "Sidebands",
        type: "Type",
        yes: "Yes",
    }
    var center = {
        lat: 27.25,
        lon: -88.99985
    }
    var box = [{
        lat: -25.4792,
        lon: -142.458
    }, {
        lat: 79.9792,
        lon: -35.5417
    }];
    var base_image = '/images';
    var base_url = '/';
    var gridColor = '#800000';
    var gridOpacity = 0.35;
    var layers = {
        grid: [],
        squares: []
    };
    var qth = {
        lat: {{ $latlon['lat'] }},
        lng: {{ $latlon['lon'] }},
        gsq: "{{ $user['gsq'] }}",
        call: "{{ $user['call'] }}",
        name: "{{ $user['name'] }}",
        qth: "{{ $user['qth'] }}, {{ $user['city'] }}, {{ $user['sp'] }}, {{ $user['itu'] }}"
    }

    var gsqs = [];
    var markers = [];
</script>
<script>
    window.addEventListener("DOMContentLoaded", function () {
        let script = document.createElement("script");
        script.loading = 'async';
        script.src = "https://maps.googleapis.com/maps/api/js?key={{ getEnv('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=LMap.init";
        document.head.appendChild(script);
    });
</script>
<table class="map map_layout" style="display: none">
    <tbody style="background: transparent">
    <tr>
        <td>
            <div class="scroll">
                <div id="scrollablelist" style="height: 473px;">
                    <table id="gsqs" class="results">
                        <thead>
                        <tr>
                            <th class="sort sorted" title="Sort by GSQ" style="width: 3.25em">GSQ</th>
                            <th class="sort" title="Sort by Bands">Bands</th>
                            <th class="sort txt_vertical" title="Sort by Logs"><div>Logs</div></th>
                            <th class="sort txt_vertical" title="Sort by Calls"><div>Calls</div></th>
                            <th class="sort txt_vertical" title="Sort by Confirmed Status"><div>Conf</div></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </td>
        <td class="map">
            <div id="header">
                <div class="form_layers">
                    <div>
                        <label>
                            <strong>Show</strong>
                        </label>
                    </div>
                    <div>
                        <label title="Show Maidenhead Locator Grid Squares">
                            <input type="checkbox" id="layer_grid" checked="checked">
                            Grid
                        </label>
                    </div>
                    <div>
                        <label title="Show Daytime / Nighttime">
                            <input type="checkbox" id="layer_night" checked="checked">
                            Night
                        </label>
                    </div>
                    <div>
                        <label title="Show Gridsquares">
                            <input type="checkbox" id="layer_squares" checked="checked">
                            Squares
                        </label>
                    </div>
                    <div>
                        <label title="Show QTH">
                            <input type="checkbox" id="layer_qth" checked="checked">
                            QTH
                        </label>
                    </div>
                </div>
            </div>
            <div id="map" style="height: 1000px;">Loading...</div>
        </td>
    </tr>
    </tbody>
</table>
