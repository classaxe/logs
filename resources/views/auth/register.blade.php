<x-app-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style="margin: 0 auto">
        <h1>Registration Form</h1>
        <p>Fields marked * are required.</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mt-4">
                <x-input-label for="name" :value="__('* Name')"/>
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('* Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Callsign -->
            <div class="mt-4">
                <x-input-label for="call" :value="__('* Callsign')" />
                <x-text-input id="call" class="block mt-1 w-full" type="text" name="call" :value="old('call')" required autocomplete="callsign" />
                <x-input-error :messages="$errors->get('call')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="qth" :value="__('Address')" />
                <x-text-input id="qth" name="qth" type="text" class="mt-1 block w-full" :value="old('qth')" autofocus autocomplete="qth" />
                <x-input-error class="mt-2" :messages="$errors->get('qth')" />
            </div>

            <div class="mt-4">
                <x-input-label for="city" :value="__('* Town / City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" required autocomplete="city" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <div class="mt-4">
                <x-input-label for="sp" :value="__('State / Province (2-character code)')" />
                <x-text-input id="sp" name="sp" type="text" class="mt-1 block w-full" :value="old('sp')" maxlength="2" pattern="[A-Z]{2}*" autocomplete="sp" />
                <x-input-error class="mt-2" :messages="$errors->get('sp')" />
            </div>

            <div class="mt-4">
                <x-input-label for="itu" :value="__('* Country (3-character ISO format)')" />
                <x-text-input id="itu" name="itu" type="text" class="mt-1 block w-full" :value="old('itu')" maxlength="3" pattern="[A-Z]{3}" required
                  autocomplete="itu" />
                <x-input-error class="mt-2" :messages="$errors->get('itu')" />
            </div>

            <!-- Gridsquare -->
            <div class="mt-4">
                <x-input-label for="gsq" :value="__('* Gridsquare  (Format AB12cd)')" />
                <x-text-input id="gsq" class="block mt-1 w-full" type="text" name="gsq" size="6" maxlength="6" :value="old('gsq')"
                  pattern="[A-R]{2}[0-9]{2}[a-x]{2}" required autocomplete="gsq" />
                <x-input-error :messages="$errors->get('gsq')" class="mt-2" />
            </div>

            <!-- QRZ API Key -->
            <div class="mt-4">
                <x-input-label for="qrz_api_key" :value="__('* QRZ API Key (Format: XXXX-XXXX-XXXX-XXXX)')" />
                <x-text-input id="qrz_api_key" class="block mt-1 w-full" type="text" name="qrz_api_key" :value="old('qrz_api_key')"
                  pattern="[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}" length="19" maxlength="19" required />
                <x-input-error :messages="$errors->get('qrz_api_key')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('* Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('* Confirm Password')" />

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
