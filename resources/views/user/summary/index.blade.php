<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Summary') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <iframe src="{{ route('embed', ['method' => 'iframe', 'mode' => 'summary', 'callsign' => str_replace('/', '-', Auth::user()->call)]) }}" title="Live logs for {{ $user->call }}"
                            style="width:740px; height: 360px; border:none; margin: auto;"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
