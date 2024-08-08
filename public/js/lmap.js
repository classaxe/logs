var LMap = {
    map : null,
    icons : {},
    infoWindow : null,
    markers : [],
    options : {},
    sortBy  : 'khz',
    sortOrder : 'a',

    init: function() {
        let latlng = qth.gsq;
        LMap.options = {
            'zoom': 7,
            'center': new google.maps.LatLng(qth.lat, qth.lng),
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
        LMap.map = new google.maps.Map($('#map').get(0), LMap.options);
        if (box[0].lat !== box[1].lat || box[0].lon !== box[1].lon) {
            LMap.fitToBox();
        }
        LMap.infoWindow = new google.maps.InfoWindow();
        LMap.drawGrid();
        LMap.drawQTH();
        LMap.setActions();
        nite.init(LMap.map);
        setInterval(function() { nite.refresh() }, 10000); // every 10s
    },

    initMapsTxtOverlay: function() {
        // Thanks to Michal, 'UX Lead at Alphero' for this custom text overlay code
        // Ref: https://stackoverflow.com/a/3955258/815790

        function TxtOverlay(pos, txt, cls) {
            this.pos = pos;
            this.txt_ = txt;
            this.cls_ = cls;
            this.div_ = null;
        }

        TxtOverlay.prototype = new google.maps.OverlayView();

        TxtOverlay.prototype.onAdd = function() {
            var div, overlayProjection, panes, position;
            div = document.createElement('DIV');
            div.className = this.cls_;
            div.innerHTML = this.txt_;
            this.div_ = div;
            overlayProjection = this.getProjection();
            position = overlayProjection.fromLatLngToDivPixel(this.pos);
            div.style.left = position.x + 'px';
            div.style.top = position.y + 'px';
            panes = this.getPanes();
            panes.floatPane.appendChild(div);
        };

        TxtOverlay.prototype.draw = function() {
            var div, position, overlayProjection;
            overlayProjection = this.getProjection();
            position = overlayProjection.fromLatLngToDivPixel(this.pos);
            div = this.div_;
            div.style.left = position.x + 'px';
            div.style.top = position.y + 'px';
        };

        TxtOverlay.prototype.onRemove = function() {
            this.div_.parentNode.removeChild(this.div_);
            this.div_ = null;
        };

        return TxtOverlay;
    },

    drawGrid : function() {
        let squares = true;
        let TxtOverlay =    LMap.initMapsTxtOverlay();
        var i, la, laf, lo, lof;
        for (la=0; la<180; la+=10) {
            layers.grid.push(
                new google.maps.Polyline({
                    path: [
                        {lat: (la-90), lng: -180},
                        {lat:(la-90), lng: 0},
                        {lat: (la-90), lng: 180}
                    ],
                    geodesic: false,
                    strokeColor: gridColor,
                    strokeOpacity: gridOpacity,
                    strokeWeight: 0.5
                })
            );
            if (typeof squares !== 'undefined' && squares) {
                for(laf=0; laf<10; laf++) {
                    layers.grid.push(
                        new google.maps.Polyline({
                            path: [{lat: laf + (la-90), lng: -180}, {lat: laf + (la-90), lng: 0}, {lat: laf +  (la-90), lng: 180}],
                            geodesic: false,
                            strokeColor: gridColor,
                            strokeOpacity: gridOpacity,
                            strokeWeight: 0.25
                        })
                    );
                }
            }
        }
        for (lo=0; lo<360; lo+=20) {
            layers.grid.push(
                new google.maps.Polyline({
                    path: [{lat: 85.05, lng: lo}, {lat: -85.05, lng: lo}],
                    geodesic: false,
                    strokeColor: gridColor,
                    strokeOpacity: gridOpacity,
                    strokeWeight: 0.5
                })
            );
            if (typeof squares !== 'undefined' && squares) {
                for (lof = 0; lof < 20; lof += 2) {
                    layers.grid.push(
                        new google.maps.Polyline({
                            path: [{lat: 85.05, lng: lo + lof}, {lat: -85.05, lng: lo + lof}],
                            geodesic: false,
                            strokeColor: gridColor,
                            strokeOpacity: gridOpacity,
                            strokeWeight: 0.25
                        })
                    );

                }
            }
        }
        for (la=10; la<170; la+=10) {
            for (lo = 0; lo < 360; lo += 20) {
                layers.grid.push(
                    new TxtOverlay(
                        new google.maps.LatLng(la -90 + 5.17,lo -180 + 9.625),
                        String.fromCharCode((lo/20) +65) + String.fromCharCode((la/10) +65),
                        'gridLabel'
                    )
                );
            }
        }
        for (i in layers.grid) {
            layers.grid[i].setMap(LMap.map);
        }
    },

    drawGridSquares: function() {
        let gsq, i, old;
        for (i in layers.squares) {
            layers.squares[i].setMap(null);
        }
        layers.squares = [];
        let html = '';
        for (gsq in gsqs) {
            this.drawGridSquare(
                gsq,
                this.gsq4Bounds(gsq),
                gsqs[gsq].conf === 'Y'
            );
            html += this.drawGridSquareList(
                gsqs[gsq]
            );
        }
        $('#gsqs tbody').html(html);
    },

    drawGridSquareList: function(gsq) {

//        let bands = LMap.getUniqueArrayValues(gsq.bands);
//        console.log(bands);
        return "<tr>" +
            "<td>" + gsq.gsq +"</td>" +
//            "<td>" + bands.length +"</td>" +
            "<td>" + gsq.logs.length +"</td>" +
            "</tr>";
    },

    drawGridSquare: function(gsq, bounds, conf) {
        let map = LMap.map;
        let rgb = conf ? '#FF0000' : '#FFFF00';
        let rgbb = conf ? '#800000' : '#808000';
        let square = new google.maps.Rectangle({
            strokeColor: rgbb,
            strokeOpacity: 0.85,
            strokeWeight: 1,
            fillColor: rgb,
            fillOpacity: 0.5,
            map,
            bounds: bounds,
        });
        google.maps.event.addListener(square, 'click', LMap.gsqClickFunction(gsq));
        layers.squares.push(square);
    },

    drawQTH : function() {
        if (typeof qth === 'undefined') {
            return;
        }
        layers.qth = new google.maps.Marker({
            position: { lat: qth.lat, lng: qth.lng },
            map: LMap.map,
            icon: {
                scaledSize: new google.maps.Size(30,30),
                url: base_image + '/red-pushpin.png'
            },
            title: qth.callsign,
            zIndex: 100
        });

        qthInfo = new google.maps.InfoWindow({
            content:
                "<h2><b>" + qth.call + "</b> - " + qth.name + "</h2>" +
                "<p>" + qth.qth + "</p>"
        });

        layers.qth.addListener('click', function() {
            qthInfo.open(LMap.map, layers.qth);
        });
    },

    fitToBox: () => {
        if (!LMap.map) {
            return;
        }

        LMap.map.fitBounds(
            new google.maps.LatLngBounds(
                new google.maps.LatLng(Math.min(qth.lat, box[0].lat) - 0.5, Math.min(qth.lng, box[0].lon) - 1), //sw
                new google.maps.LatLng(Math.max(qth.lat, box[1].lat) + 0.5, Math.max(qth.lng, box[1].lon) + 1) //ne
            )
        );
    },

    getUniqueArrayValues: (arr) => {
        let tmp = [];
        for (let key in arr) {
            if (arr.hasOwnProperty(key)) {
                tmp.push(key);
            }
        }
        return tmp;
    },

    gsq4Bounds: function(GSQ) {
        let lat, lat_d, lat_m, lon, lon_d, lon_m;
        if (!GSQ.match(/^([a-rA-R]{2})([0-9]{2})$/i)) {
            return false;
        }
        GSQ = GSQ.toUpperCase();
        lon_d = GSQ.charCodeAt(0)-65;
        lon_m = parseFloat(GSQ.substring(2,3));
        lat_d = GSQ.charCodeAt(1)-65;
        lat_m = parseFloat(GSQ.substring(3,4));
        lon = Math.round((2 * (lon_d * 10 + lon_m) - 180) * 10000) / 10000;
        lat = Math.round((lat_d * 10 + lat_m  - 90) * 10000) / 10000;
        return {
            north: lat + 1,
            south: lat,
            east: lon + 2,
            west: lon
        };
    },

    gsqClickFunction: function(g) {
        return function (e) {
            e.cancelBubble = true;
            e.returnValue = false;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
            let data, i, log, rows;
            data = gsqs[g];
            rows = '';
            for(i in data.logs) {
                log = data.logs[i];
                rows +=
                    "<tr>" +
                    "<td>" + log.datetime + "</td>" +
                    "<td>" + log.call + "</td>" +
                    "<td><span class='band band" + log.band + "'>" + log.band + "</span></td>" +
                    "<td><span class='mode m" + log.mode + "'>" + log.mode + "</span></td>" +
                    "<td>" + log.sp + "</td>" +
                    "<td>" + log.itu + "</td>" +
                    "<td>" + log.km.toLocaleString() + " Km</td>" +
                    "<td>" + log.rx + "</td>" +
                    "<td>" + log.tx + "</td>" +
                    "<td>" + log.pwr + "</td>" +
                    "<td>" + log.conf + "</td>" +
                    "</tr>";
            }
            let infoHtml =
                "<div class=\"map_info\">" +"" +
                "<h3>" +
                "<b>Grid Square <strong>" + data.gsq + "</strong></b> - " + data.logs.length +" logs (Square " + (data.conf === 'Y' ? "is" : "not") + " confirmed)" +
                "<a id='close' href='#' onclick='LMap.infoWindow.close();return false'>X</a>" +
                "</h3>" +
                "<table class='results'>" +
                "<thead><tr>" +
                "<th title='Date and time in UTC'>Date / Time</th>" +
                "<th title='Callsign of worked station'>Call</th>" +
                "<th>Band</th>" +
                "<th>Mode</th>" +
                "<th title='State, Province or Territory'>SP</th>" +
                "<th title='Country'>ITU</th>" +
                "<th title='Distance to worked station'>DX</th>" +
                "<th title='Received signal strength'>RX</th>" +
                "<th title='My signal strength'>TX</th>" +
                "<th title='My power in Watts'>Pwr</th>" +
                "<th title='Y=Confirmed by other party'>Conf</th>" +
                "</tr></thead>" +
                "<tbody>" + rows + "</table></div>";
            LMap.infoWindow.setContent(infoHtml);
            LMap.infoWindow.setPosition(new google.maps.LatLng(data.lat, data.lon));
            LMap.infoWindow.open(LMap.map);
            setTimeout(() => {
                $('#close').focus();
            }, 10);
        };
    },

    setActions : function() {
        var mapDiv = $('#map');
        $(window).resize(function() {
            let vspace = ('Y' === COOKIE.get('compact') ? 296 : 430);
            mapDiv.height($(window).height() - vspace);
            mapDiv.width($(window).width() - 390);
            $('#scrollablelist').height(mapDiv.height() + 40);
        })
        .trigger('resize');

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

        $('#layer_squares').click(function () {
            var active, i;
            active = $('#layer_squares').prop('checked');
            for (i in layers.squares) {
                layers.squares[i].setMap(active ? LMap.map : null);
            }
        });

        $('#layer_qth').click(function () {
            layers['qth'].setMap($('#layer_qth').prop('checked') ? LMap.map : null);
        });

        mapMarkerColSetActions();
    }
};
