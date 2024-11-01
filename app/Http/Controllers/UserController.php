<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPatchRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Image;
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

    public function embed(Request $request, string $mode, string $method, string $callsign)
    {
        if (!$u = User::getUserByCallsign($callsign)) {
            return redirect(url('/'));
        }
        switch ($mode) {
            case 'summary':
                switch ($method) {
                    case 'iframe':
                        if (!User::getUserByCallsign($callsign)) {
                            return redirect(url('/'));
                        }
                        $data = User::getUserDataByCallsign($callsign);
                        return view('user.summary.iframe', [
                            'qths' =>       $data['qths'],
                            'user' =>       $data['user'],
                            'hidestats' =>  $request->query('hidestats') ? '1' : '',
                        ]);
                    case 'img':
                        $Image = new Image();
                        $labels = [
                            [
                                'text' => sprintf("Locations and Stats for %s - %s", $u->name, $u->call),
                                'font' => 'arial.ttf',
                                'size' => 12,
                                'color' => 'black',
                                'ypos' => -5
                            ],
                            [
                                'text' => "Click the links below to view live logs and an interactive gridsquares map.",
                                'font' => 'arial.ttf',
                                'size' => 12,
                                'color' => 'blue',
                                'y' => 30
                            ]
                        ];
                        $padding = 10;
                        $totalWidth = 0;
                        $totalHeight = 0;
                        foreach ($labels as &$l) {
                            $l['box'] = $Image->getTextSize($l['size'], 0, $l['font'], $l['text']);
                            $totalWidth = max($totalWidth, $l['box']['width']);
                        }

                        $Image->ImageMake($totalWidth + $padding, $totalHeight + $padding);
                        $pad_v = 0;
                        foreach ($labels as &$l) {
                            $pad_v += $l['pad_v'];
                            $Image->ImageDrawText(
                                $l['size'],
                                0,
                                $l['box']['left'],
                                $l['box']['top'] + $pad_v,
                                $l['box']['width'],
                                $l['box']['height'],
                                $l['color'],
                                $l['font'],
                                $l['text']
                            );
                        }
                        $Image->ImageRender('png');
                        die();

                    case 'js':
                        $data = User::getUserDataByCallsign($callsign);
                        $contents = view('user.summary.js', [
                            'qths' =>       $data['qths'],
                            'user' =>       $data['user']
                        ]);
                        return  response($contents)->header('Content-Type', 'application/javascript');
                }
            break;
        }
    }

    public function summary(string $callsign) {
        if (!$u = User::getUserByCallsign($callsign)) {
            return redirect(url('/'));
        }
        return view('user.summary.index', [
            'callsign' => $callsign,
            'user' => $u
        ]);
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
