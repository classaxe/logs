<p class="mt-2 text-center">Showing <span id="logsShown"></span></p>
<table class="list">
    <thead>
    <tr>
        @foreach($columns as $val => $col)
        @php $class = ' ' . $col['class']; if ($val === 'logNum') $class .= ' asc' @endphp
        <th class="sortable{{ $class }}">{{ $col['lbl'] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
