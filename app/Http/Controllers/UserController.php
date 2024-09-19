<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPatchRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function edit($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();
        return view('user.edit', ['user' => $user]);

    }

    public function update(UserUpdateRequest $request) {
        $validated = $request->validated();
        $user = User::find($validated['id']);
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        if ($user->qth_names === null) {
            $user->qth_names = '';
        }
        $user->save();
        return Redirect::route('home')
            ->with('status', sprintf('<b>Success</b><br>Profile for <i>%s</i> has been updated.', $user->name));
    }

    public function patch(UserPatchRequest $request)
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
                    return Redirect::route('home')
                        ->with('status', '<b>Error:</b><br>This would remove your own access.');
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
        return Redirect::route('home')
            ->with('status', sprintf('<b>Success</b><br>Status for <i>%s</i> has been updated.', $user->name));
    }
}
