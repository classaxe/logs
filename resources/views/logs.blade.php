<x-app-layout>
<script>
    var callsign = "{{ $user['call'] }}";
    document.body.classList.add("loading");
</script>
    @include('components.logs-form')
    @include('components.logs-stats')
    @include('components.logs-tips')
    @include('components.logs-tabs')
<div id="content">
    @include('components.logs-list')
    @include('components.logs-map')
</div>
</x-app-layout>
