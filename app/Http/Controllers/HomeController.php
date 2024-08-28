<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function update(UserUpdateRequest $request)
    {
        if (!$user = User::find((int)$request->target)) {
            die(404);
        }
        if (!$state = in_array($request->value, ['0','1'])) {
            die(500);
        }
        switch($request->action) {
            case 'setActive':
                $user->active = $request->value;
                $user->save();
                break;
            case 'setAdmin':
                if (Auth::user()->id === (int)$request->target && $request->value === '0') {
                    return Redirect::route('callsigns')->with('status', '<b>Error:</b><br>This would remove your own access.');
                }
                $user->admin = $request->value;
                $user->save();
                break;
            case 'setVisible':
                $user->is_visible = $request->value;
                $user->save();
                break;
            default:
                die(500);
        }
        //return view('callsigns', ['users' => User::getActiveUsers()]);
        return Redirect::route('callsigns')->with('status', '<b>Success</b><br>User profile has been updated.');
    }

    public static function callsigns()
    {
        if (Auth::user() && Auth::user()->admin) {
            $users = User::get();
        } else {
            $users = User::getActiveUsers();
        }
        return view('callsigns', ['users' => $users]);
    }
}
