<x-app-layout>
<script>
window.addEventListener("DOMContentLoaded", function(){
    $('.band input[type=checkbox]').change(function() {
        var band = $(this).data('band');
        if ($(this).prop('checked')) {
            $('tr.b' + band).show();
        } else {
            $('tr.b' + band).hide();
        }
        $(this).blur();
    });
    $('.bandsAll').click(function() {
        $('.band input[type=checkbox][data-band]').prop('checked', $(this).prop('checked'));
        $('.band input[type=checkbox][data-band]').trigger('change');
    });
});
</script>
    <div class="bands">
        @foreach($bands as $n => $b)
            <label class="band band{{ $b }}"><input type="checkbox" data-band="{{ $b }}" checked>{{ $b }}</label>
        @endforeach
            <label><input type="checkbox" checked class="bandsAll"> All</label>
    </div>
    <table class="list">
        <thead>
            <tr>
                <td>&nbsp;</td>
                <th>Date</th>
                <th>UTC</th>
                <th>Call</th>
                <th>Band</th>
                <th>Mode</th>
                <th class="num">RX</th>
                <th class="num">TX</th>
                <th class="num">Pwr</th>
                <th>QTH</th>
                <th>S/P</th>
                <th>ITU</th>
                <th>GSQ</th>
                <th class="num">Km</th>
                <th>Conf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $n=>$log)
                <tr class="b{{ $log['band'] }} m{{ $log['mode'] }}">
                    <td>{{ $n+1 }}</td>
                    <td class="nowrap">{{ $log['date'] }}</td>
                    <td class="nowrap">{{ $log['time'] }}</td>
                    <td>{{ $log['call'] }}</td>
                    <td><span class="band band{{ $log['band'] }}">{{ $log['band'] }}</span></td>
                    <td>{{ $log['mode'] }}</td>
                    <td class="num">{{ $log['rx'] }}</td>
                    <td class="num">{{ $log['tx'] }}</td>
                    <td class="num">{{ $log['pwr'] }}</td>
                    <td>{{ $log['qth'] }}</td>
                    <td>{{ $log['sp'] }}</td>
                    <td>{{ $log['itu'] }}</td>
                    <td>{{ $log['gsq'] }}</td>
                    <td class="num">{{ number_format($log['km']) }}</td>
                    <td class="r">{{$log['conf']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
