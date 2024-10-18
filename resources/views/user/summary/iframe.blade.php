<html>
<head>
    <title>Location and Stats for {{ $user->name }} - {{ $callsign }}</title>
</head>
<body>
<div id="qthinfo"></div>
<script src="{{ route('embed', ['method' => 'js', 'mode' => 'summary', 'callsign' =>Auth::user()->call]) }}"></script>
</body>
</html>
