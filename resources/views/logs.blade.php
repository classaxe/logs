<x-app-layout>
@vite([
    'resources/js/logs.js'
])

<script src="/js/lmap.js?v={{ exec('git describe --tags') }}"></script>
<script>
    var callsign = "{{ $user['call'] }}";
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
    @include('components.logs-form')
    @include('components.logs-stats')
    @include('components.logs-tips')
</div>
@include('components.logs-tabs')
<div id="content">
    @include('components.logs-list')
    @include('components.logs-map')
</div>
</x-app-layout>
