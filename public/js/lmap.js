// Globals: signals, types
var LMap = {
    map : null,
    icons : {},
    infoWindow : null,
    markers : [],
    options : {},
    sortBy  : 'khz',
    sortOrder : 'a',

    init: function() {
        var icons = [ 'dgps', 'dsc', 'hambcn', 'navtex', 'ndb', 'time', 'other' ];
        var states = [ 0, 1 ];
        for (var i in icons) {
            for (var j in states) {
                var pin = base_image + '/pins/' + icons[i] + '_' + states[j] + '.png';
                LMap.icons[icons[i] + '_' + states[j]] =
                    new google.maps.MarkerImage(pin, new google.maps.Size(12, 20));
            }
        }
        //LMap.getGridSquares();
        LMap.options = {
            'zoom': 7,
            'center': new google.maps.LatLng(center.lat, center.lon),
            'mapTypeId': google.maps.MapTypeId.ROADMAP
        };
        LMap.map = new google.maps.Map($('#map').get(0), LMap.options);
        if (box[0].lat !== box[1].lat || box[0].lon !== box[1].lon) {
            LMap.map.fitBounds(
                new google.maps.LatLngBounds(
                    new google.maps.LatLng(box[0].lat, box[0].lon), //sw
                    new google.maps.LatLng(box[1].lat, box[1].lon) //ne
                )
            );
        }

        LMap.infoWindow = new google.maps.InfoWindow();
        LMap.drawGrid();
        LMap.drawMarkers();
        LMap.drawQTH();
        LMap.setActions();
        //setExternalLinks();
        //setClippedCellTitles();
        nite.init(LMap.map);
        setInterval(function() { nite.refresh() }, 10000); // every 10s
    },

    drawGrid : function() {
        return drawGrid(LMap.map, layers, true);
    },

    drawMarkers : function() {
        var html, i, icon_highlight, marker, mode;
        if (!signals) {
            return;
        }
        mode = (typeof listener === 'undefined' ? 'S' : 'LS');
        LMap.markerGroups=new google.maps.MVCObject();
        for (i in types) {
            LMap.markerGroups.set('type_' + types[i] + '_0', LMap.map);
            LMap.markerGroups.set('type_' + types[i] + '_1', LMap.map);
        }
        LMap.markerGroups.set('highlight', LMap.map);

        icon_highlight = {
            url: base_image + '/map_point_here.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(6, 7)
        };

        html = '';
        for (i in signals) {
            s = signals[i];
            html +=
                '<tr' +
                ' class="type_' + s.typeId +
                ' type_' + s.className +
                (s.decommissioned ? ' decommissioned' : '') +
                (typeof s.logged !== 'undefined' ? (s.logged ? ' logged' : ' unlogged') : '') +
                '"' +
                ' id="signal_' + s.id + '"' +
                ' data-gmap="' + s.lat + '|' + s.lon + '"' +
                '>' +
                (typeof s.logged !== 'undefined' ? '<td class="personalise" data-val="' + (s.logged ? 'logged' : 'unlogged') + '">' + (s.logged ? '&#x2714;' : '&nbsp;') + '</td>' : '') +
                '<td data-val="' + s.khz +'">' + s.khz + '</td>' +
                '<td data-val="' + s.call + '" class="text-nowrap">' +
                '<a href="' + base_url + 'signals/' + s.id + '" data-popup="1">' + s.call + '</a>' +
                '</td>' +
                '<td data-val="' + s.qth + '" class="clipped">' + s.qth + '</td>' +
                '<td data-val="' + s.sp + '">' + s.sp + '</td>' +
                '<td data-val="' + s.itu + '">' + s.itu + '</td>' +
                (typeof s.km !== 'undefined' ? '<td class="personalise num" data-val="' + s.km +'">' + s.km + '</td>' : '') +
                (typeof s.mi !== 'undefined' ? '<td class="personalise num" data-val="' + s.mi +'">' + s.mi + '</td>' : '') +
                (typeof s.deg !== 'undefined' ? '<td class="personalise num" data-val="' + s.deg +'">' + s.deg + '</td>' : '') +
                '</tr>';

            marker = new google.maps.Marker({
                id : 'point_' + s.id,
                icon : LMap.icons[s.icon + '_' + (s.active ? 1 : 0)],
                position : new google.maps.LatLng(s.lat, s.lon),
                title : s.khz + ' ' + s.call
            });
            google.maps.event.addListener(marker, 'click', LMap.markerClickFunction(s));
            marker.bindTo('map', LMap.markerGroups, 'type_' + s.typeId + '_' + (s.active ? '1' : '0'));
            markers.push(marker);
        }

        $('.results tbody').append(html);

        $('tr[data-gmap]')
            .mouseover(function() {
                var coords = $(this).data('gmap').split('|');
                highlight = new google.maps.Marker({
                    position: new google.maps.LatLng(coords[0], coords[1]),
                    map: LMap.map,
                    icon: icon_highlight
                });
            })
            .mouseout(function() {
                highlight.setMap(null);
            });

        $('.no-results').hide();
        $('.results').show();
    },

    drawQTH : function() {
        if (typeof listener === 'undefined') {
            return;
        }
        layers.qth = new google.maps.Marker({
            position: { lat: listener.lat, lng: listener.lng },
            map: LMap.map,
            icon: {
                scaledSize: new google.maps.Size(30,30),
                url: "//maps.google.com/mapfiles/kml/pushpin/red-pushpin.png"
            },
            title: listener.name,
            zIndex: 100
        });

        qthInfo = new google.maps.InfoWindow({
            content:
                "<h2>" + listener.name + "</h2>" +
                "<p>" + listener.qth + "</p>"
        });

        layers.qth.addListener('click', function() {
            qthInfo.open(LMap.map, layers.qth);
        });
    },

    markerClickFunction: function(s) {
        return function(e) {
            e.cancelBubble = true;
            e.returnValue = false;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
            var infoHtml =
                '<div class="map_info">' +
                '  <h3><a href="' + base_url + 'signals/' + s.id + '" onclick="return popup(this.href);">' + s.khz + ' ' + s.call + '</a></h3>' +
                '  <table class="info-body">' +
                (typeof s.logged !== 'undefined' ? '    <tr><th>' + msg.logged +'</th><td>' + (s.logged ? msg.yes : msg.no) + '</td></tr>' : '') +
                '    <tr><th>' + msg.id + '</th><td>'+s.call + '</td></tr>' +
                '    <tr><th>' + msg.khz + '</th><td>'+s.khz + '</td></tr>' +
                '    <tr><th>' + msg.type + '</th><td>'+s.type + '</td></tr>' +
                (s.pwr !== '0' ? '    <tr><th>' + msg.power + '</th><td>'+s.pwr + 'W</td></tr>' : '') +
                '    <tr><th>' + msg.name_qth + '</th><td>'+s.qth + (s.sp ? ', ' + s.sp : '') + ', ' + s.itu + '</td></tr>' +
                (s.gsq ? '    <tr><th>' + msg.gsq + '</th><td><a href="' + base_url + 'signals/' + s.id + '/map" onclick="return popup(this.href);" title="Show map (accuracy limited to nearest Grid Square)">'+s.gsq+'</a></td></tr>' : '') +
                '    <tr><th>' + msg.lat_lon + '</th><td>' + s.lat + ', ' + s.lon + '</td></tr>' +
                (s.usb || s.lsb ? '    <tr><th>' + msg.sidebands + '</th><td>' + (s.lsb ? 'LSB: ' + s.lsb : '') + (s.usb ? (s.lsb ? ', ' : '') + ' USB: ' + s.usb : '') + '</td></tr>' : '') +
                (s.sec || s.fmt ? '    <tr><th>' + msg.sec_format + '</th><td>' + (s.sec ? s.sec + ' sec' : '') + (s.sec && s.fmt ? ', ' : '') + s.fmt + '</td></tr>' : '') +
                '    <tr><th>' + msg.last_logged + '</th><td>' + s.heard + '</td></tr>' +
                '    <tr><th>' + msg.heard_in + '</th><td>' + s.heard_in + '</td></tr>' +
                '  </table>' +
                '</div>';
            LMap.infoWindow.setContent(infoHtml);
            LMap.infoWindow.setPosition(new google.maps.LatLng(s.lat, s.lon));
            LMap.infoWindow.open(LMap.map);
        };
    },

    setActions : function() {
        $('#layer_grid').click(function () {
            var active, i;
            active = $('#layer_grid').prop('checked');
            for (i in layers.grid) {
                layers.grid[i].setMap(active ? LMap.map : null);
            }
        });

        $('#layer_night').click(function () {
            if ($('#layer_night').prop('checked')) {
                nite.show()
            } else {
                nite.hide();
            }
        });

        $('#layer_qth').click(function () {
            layers['qth'].setMap($('#layer_qth').prop('checked') ? LMap.map : null);
        });

        $('#layer_active').click(function () {
            var i, layer_active, layer_type, type;
            for (i in types) {
                type = types[i];
                layer_active = $('#layer_active');
                layer_type = $('#layer_' + type);
                LMap.markerGroups.set(
                    'type_' + type + '_1',
                    layer_active.prop('checked') && layer_type.prop('checked') ? LMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if (layer_active.prop('checked')) {
                        $('.results tbody .type_' + type + '.active').show();
                    } else {
                        $('.results tbody .type_' + type + '.active').hide();
                    }
                } else {
                    $('.results tbody .type_' + type + '.active').hide();
                }
            }
        });
        $('#layer_inactive').click(function () {
            var i, layer_inactive, layer_type, type;
            for (i in types) {
                type = types[i];
                layer_inactive = $('#layer_inactive');
                layer_type = $('#layer_' + type);
                LMap.markerGroups.set(
                    'type_' + type + '_0',
                    layer_inactive.prop('checked') && layer_type.prop('checked') ? LMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if (layer_inactive.prop('checked')) {
                        $('.results tbody .type_' + type + '.inactive').show();
                    } else {
                        $('.results tbody .type_' + type + '.inactive').hide();
                    }
                } else {
                    $('.results tbody .type_' + type + '.inactive').hide();
                }
            }
        });
        types.forEach(function (type) {
            $('#layer_' + type).click(function () {
                var layer_type = $('#layer_' + type);
                LMap.markerGroups.set(
                    'type_' + type + '_0',
                    $('#layer_inactive').prop('checked') && layer_type.prop('checked') ? LMap.map : null
                );
                LMap.markerGroups.set(
                    'type_' + type + '_1',
                    $('#layer_active').prop('checked') && layer_type.prop('checked') ? LMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if ($('#layer_inactive').prop('checked')) {
                        $('.results tbody .type_' + type + '.inactive').show();
                    } else {
                        $('.results tbody .type_' + type + '.inactive').hide();
                    }
                    if ($('#layer_active').prop('checked')) {
                        $('.results tbody .type_' + type + '.active').show();
                    } else {
                        $('.results tbody .type_' + type + '.active').hide();
                    }
                } else {
                    $('.results tbody .type_' + type).hide();
                }
            });
        });
        mapMarkerColSetActions();
    }
};
