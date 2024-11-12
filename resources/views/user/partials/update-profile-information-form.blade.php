<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('user.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        <input type="hidden" id="id" name="id" value="{{ $user->id }}" />
        <div>
            <x-input-label for="name" class="w-3/12 inline-block" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 w-8/12" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" class="w-3/12 inline-block" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 w-8/12" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('This user\'s email address is unverified.') }}<br>

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="call" class="w-3/12 inline-block" :value="__('* Callsign')" />
            <x-text-input id="call" name="call" type="text" class="mt-1 w-8/12" :value="old('call', $user->call)" required autofocus autocomplete="callsign" />
            <x-input-error class="mt-2" :messages="$errors->get('call')" />
        </div>

        <div>
            <x-input-label for="qth" class="w-3/12 inline-block" :value="__('Address')" />
            <x-text-input id="qth" name="qth" type="text" class="mt-1 w-8/12" :value="old('qth', $user->qth)" autofocus autocomplete="qth" />
            <x-input-error class="mt-2" :messages="$errors->get('qth')" />
        </div>

        <div>
            <x-input-label for="city" class="w-3/12 inline-block" :value="__('* Town / City')" />
            <x-text-input id="city" name="city" type="text" class="mt-1 w-8/12" :value="old('city', $user->city)" required autofocus autocomplete="city" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div>
            <x-input-label for="sp" class="w-3/12 inline-block" :value="__('State / Province')" />
            <x-text-input id="sp" name="sp" type="text" class="mt-1 w-8/12" :value="old('sp', $user->sp)" autofocus autocomplete="sp" />
            <x-input-error class="mt-2" :messages="$errors->get('sp')" />
        </div>

        <div>
            <x-input-label for="itu" class="w-3/12 inline-block" :value="__('* Country')" />
            <x-text-input id="itu" name="itu" type="text" class="mt-1 w-8/12" :value="old('itu', $user->itu)" required autofocus autocomplete="itu" />
            <x-input-error class="mt-2" :messages="$errors->get('itu')" />
        </div>

        <!-- Gridsquare -->
        <div class="mt-4">
            <x-input-label for="gsq" class="w-3/12 inline-block" :value="__('* Maidenhead GSQ (6 or 8 chars)')" />
            <x-text-input id="gsq" class="mt-1 w-8/12" type="text" name="gsq" :value="old('gsq', $user->gsq)" maxlength="8"
              pattern="^(?:[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}|[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}[0-9]{2})$" required autocomplete="gsq" />
            <x-input-error :messages="$errors->get('gsq')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="qth_names" :value="__('Home QTH Names - Format GSQ = Name')" />
            <x-input-label for="qth_names" :value="__('To HIDE all logs for any location, set the name  to HIDE')" />
            <x-textarea id="qth_names" rows="8" class="font-mono block mt-1 w-full" name="qth_names" :value="old('qth_names', $user->qth_names)" autocomplete="qth_names" />
            <x-input-error :messages="$errors->get('qth_names')" class="mt-2" />
        </div>

        <!-- QRZ API Key -->
        <div class="mt-4">
            <x-input-label for="qrz_api_key" :value="__('* QRZ API Key (Format: XXXX-XXXX-XXXX-XXXX)')" />
            <x-text-input id="qrz_api_key" class="block mt-1 w-full" type="text" name="qrz_api_key" :value="old('qrz_api_key', $user->qrz_api_key)"
                          pattern="[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}" length="19" maxlen="19" required />
            <x-input-error :messages="$errors->get('qrz_api_key')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
