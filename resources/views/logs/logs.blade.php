<x-app-layout>
@vite([
    'resources/js/logs.js'
])

<script src="/js/lmap.js?v={{ exec('git describe --tags') }}"></script>
<script>
var callsign = "{{ $user['call'] }}";
<?php
$presets = [];
if ($_GET['presets']??[]) {
    foreach ($_GET['presets'] as $preset) {
        if (!$preset) {
            continue;
        }
        $keyval = explode('|', $preset);
        if (count($keyval) === 2) {
            $presets[] = $keyval[0] . ": '" . $keyval[1] . "'";
        }
    }
}
?>
var presets = {
    {!! implode(",\n    ", $presets) !!}
};
document.body.classList.add("loading");
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
    @include('logs.partials.logs-stats')
    @include('logs.partials.logs-tips')
</div>
@include('logs.partials.logs-tabs')
<div id="content">
    @include('logs.partials.logs-list')
    @include('logs.partials.logs-map')
</div>
</x-app-layout>
