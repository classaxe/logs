<x-app-layout>
    <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md xoverflow-hidden sm:rounded-lg">
        <ul class="callsigns">
        @foreach($users as $u)
            <li><a href="{{ url('/callsign', ['callsign' => $u['call']]) }}">{{ $u['call'] }}</a>
                {{ $u['name'] }}, {{ $u['gsq'] }} {{ $u['sp'] }} {{ $u['itu' ]}} ({{ $u['log_count' ]}} logs)
            </li>
        @endforeach
        </ul>
    </div>
</x-app-layout>
