<pre>
var qths = [
@foreach ($qths as $label => $data)
    {
        label:      "{{ $label }}",
        gsq:        "{{ $data['gsq'] }}",
        logs:       {{ $data['logs'] }},
        logDays:    {{ $data['logDays'] }},
        logFirst:   "{{ $data['logFirst'] }},
        logLast:    "{{ $data['logLast'] }},
    },
@endforeach
]
</pre>
<?php


dump($user);
dump($qths);
