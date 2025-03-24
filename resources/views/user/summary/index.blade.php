<x-app-layout>
    @vite([
        'resources/css/summary.css'
    ])
    <script>
        function copyToClipboard(text) {
            console.log(text);
            var temp = $("<textarea>");
            $("body").append(temp);
            temp.val(text).select();
            document.execCommand("copy");
            temp.remove();
            return false;
        }
    </script>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Summary') }}</h2>
                    @include('user.summary.content')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
