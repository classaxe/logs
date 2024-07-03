<x-app-layout>
    <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style="margin: 1em auto">
        <ul class="callsigns">
        @foreach($users as $u)
            <li><a href="{{ url('/logs', ['callsign' => $u['call']]) }}">{{ $u['call'] }}</a>
                <div class="group">{{ $u['name'] }} {{ $u['gsq'] }}</div>
                <div class="group">{{ $u['city'] }}, {{ $u['sp'] }} {{ $u['itu' ]}} ({{ $u['log_count' ]}} logs)</div>
            </li>
        @endforeach
        </ul>
    </div>
</x-app-layout>
