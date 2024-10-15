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
                    {{ __("Welcome") }} <strong>{{ Auth::user()->name }}</strong><br>
                    {{ __("You're logged in!") }}
                    <br><br>
                    To embed your log locations in another site, paste this code where you want the information to appear:<br><br>
<textarea style="width: 600px;height: 4em; overflow:hidden;background:#eee;font-family: 'Courier New', monospace;font-weight: bold">
<div id="qthinfo"></div>
<script src="http://logs.classaxe.local/js/qthinfo/{{ Auth::user()->call }}"></script>
</textarea>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
