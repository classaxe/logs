<x-app-layout>
    @vite([
        'resources/css/summary.css'
    ])
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
                    <fieldset>
                        <img src="{{ asset('images/blue-pushpin.png') }}" alt="Blue Pushpin" style="display: inline; height: 30px">Home QTH &nbsp;
                        <img src="{{ asset('images/green-pushpin.png') }}" alt="Green Pushpin" style="display: inline; height: 20px">POTA (visited) &nbsp;
                        <img src="{{ asset('images/red-pushpin.png') }}" alt="Red Pushpin" style="display: inline; height: 20px">POTA (unvisited) &nbsp;
                        <img src="{{ asset('images/yellow-pushpin.png') }}" alt="Yellow Pushpin" style="display: inline; height: 20px">Other location
                    </fieldset>
                    <div id="map" style="height: 1000px;">Loading...</div>
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
        var gridColor = '#800000';
        var gridOpacity = 0.35;
        var gsqs = [];
        var layers = {
            grid: [],
            locations: [],
            pota: [],
            squares: [],
            squareLabels: []
        };
        var logDates = ['{{ substr($user->first_log, 0, 10) }}', '{{ substr($user->last_log, 0, 10) }}'];
        var qth = {
            lat: {{ $user['lat'] }},
            lng: {{ $user['lon'] }},
            gsq: "{{ $user['gsq'] }}",
            call: "{{ $user['call'] }}",
            name: "{{ $user['name'] }}",
            loc: "{{ $user['qth'] }}, {{ $user['city'] }}, {{ $user['sp'] }}, {{ $user['itu'] }}"
        }
        var locations = [
@foreach($qths as $name => $qth)
            { name: "{{ $name }}", lat: {{ $qth['lat'] }}, lng: {{ $qth['lon'] }}, pota: "{{ $qth['pota'] }}" },
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
