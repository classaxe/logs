<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPatchRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    public function edit($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();
        return view('user.edit', ['user' => $user]);

    }

    public function summary(string $callsign): View
    {
        if (!User::getUserByCallsign($callsign)) {
            return redirect(url('/'));
        }
        return view('user.summary', [
            'callsign' =>  $callsign
        ]);
    }


    public function userJs(string $mode, string $callsign) {
        $data = User::getUserDataByCallsign($callsign);

        $contents = view('user/js/qths', [
            'qths' =>       $data['qths'],
            'user' =>       $data['user']
        ]);
        return  response($contents)->header('Content-Type', 'application/javascript');
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
            case 'purgeLogs':
                $user->first_log = null;
                $user->last_log = null;
                $user->qrz_last_data_pull = null;
                $user->qrz_last_data_pull_debug = '';
                $user->qrz_last_result = null;
                $user->qth_count = 0;
                $user->log_count = 0;
                $user->save();
                Log::deleteLogsForUser($user);
                return Redirect::route('home')
                    ->with('status', sprintf('<b>Success</b><br>Logs for <i>%s</i> have been purged.', $user->name));
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
