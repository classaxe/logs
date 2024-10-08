<x-app-layout>
    <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md sm:rounded-lg" style="margin: 0 auto">
        <div class="mb-4 text-sm text-gray-600">
            <h1>Forgotten Password?</h1>
            <p>
                {{ __('Let us know your email address and we will') }}<br>
                {{ __('email you a password reset link.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
