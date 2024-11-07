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
        $callsign = str_replace('-', '/', $callsign);
        if (!$u = User::getUserDataByCallsign($callsign)) {
            return redirect(url('/'));
        }
        switch ($mode) {
            case 'summary':
                switch ($method) {
                    case 'iframe':
                        $hidestats = $request->query('hidestats') ? '1' : '';
                        $title = sprintf("Location%s %s for %s - %s",
                            (count($u['qths']) > 1 ? 's' : ''),
                            ($hidestats ? "" : " and Stats"),
                            $u['user']->name,
                            $u['user']->call
                        );
                        $url = route(
                            'embed', [
                                'mode' => 'summary',
                                'method' => 'iframe',
                                'callsign' => str_replace('/', '-', $u['user']->call)
                            ]
                        );

                        return view('user.summary.iframe', [
                            'cta' =>        true,
                            'hidestats' =>  $hidestats,
                            'qths' =>       $u['qths'],
                            'qth_bounds' => $u['qth_bounds'],
                            'title' =>      $title,
                            'url' =>        $url,
                            'user' =>       $u['user'],
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
                        $contents = view('user.summary.js', [
                            'qths' =>       $u['qths'],
                            'user' =>       $u['user']
                        ]);
                        return  response($contents)->header('Content-Type', 'application/javascript');
                }
            break;
        }
    }

    public function summary(Request $request, string $callsign) {
        $testGsq = $request->query('testgsq') ?: null;
        $callsign = str_replace('-', '/', $callsign);
        if (!$u = User::getUserDataByCallsign($callsign, $testGsq)) {
            return redirect(url('/'));
        }
        $hidestats = $request->query('hidestats') ? '1' : '';
        $title = sprintf("Location%s %s for %s - %s",
            (count($u['qths']) > 1 ? 's' : ''),
            ($hidestats ? "" : " and Stats"),
            $u['user']->name,
            $u['user']->call
        );
        $url = route(
            'summary', [
                'callsign' => str_replace('/', '-', $u['user']->call)
            ]
        );
        return view('user.summary.index', [
            'cta' =>        false,
            'hidestats' =>  $hidestats,
            'qths' =>       $u['qths'],
            'qth_bounds' => $u['qth_bounds'],
            'title' =>      $title,
            'url' =>        $url,
            'user' =>       $u['user'],
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
