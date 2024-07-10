var filters = {
    bands: [],
    modes: [],
    conf: '',
    cont: '',
    call: '',
    sp: '',
    itu: '',
    gsq: ''
}
var logs = [];
var logsFiltered = [];

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
        filters.cont =  $('input[name=cont]').val();
        filters.gsq =   $('input[name=gsq]').val();
    },
    sortLogs: function(sortField, sortZa) {
        if (sortZa) {
            logs.sort(function(a,b){
                let aVal = (typeof a[sortField] === 'string' ? a[sortField].toLowerCase() : a[sortField]);
                let bVal = (typeof b[sortField] === 'string' ? b[sortField].toLowerCase() : b[sortField]);
                return ((aVal < bVal) ? -1 : ((aVal > bVal) ? 1 : 0));
            });
        } else {
            logs.sort(function(a,b){
                let aVal = (typeof a[sortField] === 'string' ? a[sortField].toLowerCase() : a[sortField]);
                let bVal = (typeof b[sortField] === 'string' ? b[sortField].toLowerCase() : b[sortField]);
                return ((bVal < aVal) ? -1 : ((bVal > aVal) ? 1 : 0));
            });
        }
    },
    parseLogs: function() {
        let html = [];
        let sortField = $('select[name=sortField]').val();
        switch(sortField) {
            case 'date':
                sortField = 'datetime';
                break;
            case 'itu':
                sortField = 'itusp';
                break;
        }
        let sortZa = $('input[name=sortZA]').prop('checked') ? false : true;
        if (sortField) {
            frm.sortLogs(sortField, sortZa);
        }
        logsFiltered = [];
        $.each(logs, function(idx, log) {
            if (frm.isVisible(log)){
                logsFiltered.push(log)
            }
        });
        $.each(logsFiltered, function(idx, log){
            html.push(
                '<tr>' +
                '<td class="r">' + (log.logNum)+ '</td>' +
                '<td class="nowrap">' + log.date + '</td>' +
                '<td class="nowrap">' + log.time + '</td>' +
                '<td data-link="call">' + log.call + '</td>' +
                '<td data-link="band"><span class="band band' + log.band + '">' + log.band + '</span></td>' +
                '<td data-link="mode"><span class="mode m' + log.mode + '">' + log.mode + '</span></td>' +
                '<td class="r">' + log.rx + '</td>' +
                '<td class="r">' + log.tx + '</td>' +
                '<td class="r">' + log.pwr + '</td>' +
                '<td>' + log.qth + '</td>' +
                '<td data-link="sp">' + log.sp + '</td>' +
                '<td data-link="itu">' + log.itu + '</td>' +
                '<td data-link="cont">' + log.continent + '</td>' +
                '<td data-link="gsq">' + log.gsq + '</td>' +
                '<td class="r">' + log.km + '</td>' +
                '<td class="r">' + log.conf + '</td>'
            )
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
        if (filters.cont.length && filters.cont.toLowerCase() !== log.continent.toLowerCase().substring(0, filters.cont.length)) {
            return false;
        }
        if (filters.gsq.length && filters.gsq.toLowerCase() !== log.gsq.toLowerCase().substring(0, filters.gsq.length)) {
            return false;
        }
        return true;
    },
    count: function() {
        let all = logs.length;
        let shown = logsFiltered.length;
        $('#logCount').text(all);
        $('#logUpdated').text()
        $('#logsShown').html(
            (all === shown ? 'all ' : '') + '<strong>' + shown + '</strong> log' + (shown ===1 ? '' : 's')
        );
    },
    getUniqueValues: function(field) {
        let idx;
        let tmp = [];
        let count = 0;
        $(logsFiltered).each(function(idx,log){
            if (log[field] !== '') {
                tmp[log[field].toUpperCase()] = 1;
            }
        });
        for (idx in tmp) {
            if (tmp.hasOwnProperty(idx)) {
                count++;
            }
        }
        return count;
    },
    stats: function() {
        console.log(logsFiltered[0]);
        $('#statsSps').text(frm.getUniqueValues('sp'));
        $('#statsItus').text(frm.getUniqueValues('itu'));
        $('#statsContinents').text(frm.getUniqueValues('continent'));
        $('#statsCalls').text(frm.getUniqueValues('call'));
        $('#statsGsqs').text(frm.getUniqueValues('gsq'));
    },
    load: function(callsign) {
        frm.start = Date.now();
        $.ajax({
            type: 'GET',
            url: '/logs/' + callsign + '/logs',
            dataType: 'json',
            success: function (data) {
                logs = data.logs;
                $(logs).each(function(idx, log) {
                    logs[idx].datetime = log.date + log.time;
                    logs[idx].itusp = log.itu + log.sp;
                });
                frm.getFilters();
                $('table.list tbody').html(frm.parseLogs());
                $('#logUpdated').text(data.lastPulled);
                frm.count();
                frm.stats();
                frm.addLinks();
                $("body").removeClass("loading");
                console.log('Updated in ' + ((Date.now() - frm.start)/1000) + ' seconds');

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
        $('table.list tbody').html(frm.parseLogs());
        frm.count();
        frm.stats();
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
    $('input[name=band]').click(function(e) {
        if (e.shiftKey) {
            $('input[name=band]').prop('checked', false);
            $('.bandsAll').prop('checked', false);
            $(this).prop('checked', 'checked');
        }
        $(this).blur();
        $('#logUpdated').focus();
        frm.update();
    });
    $('.bandsAll').click(function() {
        $('input[name=band]').prop('checked', $(this).prop('checked'));
        $('input[name=band]').trigger('change');
    });
    $('input[name=mode]').click(function(e) {
        if (e.shiftKey) {
            $('input[name=mode]').prop('checked', false);
            $('.modesAll').prop('checked', false);
            $(this).prop('checked', 'checked');
        }
        $(this).blur();
        $('#logUpdated').focus();
        frm.update();
    });
    $('.modesAll').click(function() {
        $('input[name=mode]').prop('checked', $(this).prop('checked'));
        $('input[name=mode]').trigger('change');
    });
    $('input[name=conf]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=call]').keyup(function() {
        frm.update();
    });
    $('input[name=sp]').keyup(function() {
        frm.update();
    });
    $('input[name=itu]').keyup(function() {
        frm.update();
    });
    $('input[name=cont]').keyup(function() {
        frm.update();
    });
    $('input[name=gsq]').keyup(function() {
        frm.update();
    });
    $('select[name=sortField]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=sortZA]').change(function() {
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
        $('input[name=cont]').val('');
        $('input[name=gsq]').val('');
        $('select[name=sortField]').val('logNum');
        $('input[name=sortZA]').prop('checked', 'checked');
        $('.sortable').removeClass('asc').removeClass('desc');
        $('th[data-field=logNum]').addClass('desc');
        frm.update();
        $(this).blur();
    });
    var $sortable = $('.sortable');
    $sortable.on('click', function(){
        var $this = $(this);
        var asc = $this.hasClass('asc');
        var desc = $this.hasClass('desc');
        $sortable.removeClass('asc').removeClass('desc');

        if (desc || (!asc && !desc)) {
            $this.addClass('asc');
            $('input[name=sortZA]').prop('checked', false);
        } else {
            $this.addClass('desc');
            $('input[name=sortZA]').prop('checked', 'checked');
        }
        $('select[name=sortField]').val($this.data('field'));
        frm.update();
    });
    frm.load(callsign)
});

