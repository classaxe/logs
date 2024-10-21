<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('This action will remove your profile and all your logs stored on this server.') }}
        </p>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Your data at QRZ.com will be unaffected, and you can recreate an account here again at a later date if you wish.') }}
        </p>
    </header>
    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <x-danger-button class="p-0" onclick="return confirm('Are you sure you wish to delete your profile and logs?')">{{ __('Delete Profile') }}</x-danger-button>
    </form>
</section>
