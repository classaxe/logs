<x-app-layout>
    <?php ob_start() ?>@vite(['resources/js/logs.js'])<?php echo str_replace(' /><', ">\n        <", ob_get_clean()) ?>

    <script src="/js/lmap.js?v={{ exec('git describe --tags') }}"></script>
    <script>
        var callsign = "{{ $user['call'] }}";
        var base_image = '/images';
        var box = [{}, {}];
        var center = {}
        var gridColor = '#800000';
        var gridOpacity = 0.35;
        var gsqs = [];
        var layers = {
            grid: [],
            squares: [],
            squareLabels: []
        };
        var qth = {
            lat: {{ $user['lat'] }},
            lng: {{ $user['lon'] }},
            gsq: "{{ $user['gsq'] }}",
            call: "{{ $user['call'] }}",
            name: "{{ $user['name'] }}",
            loc: "{{ $user['qth'] }}, {{ $user['city'] }}, {{ $user['sp'] }}, {{ $user['itu'] }}"
        }
        var presets = {
            {!! implode(",\n    ", $presets) !!}
        };
        document.body.classList.add("loading");

        window.addEventListener("DOMContentLoaded", () => {
            let script = document.createElement("script");
            script.loading = 'async';
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ getEnv('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=LMap.init";
            document.head.appendChild(script);
        });
    </script>
    @if($user['qth_count'] < 2)
        <style>
            .multi-qth {
                display: none !important;
            }
        </style>
    @endif
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        @include('logs.partials.logs-form')
        @include('logs.partials.logs-summary')
        @include('logs.partials.logs-tips')
    </div>
    @include('logs.partials.logs-tabs')
    <div id="content">
        @include('logs.partials.logs-list')
        @include('logs.partials.logs-map')
        @include('logs.partials.logs-stats')
    </div>
</x-app-layout>
