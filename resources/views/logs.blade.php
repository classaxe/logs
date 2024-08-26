<x-app-layout>
@vite([
    'resources/js/logs.js'
])
<script src="/js/nite-overlay/nite-overlay.js"></script>
<script src="/js/lmap.js"></script>
<script src="/js/cookies.js"></script>
<script>
    var callsign = "{{ $user['call'] }}";
    document.body.classList.add("loading");
</script>
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
