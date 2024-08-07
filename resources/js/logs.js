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

    _init: () => {
        if (typeof callsign !== 'undefined') {
            frm.setActions();
            frm.compact();
            frm.load(callsign)
        }
    },

    addLinks: () => {
        $('td[data-link]').each(function() {
            let link = $(this).attr('data-link');
            let html = $("<a href=\"#\">" + $(this).html() + "</a>");
            $(html).on('click', function() {
                return frm.setVal(link, $(this).text());
            })
            $(this).html(html);
        })
    },

    cl: function(v) {
        console.log(v);
    },

    compact: () => {
        let compact = COOKIE.get('compact');
        'Y' === compact ? $('.not-compact').hide() : $('.not-compact').show();
        $(window).trigger('resize');
    },

    count: () => {
        let all = logs.length;
        let shown = logsFiltered.length;
        $('#logCount').text(all);
        $('#logUpdated').text()
        $('#logsShown').html(
            (all === shown ? 'all ' : '') + '<strong>' + shown + '</strong> log' + (shown ===1 ? '' : 's')
        );
    },

    getFilters: () => {
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
        filters.cont =  $('select[name=cont]').val();
        filters.gsq =   $('input[name=gsq]').val();
    },

    getGridSquares: ()  => {
        gsqs = [] // Global
        let gsqs_tmp = {}
        let lat_min = 90;
        let lat_max = -90;
        let lon_min = 180;
        let lon_max = -180

        $(logsFiltered).each(function(idx,log){
            let gsq = log.gsq;
            let latlon;
            if (gsq) {
                if (typeof gsqs_tmp[gsq] === 'undefined') {
                    latlon = frm.gsq_deg(gsq);
                    gsqs_tmp[gsq] = {
                        bands: [],
                        calls: [],
                        conf: '',
                        deg: 0,
                        gsq: gsq,
                        km: 0,
                        lat: latlon.lat,
                        lon: latlon.lon,
                        logs: [],
                        marker: null
                    };
                    if (latlon.lat > lat_max) {
                        lat_max = latlon.lat;
                    }
                    if (latlon.lat < lat_min) {
                        lat_min = latlon.lat;
                    }
                    if (latlon.lon > lon_max) {
                        lon_max = latlon.lon;
                    }
                    if (latlon.lon < lon_min) {
                        lon_min = latlon.lon;
                    }
                    box = [{
                        lat: lat_min,
                        lon: lon_min
                    }, {
                        lat: lat_max,
                        lon: lon_max
                    }];
                }
                if (log.conf === 'Y') {
                    gsqs_tmp[gsq].conf = 'Y'
                }
                gsqs_tmp[gsq].logs.push(log);
                gsqs_tmp[gsq].bands[log.band] = log.band;
                gsqs_tmp[gsq].calls[log.call] = log.call;
            }
        });
        for (let gsq in gsqs_tmp) {
            gsqs.push(gsqs_tmp[gsq]);
        }
        LMap.fitToBox();
    },

    getUniqueValues: (field) => {
        let idx;
        let tmp = [];
        let count = 0;
        let values = [];
        $(logsFiltered).each(function(idx,log){
            if (log[field] !== '') {
                tmp[log[field].toUpperCase()] = 1;
            }
        });
        for (idx in tmp) {
            if (tmp.hasOwnProperty(idx)) {
                count++;
                values.push(idx)
            }
        }
        return {
            count: count,
            values: values.sort()
        };
    },

    getUniqueValuesStats: (field) => {
        let tmp = frm.getUniqueValues(field);
        return "<span title='" + tmp.values.join(', ') + "' style='cursor: help'>" + tmp.count + "</span>";
    },

    gsq_deg: (GSQ) => {
        let lat, lat_d, lat_m, lat_s, lon, lon_d, lon_m, lon_s, offset;
        if (!GSQ.match(/^([a-rA-R]{2})([0-9]{2})([a-xA-X]{2})?$/i)) {
            return false;
        }
        GSQ = GSQ.toUpperCase();
        offset = 0;//(GSQ.length === 6 ? 1/48 : 0);
        GSQ = GSQ + (GSQ.length === 4 ? 'MM' : '');
        lon_d = GSQ.charCodeAt(0)-65;
        lon_m = parseFloat(GSQ.substring(2,3));
        lon_s = GSQ.charCodeAt(4)-65;
        lat_d = GSQ.charCodeAt(1)-65;
        lat_m = parseFloat(GSQ.substring(3,4));
        lat_s = GSQ.charCodeAt(5)-65;
        lon = Math.round((2 * (lon_d * 10 + lon_m + lon_s / 24 + offset) - 180) * 10000) / 10000;
        lat = Math.round((lat_d * 10 + lat_m + lat_s / 24 + offset - 90) * 10000) / 10000;
        return {
            lat: lat,
            lon: lon,
            north: lat + 0.5,
            south: lat - 0.5,
            east: lon +1,
            west: lon -1
        };
    },

    isVisible: (log) => {
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

    load: (callsign) => {
        frm.start = Date.now();
        $.ajax({
            type: 'GET',
            url: '/logs/' + callsign + '/logs',
            dataType: 'json',
            success: function (data) {
                logs = data.logs;
                $(logs).each(function(idx, log) {
                    logs[idx].datetime = log.date + ' ' + log.time;
                    logs[idx].countyName = (log.county.indexOf(',') > 0 ? log.county.split(',')[1] : '');
                    logs[idx].itusp = log.itu + log.sp;
                    logs[idx].ituband = log.itu + log.band;
                    logs[idx].callband = log.call + log.band;
                });
                frm.getFilters();
                $('table.list tbody').html(frm.parseLogs());
                frm.compact();
                $('#logUpdated').text(data.lastPulled);
                frm.count();
                frm.stats();
                frm.getGridSquares();
                frm.addLinks();
                $("body").removeClass("loading");
                console.log('Updated in ' + ((Date.now() - frm.start)/1000) + ' seconds');
            }
        })
    },

    parseLogs: () => {
        let html = [];
        let sortField = $('select[name=sortField]').val();
        switch(sortField) {
            case 'county':
                sortField = 'countyName'
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
                '<td class="not-compact">' + log.name + '</td>' +
                '<td data-link="band"><span class="band band' + log.band + '">' + log.band + '</span></td>' +
                '<td data-link="mode"><span class="mode m' + log.mode + '">' + log.mode + '</span></td>' +
                '<td class="r">' + log.rx + '</td>' +
                '<td class="r">' + log.tx + '</td>' +
                '<td class="r">' + log.pwr + '</td>' +
                '<td>' + log.qth + '</td>' +
                '<td class="not-compact">' + log.countyName + '</td>' +
                '<td data-link="sp">' + log.sp + '</td>' +
                '<td data-link="itu">' + log.itu + '</td>' +
                '<td data-link="cont">' + log.continent + '</td>' +
                '<td class="not-compact" data-link="gsq">' + log.gsq + '</td>' +
                '<td class="r">' + (typeof log.km === 'number' ? log.km : '') + '</td>' +
                '<td class="r">' + (typeof log.deg === 'number' ? log.deg : '') + '</td>' +
                '<td class="r">' + log.conf + '</td>'
            )
        });
        return html.join('\n');
    },

    setActions: () =>{
        ('Y' === COOKIE.get('compact') ?
            $('input#compact_Y').prop('checked','checked') :
            $('input#compact_N').prop('checked','checked')
        );
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
            $(this).blur();
            frm.update();
        });
        $('input[name=compact]').change(function() {
            $(this).blur();
            COOKIE.set('compact', $(this).val(), '/');
            frm.compact();
        })
        $('input[name=call]').keyup(function() {
            frm.update();
        });
        $('input[name=sp]').keyup(function() {
            frm.update();
        });
        $('input[name=itu]').keyup(function() {
            frm.update();
        });
        $('select[name=cont]').change(function() {
            $(this).blur();
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
            $('select[name=cont]').val('');
            $('input[name=gsq]').val('');
            $('select[name=sortField]').val('logNum');
            $('input[name=sortZA]').prop('checked', 'checked');
            $('.sortable').removeClass('asc').removeClass('desc');
            $('input#compact_N').prop('checked','checked');
            $('th[data-field=logNum]').addClass('desc');
            frm.update();
            $(this).blur();
        });
        $('#show_list').click(function() {
            $('#show_list').removeClass('is-inactive').addClass('is-active');
            $('#show_map').removeClass('is-active').addClass('is-inactive');
            $('.map').hide();
            $('.list').show();
        });
        $('#show_map').click(function() {
            LMap.drawGridSquares();
            $('#show_list').removeClass('is-active').addClass('is-inactive');
            $('#show_map').removeClass('is-inactive').addClass('is-active');
            $('.list').hide();
            $('.map').show();
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
    },

    setVal: (source, value)=>  {
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
    },

    sortLogs: (sortField, sortZa) => {
        if (sortZa) {
            logs.sort(function(a,b){
                let aVal = (typeof a[sortField] === 'string' ? a[sortField].toLowerCase() || '|||' : a[sortField]);
                let bVal = (typeof b[sortField] === 'string' ? b[sortField].toLowerCase() || '|||' : b[sortField]);
                return ((aVal < bVal) ? -1 : ((aVal > bVal) ? 1 : 0));
            });
        } else {
            logs.sort(function(a,b){
                let aVal = (typeof a[sortField] === 'string' ? a[sortField].toLowerCase() || '!!!' : a[sortField]);
                let bVal = (typeof b[sortField] === 'string' ? b[sortField].toLowerCase() || '!!!' : b[sortField]);
                return ((bVal < aVal) ? -1 : ((bVal > aVal) ? 1 : 0));
            });
        }
    },

    stats: () => {
        let sp = frm.getUniqueValues('sp');
        let cont = frm.getUniqueValues('continent');
        $('#statsCounties').html(frm.getUniqueValuesStats('county'));
        $('#statsSps').html(frm.getUniqueValuesStats('sp'));
        $('#statsItus').html(frm.getUniqueValuesStats('itu'));
        $('#statsContinents').html(frm.getUniqueValuesStats('continent'));
        $('#statsCalls').text(frm.getUniqueValues('call').count);
        $('#statsGsqs').text(frm.getUniqueValues('gsq').count);
        $('#statsItuBands').text(frm.getUniqueValues('ituband').count);
        $('#statsCallBands').text(frm.getUniqueValues('callband').count);
    },

    update: () => {
        frm.start = Date.now();
        $('body').addClass('loading');
        window.setTimeout(function() { frm.update_doit()}, 1);
    },

    update_doit: () => {
        frm.getFilters();
        $('table.list tbody').html(frm.parseLogs());
        frm.compact();
        frm.count();
        frm.stats();
        frm.getGridSquares();
        frm.addLinks();
        if ($('.map').is(':visible')) {
            LMap.drawGridSquares();
        }
        $("body").removeClass("loading");
        console.log('Updated in ' + ((Date.now() - frm.start)/1000) + ' seconds');
    },
}

frm._init();
