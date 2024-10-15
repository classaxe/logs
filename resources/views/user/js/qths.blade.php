function qthInfo() {
    let callsign = "{{ $user['call'] }}";
    let name = "{{ $user['name'] }}";
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
    #qthinfo h2 {
        font-family: Arial, Helvetica, sans-serif;
        margin: 1em 0 0.5em 0;
    }
    #qthinfo p {
        text-align: center;
        margin: 0.5em auto;
    }
    #qthinfo table {
        border-collapse: collapse;
        margin: 0 auto;
    }
    #qthinfo table th {
        background: #888;
        color: #fff;
    }
    #qthinfo table th,
    #qthinfo table td {
        border: 1px solid #888;
        padding: 2px 5px;
        vertical-align: top;
    }
    #qthinfo table td.days,
    #qthinfo table td.logs {
        text-align: right;
    }
    #qthinfo table td a {
        color: #00f;
    }
    #qthinfo table td a:hover {
        color: #f00;
        text-decoration: underline;
    }
    </style>
    <h2>Locations and Stats for ` + name + ` - ` + callsign + `</h2>
    <p>Click the links below to view live logs and an interactive gridsquares map.</p>
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Grid</th>
            <th>Location</th>
            <th>Dates</th>
            <th>Days</th>
            <th>Logs</th>
        </tr>
        </thead>
        <tbody>`;
    for (let i=0; i < qths.length; i++) {
        let q = qths[i];
        html += `    <tr>
        <td>` + q['gsq'] + `</td>
        <td><a href="https://logs.classaxe.com/logs/` + callsign + `?presets[]=myQth|` + q['label'] + `" target="_blank">` + q['label'] + `</a></td>
        <td>` + q['logFirst'] + (q['logDays'] > 1 ? ` - ` + q['logLast']  : '') + `</td>
        <td class="days">` + q['logDays'] + `</td>
        <td class="logs">` + q['logs'] + `</td>
    </tr>`
    }
    html += `</tbody></table>`;
    document.getElementById('qthinfo').innerHTML = html;
}
qthInfo();
