<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'call' => ['required', 'string', 'max:12'],
            'gsq' => ['required', 'string', 'max:8'],
            'qth' => ['nullable', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:40'],
            'sp' => ['required', 'string', 'max:2'],
            'itu' => ['required', 'string', 'max:3'],
            'qrz_api_key' => ['required', 'string', 'min:19, max:19'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'call' => $request->call,
            'gsq' => $request->gsq,
            'qth' => $request->qth,
            'city' => $request->city,
            'sp' => $request->sp,
            'itu' => $request->itu,
            'qrz_api_key' => $request->qrz_api_key,
            'password' => Hash::make($request->password),

            // For now make all users visible
            'is_visible' => 1
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
