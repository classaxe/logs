var logs = [];
var filters = {
    bands: [],
    modes: [],
    conf: '',
    call: '',
    sp: '',
    itu: '',
    gsq: ''
}

var frm = {
    start: null,
    getFilters: function () {
        filters.bands = [];
        filters.modes = [];
        $('.band input:checked').each(function () {
            filters.bands.push($(this).data('band'));
        });
        $('.mode input:checked').each(function () {
            filters.modes.push($(this).data('mode'));
        });
        filters.conf =  $('input[name=conf]:checked').val();
        filters.call =  $('input[name=call]').val();
        filters.sp =    $('input[name=sp]').val();
        filters.itu =   $('input[name=itu]').val().replace(' ','');
        filters.gsq =   $('input[name=gsq]').val();
    },
    parseLogs: function() {
        let html = [];
        $.each(logs, function(idx, log) {
            if (frm.isVisible(log)){
                html.push(
                    '<tr>' +
                    '<td>' + (idx + 1)+ '</td>' +
                    '<td class="nowrap">' + log.date + '</td>' +
                    '<td class="nowrap">' + log.time + '</td>' +
                    '<td data-link="call">' + log.call + '</td>' +
                    '<td data-link="band"><span class="band band' + log.band + '">' + log.band + '</span></td>' +
                    '<td data-link="mode"><span class="mode m' + log.mode + '">' + log.mode + '</span></td>' +
                    '<td class="num">' + log.rx + '</td>' +
                    '<td class="num">' + log.tx + '</td>' +
                    '<td class="num">' + log.pwr + '</td>' +
                    '<td>' + log.qth + '</td>' +
                    '<td data-link="sp">' + log.sp + '</td>' +
                    '<td data-link="itu">' + log.itu + '</td>' +
                    '<td data-link="gsq">' + log.gsq + '</td>' +
                    '<td class="num">' + log.km + '</td>' +
                    '<td class="r">' + log.conf + '</td>'
                )
            }
        });
        return html.join('\n');
    },
    addLinks: function() {
        $('td[data-link]').each(function() {
            let link = $(this).attr('data-link');
            $(this).html("<a href=\"#\" onclick=\"return setVal('" + link + "\', \'" + $(this).text() +"\')\">" + $(this).html() + "</a>");
        })
    },
    isVisible: function(log) {
        if (!filters.bands.length || $.inArray(log.band, filters.bands) < 0) {
            return false;
        }
        if (!log.mode.length || $.inArray(log.mode, filters.modes) < 0) {
            return false;
        }
        if (filters.conf === 'N' && log.conf !== '') {
            return false;
        }
        if (filters.conf === 'Y' && log.conf !== 'Y') {
            return false;
        }
        if (filters.call.length && filters.call.toLowerCase() !== log.call.toLowerCase().substring(0, filters.call.length)) {
            return false;
        }
        if (filters.sp.length && filters.sp.toLowerCase() !== log.sp.toLowerCase()) {
            return false;
        }
        if (filters.itu.length && filters.itu.toLowerCase() !== log.itu.toLowerCase().substring(0, filters.itu.length)) {
            return false;
        }
        if (filters.gsq.length && filters.gsq.toLowerCase() !== log.gsq.toLowerCase().substring(0, filters.gsq.length)) {
            return false;
        }
        return true;
    },
    count: function() {
        let all = logs.length;
        let shown =     $('.list tbody tr').length;
        $('#logsShown').html((all === shown ? 'all ' : '') + '<strong>' + shown + '</strong> log' + (shown ===1 ? '' : 's'));
    },
    load: function(callsign) {
        $.ajax({
            type: 'GET',
            url: '/logs/' + callsign + '/logs',
            dataType: 'json',
            success: function (data) {
                logs = data.logs;
                frm.getFilters();
                // console.log(logs);
                // console.log(filters);
                $('table.list tbody').html(frm.parseLogs());
                frm.count();
                frm.addLinks();
            }
        })
    },
    update: function() {
        frm.start = Date.now();
        $('body').addClass('loading');
        window.setTimeout(function() { frm.update_doit()}, 1);
    },
    update_doit: function() {
        frm.getFilters();
        console.log(filters);
        $('table.list tbody').html(frm.parseLogs());
        frm.count();
        frm.addLinks();
        $("body").removeClass("loading");
        console.log('Updated in ' + ((Date.now() - frm.start)/1000) + ' seconds');
    },
}


window.setVal = function(source, value) {
    switch(source) {
        case 'band':
            $('input[name=' + source + ']').prop('checked', false);
            $('input[data-band=' + value + ']').prop('checked', true);
            break;
        case 'mode':
            $('input[name=' + source + ']').prop('checked', false);
            $('input[data-mode=' + value + ']').prop('checked', true);
            break;
        default:
            $('input[name=' + source + ']').val(value);
            break;
    }
    frm.update();
    return false;
}
window.addEventListener("DOMContentLoaded", function(){
    $('input[name=band]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.bandsAll').click(function() {
        $('input[name=band]').prop('checked', $(this).prop('checked'));
        $('input[name=band]').trigger('change');
    });
    $('input[name=mode]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.modesAll').click(function() {
        $('input[name=mode]').prop('checked', $(this).prop('checked'));
        $('input[name=mode]').trigger('change');
    });
    $('input[name=conf]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=call]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=sp]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=itu]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=gsq]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('button#reload').click(function() {
        window.location.reload();
    });
    $('button#reset').click(function() {
        $('input[name=band]').prop('checked','checked');
        $('input[name=mode]').prop('checked','checked');
        $('input#conf_All').prop('checked','checked');
        $('input[name=call]').val('');
        $('input[name=sp]').val('');
        $('input[name=itu]').val('');
        $('input[name=gsq]').val('');
        frm.update();
        $(this).blur();
    });
    frm.load(callsign)
});

