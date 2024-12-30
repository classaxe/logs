<?php
$pota_v = 0;
$other = 0;
foreach($qths as $name => $qth) {
    $pota_v += $qth['pota'] ? 1 : 0;
    $other +=  $qth['pota'] ? 0 : ($qth['home'] ? 0 : 1);
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
                    @if (count($qths) > 1)
                        <p>@if(count($qths) === 2)Both @else All <b>{{count($qths)}}</b>@endif locations are situated within a radius of
                            <b>{{ ceil($qth_bounds['radius'] / 1000) }} Km</b> ({{ ceil(0.6213712 * ($qth_bounds['radius'] / 1000)) }} Miles)
                            - indicated by the <span style="color:green">green</span> circle.
                        </p>
                    @endif
                    <p>Most QRZ awards require locations used to qualify be within 50 miles radius of a given point
                        - indicated by the <span style="color:red">red</span> circle.</p>

                    <fieldset>
                        <img src="{{ asset('images/blue-pushpin.png') }}" alt="Blue Pushpin" style="display: inline; height: 30px">Home QTH &nbsp;
                        @if ($pota_v)
                            <img src="{{ asset('images/green-pushpin.png') }}" alt="Green Pushpin" style="display: inline; height: 20px">POTA (visited): <span id="count_pota_v">{{ $pota_v }}</span>&nbsp;
                        @endif
                        <img src="{{ asset('images/red-pushpin.png') }}" alt="Red Pushpin" style="display: inline; height: 20px">POTA (unvisited)
                        @if ($other)
                            <img src="{{ asset('images/yellow-pushpin.png') }}" alt="Yellow Pushpin" style="display: inline; height: 20px">Other location: {{ $other }}
                        @endif
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
        var qthBounds = {
            lat: {{ $qth_bounds['center'][0] }},
            lng: {{ $qth_bounds['center'][1] }},
            radius: {{ $qth_bounds['radius'] }}
        }
        var locations = [
@foreach($qths as $name => $qth)
            { days: {{ $qth['logDays'] }}, home: {{ $qth['home'] ? 1 : 0 }}, lat: {{ $qth['lat'] }}, lng: {{ $qth['lon'] }}, gsq: '{{ $qth['gsq'] }}', logs: {{ $qth['logs'] }}, name: "{{ $name }}", pota: "{{ $qth['pota'] }}" },
@endforeach
        ];
        var center = { lat: {{ $qth_bounds['center'][0] }}, lng: {{ $qth_bounds['center'][1] }} }
        window.addEventListener("DOMContentLoaded", () => {
            let script = document.createElement("script");
            script.loading = 'async';
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ getEnv('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=LMap.initLocationsMap";
            document.head.appendChild(script);
        });
    </script>
</x-app-layout>
