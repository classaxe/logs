@php
/* @var $qths */
/* @var $hidestats */
/* @var $user */

$title = sprintf(
    "Location%s %s for %s - %s",
    (count($qths) > 1 ? 's' : ''),
    ($hidestats ? "" : " and Stats"),
    $user->name,
    $user->call
);
$url = route('embed', ['mode' => 'summary', 'method' => 'iframe', 'callsign' => str_replace('/', '-', $user->call)])
@endphp
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        #qthinfo h2 {
            font-size: 1em;
        }
        #qthinfo h2,
        #qthinfo p {
            text-align: center;
            margin: 0.25em auto;
            font-family: Figtree,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji"
        }
        #qthinfo a.btn {
            margin: 0 0 0.25em 0.25em;
            font-size: 70%;
            color: white;
            text-decoration: none;
            padding: 0.25em;
            border-radius: 0.5em;
        }
        #qthinfo a.btn.r {
            background: #a44;
        }
        #qthinfo a.btn.g {
            background: #484;
        }
        #qthinfo a.btn.b {
            background: #44f;
        }
        #qthinfo a.btn:hover {
            color: yellow;
        }
        #qthinfo a.btn.r:hover {
            background: #f44;
        }
        #qthinfo a.btn.g:hover {
            background: #686;
        }
        #qthinfo a.btn.b:hover {
            background: #88f;
        }
        #qthinfo table {
            border-collapse: collapse;
            margin: 0 auto;
        }
        #qthinfo table thead th {
            background: #888;
            color: #fff;
            font-family: Figtree,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji"
        }
        #qthinfo table tbody td {
            font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace
        }
        #qthinfo table tbody tr.totals td {
            font-weight: bold;
            text-align: left;
            background: #eee;
            color: #000;
        }
        #qthinfo table th,
        #qthinfo table td {
            color: #444;
            border: 1px solid #444;
            padding: 4px 5px;
            vertical-align: top;
        }
        #qthinfo table tbody td.r {
            text-align: right !important;
        }
        #qthinfo table td a {
            text-decoration: none;
            color: #00f;
        }
        #qthinfo table td a:hover {
            color: #f00;
            text-decoration: underline;
        }
        #qthinfo p.cta {
            font-style: italic;
            font-size: 70%;
        }
        #qthinfo p.cta a {
            color: #00f;
            font-weight: bold;
        }
        @media print {
            h2, p {
                display: none;
            }
            td, a {
                color: #000 !important;
            }
            td {
                padding: 0.25em 0.5em 0.25em 0.5em !important;
            }
        }
    </style>
</head>
<body>
<div id="qthinfo">
    <h2>{{ $title }}
        <a class="btn r" style="margin-left: 2em" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Reload</a>
        @if($hidestats)
            <a class="btn g" href="{{ $url }}">Show Stats</a>
        @else
            <a class="btn g" href="{{ $url }}?hidestats=1">Hide Stats</a>
        @endif
        <a class="btn b" target="_blank" href="{{ $url }}{{ $hidestats ? '?hidestats=1' : '' }}">Print</a>
    </h2>
    <p>Click the links below to view live logs and an interactive gridsquares map.</p>
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Grid</th>
            <th>Location</th>
            @if(!$hidestats)
                <th>Dates</th>
                <th title="Days actively logging">*Days</th>
                <th>Logs</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($qths as $label => $q)
            <tr>
                <td> {{ $q['gsq'] }}</td>
                <td><a href="{{ route('home') }}/logs/{{ str_replace('/', '-', $user->call) }}/?presets[]=myQth|{{ $label }}" target="_blank">{{ $label }}</a></td>
                @if(!$hidestats)
                    <td>{{ $q['logFirst'] }}@if($q['logDays'] > 1) - {{ $q['logLast'] }}@endif</td>
                    <td class="r">{{ $q['logDays'] }}</td>
                    <td class="r">{{ $q['logs'] }}</td>
                @endif
            </tr>
        @endforeach
        @if (!$hidestats && count($qths) > 1)
            <tr class="totals">
                <td colspan="2">Totals</td>
                <td>{{ substr($user['first_log'], 0, 10) }}@if(substr($user['first_log'], 0, 10) !== substr($user['last_log'], 0, 10)) - {{ substr($user['last_log'], 0, 10) }}@endif</td>
                <td class="r">{{ $user['log_days'] ?: 0 }}</td>
                <td class="r">{{ $user['log_count'] }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    <p><b>*Days</b> means days with recorded logs.</p>
    <p class="cta">Share your <a href="https://qrz.com">QRZ.com</a> live logs, maps and stats at <a href="https://logs.classaxe.com" target="_blank">https://logs.classaxe.com</a></p>
</div>
</body>
</html>
