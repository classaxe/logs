<html>
<head>
    <title>Location and Stats for {{ $user->name }} - {{ $user->call }}</title>
    <style>
        #qthinfo h2 {
            font-size: 1em;
        }
        #qthinfo h2,
        #qthinfo p {
            text-align: center;
            margin: 0.5em auto;
            font-family: Figtree,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji"
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
            font-weight: 200;
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
    </style>
</head>
<body>
<div id="qthinfo">
    <h2>Location and Stats for {{ $user->name }} - {{ $user->call }}</h2>
    <p>Click the links below to view live logs and an interactive gridsquares map.</p>
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Grid</th>
            <th>Location</th>
            <th>Dates</th>
            <th title="Days actively logging">*Days</th>
            <th>Logs</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($qths as $label => $q)
            <tr>
                <td> {{ $q['gsq'] }}</td>
                <td><a href="{{ route('home') }}/logs/{{ $user->call }}/?presets[]=myQth|{{ $label }}" target="_blank">{{ $label }}</a></td>
                <td>{{ $q['logFirst'] }}@if($q['logDays'] > 1) - {{ $q['logLast'] }}@endif</td>
                <td class="r">{{ $q['logDays'] }}</td>
                <td class="r">{{ $q['logs'] }}</td>
            </tr>
        @endforeach
        @if (count($qths) > 1)
            <tr class="totals">
                <td colspan="2">Totals</td>
                <td>{{ substr($user['first_log'], 0, 10) }}@if($user['log_days'] ?: 0 > 1) - {{ substr($user['last_log'], 0, 10) }}@endif</td>
                <td class="r">{{ $user['log_days'] ?: 0 }}</td>
                <td class="r">{{ $user['log_count'] }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    <p><b>*Days</b> means days with recorded logs.</p>
</div>
</body>
</html>
