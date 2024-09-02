<table class="list">
    <thead>
        <tr>
@foreach($columns as $val => $col)
@php
    $class = ' ' . $col['class'];
    if ($val === 'logNum') {
        $class .= ' desc';
    }
@endphp
            <th data-field="{{ $val }}" class="sortable{{ $class }}">&nbsp; &nbsp; &nbsp;{{ $col['lbl'] }}</th>
@endforeach
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
