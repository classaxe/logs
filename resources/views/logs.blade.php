<x-app-layout>
    <script>
        var callsign = "{{ $user['call'] }}";
    </script>
    @include('components.logs-form')
    @include('components.logs-table')
</x-app-layout>
