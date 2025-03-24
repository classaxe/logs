<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function view(Request $request): View
    {
        $params = [
            'user' => Auth::user(),
            'urlIframe' => route('embed', [
                'method' => 'iframe',
                'mode' => 'summary',
                'callsign' => str_replace('/', '-', Auth::user()->call)
            ]),
            'urlJs' => route('embed', [
                'method' => 'js',
                'mode' => 'summary',
                'callsign' => str_replace('/', '-', Auth::user()->call)
            ])
        ];
        return view('user.dashboard', $params);
    }

}
