@php
/* @var $qths */
/* @var $hidestats */
/* @var $user */

@endphp
<html>
<head>
<title>{{ $title }}</title>
@vite([
    'resources/css/summary.css'
])
</head>
<body>
@include('user.summary.content')
</body>
</html>
