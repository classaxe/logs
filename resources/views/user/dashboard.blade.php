<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>{{ __("Welcome") }} <strong>{{ Auth::user()->name }}</strong>.</p>
                    <p>Here's how you can embed your live information from this site in other websites</p>
                    <h2 style="margin-top: 1em">Summary information</h2>
                    <ol>
                        <li><b>Javascript Embedding</b>
                            <p>Note that this method <strong>does not work for QRZ profile pages</strong>, since script tags are not allowed.</p>
                            <textarea style="width: 1000px;height: 4em; overflow:hidden;background:#eee;font-family: 'Courier New', monospace;font-weight: bold">
<h2>Location and Stats for {{ $user->name }} - {{ $user->call }}</h2>
<div id="qthinfo"></div>
<script src="{{ route('embed', ['method' => 'js', 'mode' => 'summary', 'callsign' =>Auth::user()->call]) }}"></script>
</textarea>
                        </li>
                        <li><b>IFRAME Embedding</b><br>
                            <textarea style="width: 1000px;height: 2.5em; overflow:hidden;background:#eee;font-family: 'Courier New', monospace;font-weight: bold">
<iframe src="{{ route('embed', ['method' => 'iframe', 'mode' => 'summary', 'callsign' => $user->call]) }}" title="Live logs for {{ $user->call }}">
</textarea>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
