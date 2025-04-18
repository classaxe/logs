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
            'qth' => ['nullable', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:40'],
            'sp' => ['nullable', 'string', 'size:2'],
            'itu' => ['required', 'string', 'size:3'],
            'gsq' => ['required', 'string', 'regex:/^(?:[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}|[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}[0-9]{2})$/'],
            'qrz_api_key' => ['required', 'string', 'min:19, max:19'],
            'clublog_email' => ['nullable', 'string'],
            'clublog_password' => ['nullable', 'string'],
            'clublog_call' => ['nullable', 'string'],
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
            'qth_names' => '',
            'qrz_api_key' => $request->qrz_api_key,
            'password' => Hash::make($request->password),
            'clublog_email' => $request->clublog_email,
            'clublog_password' => $request->clublog_password,
            'clublog_call' => $request->clublog_call,

            // For now make all users visible
            'is_visible' => 1
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
