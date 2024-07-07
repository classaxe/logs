<x-app-layout>
<script>
    var callsign = "{{ $user['call'] }}";
    document.body.classList.add("loading");
</script>
    @include('components.logs-form')
    @include('components.logs-table')
</x-app-layout>
