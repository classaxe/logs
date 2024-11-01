function qthInfo() {
    let callsign = "{{ $user['call'] }}";
    let logs =     {{ $user['log_count'] }};
    let logDays =  {{ $user['log_days'] ?: 0 }};
    let logFirst = "{{ substr($user['first_log'], 0, 10) }}";
    let logLast =  "{{ substr($user['last_log'], 0, 10) }}";
    let name =     "{{ $user['name'] }}";
    let qths = [
@foreach ($qths as $label => $data)
        {
            label:      "{{ $label }}",
            gsq:        "{{ $data['gsq'] }}",
            logs:       {{ $data['logs'] }},
            logDays:    {{ $data['logDays'] }},
            logFirst:   "{{ $data['logFirst'] }}",
            logLast:    "{{ $data['logLast'] }}"
        },
@endforeach
    ];

    let html = `
<style>
#qthinfo p {
    text-align: center;
    margin: 0.5em auto;
}
#qthinfo table {
    border-collapse: collapse;
    margin: 0 auto;
}
#qthinfo table thead th {
    background: #888;
    color: #fff;
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
    border: 1px solid #888;
    padding: 2px 5px;
    vertical-align: top;
}
#qthinfo table tbody td.r {
    text-align: right !important;
}
#qthinfo table td a {
    color: #00f;
}
#qthinfo table td a:hover {
    color: #f00;
    text-decoration: underline;
}
</style>
<h2>Location and Stats for {{ $user->name }} - {{ $user->call }}</h2>
<p>Click the links below to view live logs and an interactive gridsquares map.</p>
<table border="1" cellpadding="2" cellspacing="0">
    <thead>
    <tr>
        <th>Location</th>
        <th>Grid</th>
        <th>Dates</th>
        <th title="Days actively logging">*Days</th>
        <th>Logs</th>
    </tr>
    </thead>
    <tbody>`;
    for (let i=0; i < qths.length; i++) {
        let q = qths[i];
        html += `
        <tr>
            <td><a href="https://logs.classaxe.com/logs/` + callsign + `?presets[]=myQth|` + q['label'] + `" target="_blank">` + q['label'] + `</a></td>
            <td>` + q['gsq'] + `</td>
            <td>` + q['logFirst'] + (q['logDays'] > 1 ? ` - ` + q['logLast']  : '') + `</td>
            <td class="r">` + q['logDays'] + `</td>
            <td class="r">` + q['logs'] + `</td>
         </tr>`
    }
    if (qths.length > 1) {
        html += `
        <tr class="totals">
            <td colspan="2">Totals</td>
            <td>` + logFirst + (logDays > 1 ? ` - ` + logLast : '') + `</td>
            <td class="r">` + logDays + `</td>
            <td class="r">` + logs + `</td>
        </tr>`;
    }
    html += `</tbody></table>`;
html += "<p><b>*Days</b> means days with recorded logs.</p>";
    document.getElementById('qthinfo').innerHTML = html;
}
qthInfo();
