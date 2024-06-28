<x-app-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Callsign -->
            <div class="mt-4">
                <x-input-label for="call" :value="__('Callsign')" />
                <x-text-input id="call" class="block mt-1 w-full" type="text" name="call" :value="old('call')" required autocomplete="callsign" />
                <x-input-error :messages="$errors->get('call')" class="mt-2" />
            </div>

            <!-- Gridsquare -->
            <div class="mt-4">
                <x-input-label for="gsq" :value="__('Gridsquare')" />
                <x-text-input id="gsq" class="block mt-1 w-full" type="text" name="gsq" :value="old('gsq')" required autocomplete="gsq" />
                <x-input-error :messages="$errors->get('gsq')" class="mt-2" />
            </div>

            <!-- QRZ API Key -->
            <div class="mt-4">
                <x-input-label for="qrz_api_key" :value="__('QRZ API Key')" />
                <x-text-input id="qrz_api_key" class="block mt-1 w-full" type="text" name="qrz_api_key" :value="old('qrz_api_key')" required />
                <x-input-error :messages="$errors->get('qrz_api_key')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ml-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
