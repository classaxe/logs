<?php
$isMain = strpos($user['call'], '/') === false;
$home = 0;
$pota_v = 0;
$pota_10 = 0;
$other = 0;
$locations = 0;

$alt_home = 0;
$alt_pota_v = 0;
$alt_pota_10 = 0;
$alt_other = 0;
$alt_locations = 0;
foreach ($qths as $name => $qth) {
    if ($qth['call'] === $user->call) {
        $home += $qth['home'] ? 1 : 0;
        $locations += 1;
        $pota_v += $qth['pota'] ? 1 : 0;
        $pota_10 += $qth['pota'] && $qth['logBands'] >= 10 ? 1 : 0;
        $other += !$qth['pota'] && !$qth['home'] ? 1 : 0;
    } else {
        $alt_home += $qth['home'] ? 1 : 0;
        $alt_locations += 1;
        $alt_pota_v += $qth['pota'] ? 1 : 0;
        $alt_pota_10 += $qth['pota'] && $qth['logBands'] >= 10 ? 1 : 0;
        $alt_other += !$qth['pota'] && !$qth['home'] ? 1 : 0;
    }
}
?>
<x-app-layout>
    @vite([
        'resources/css/summary.css'
    ])
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{!! $title !!}</h2>
                    @if ($isMain && $locations > 1)
                        <p>@if($locations === 2)
                                Both
                            @else
                                All <b>{{ $locations }}</b>
                            @endif locations
                            @if($pota_v)
                                - including <b>{{ $pota_v }}</b> <a class="url" target="_blank"
                                                                    href="https://pota.app/#/profile/{{ explode('/',$user->call)[0] }}">POTA
                                    Park{{ $pota_v > 1 ? 's' : '' }}</a> -
                            @endif
                            are situated within a radius of
                            <b>{{ round($qth_bounds['radius'] / 1000, 1) }} Km</b>
                            ({{ round(0.6213712 * ($qth_bounds['radius'] / 1000),1) }} Miles)
                            - indicated by the <span style="color:green">green</span> circle.
                            @if($pota_10)
                                <br>{!! $pota_10 === 1 ? '<b>One</b> park' : 'A total of <b>' . $pota_10 . '</b> parks' !!}
                                included logs on 10 or more bands, qualifying towards the <a
                                    href="https://docs.pota.app/docs/awards.html#james-f-laporta-n1cc-awards"
                                    target="_blank" class="url">POTA N1CC Award</a>.
                            @endif
                        </p>
                    @endif
                    @if($isMain)
                        <p>Most QRZ awards require locations used to qualify be within 50 miles radius of a given point
                            - indicated by the <span style="color:red">red</span> circle.</p>
                    @endif
                    <fieldset>
                        @if($home)
                        <img src="{{ asset('images/blue-pushpin.png') }}" alt="Blue Pushpin"
                             style="display: inline; height: 30px">Home QTH &nbsp;
                        @endif
                        @if ($pota_v)
                            <img src="{{ asset('images/green-pushpin.png') }}" alt="Green Pushpin"
                                 style="display: inline; height: 20px">POTA Park with 1-9 bands: <b
                                id="count_pota_v">{{ $pota_v - $pota_10 }}</b>&nbsp;
                            @if ($pota_10)
                                <img src="{{ asset('images/lightgreen-pushpin.png') }}" alt="Light Green Pushpin"
                                     style="display: inline; height: 20px">POTA Park with 10 bands: <b
                                    id="count_pota_10">{{ $pota_10 }}</b>&nbsp;
                            @endif
                        @endif
                        @if ($other)
                            <img src="{{ asset('images/yellow-pushpin.png') }}" alt="Yellow Pushpin"
                                 style="display: inline; height: 20px">Other location: <b
                                id="count_other">{{ $other }}</b>
                        @endif
                        @if ($alt_locations)
                            <img src="{{ asset('images/grey-pushpin.png') }}" alt="Grey Pushpin"
                                 style="display: inline; height: 20px">Other Callsign: <b
                                id="count_alt_location">{{ $alt_locations }}</b>
                        @endif
                            <img src="{{ asset('images/red-pushpin.png') }}" alt="Red Pushpin"
                                 style="display: inline; height: 20px">POTA (unvisited)
                        <span id="currentLocation" style="display: none">
                            <a class="url" href="#" id="btnCurrent" title="Click to show your current location">
                                <img src="{{ asset('images/purple-pushpin.png') }}"
                                     style="display: inline; height: 20px">Your Location</a>:
                            <input type="text" id="currentGsq">
                        </span>
                    </fieldset>
                    <div id="map" style="height: 600px;">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/lmap.js?v={{ exec('git describe --tags') }}"></script>
    <script>
        var callsign = "{{ $user['call'] }}";
        var base_image = '/images';
        var bounds = [];
        var box = [{}, {}];
        var gridColor = '#ff0000';
        var gridOpacity = 0.75;
        var gsqs = [];
        var layers = {
            grid: [],
            locs: [],
            potaU: [],
            potaV: [],
            squares: [],
            squareLabels: []
        };
        var logDates = ['{{ substr($user->first_log, 0, 10) }}', '{{ substr($user->last_log, 0, 10) }}'];
        var qth = {
            call: "{{ $user['call'] }}",
            gsq: "{{ $user['gsq'] }}",
            loc: "{{ $user['qth'] }}, {{ $user['city'] }}, {{ $user['sp'] }}, {{ $user['itu'] }}",
            lat: {{ $user['lat'] }},
            lng: {{ $user['lon'] }},
            name: "{{ $user['name'] }}",
        }
        @if($isMain)
        var drawRing = true;
        @else
        var drawRing = false;
        @endif
        var qthBounds = {
            lat: {{ $qth_bounds['center'][0] }},
            lng: {{ $qth_bounds['center'][1] }},
            radius: {{ $qth_bounds['radius'] }}
        }
        var locations = [
                @foreach($qths as $name => $qth)
            {
                name: "{{ $name
            }}", pota: "{{ $qth['pota']
            }}", home: {{ $qth['home'] ? 1 : 0
            }}, primary: {{ $qth['call'] === $user['call'] ? 1 : 0
            }}, lat: {{ $qth['lat']
            }}, lng: {{ $qth['lon']
            }}, gsq: '{{ $qth['gsq']
            }}', days: {{ $qth['logDays']
            }}, logs: {{ $qth['logs']
            }}, logBands: {{ $qth['logBands']
            }}, logBandNames: "{{ $qth['logBandNames']
            }}"
            },
            @endforeach
        ];
        var center = {lat: {{ $qth_bounds['center'][0] }}, lng: {{ $qth_bounds['center'][1] }}}
        window.addEventListener("DOMContentLoaded", () => {
            let script = document.createElement("script");
            script.loading = 'async';
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ getEnv('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=LMap.initLocationsMap";
            document.head.appendChild(script);
        });
    </script>
</x-app-layout>
