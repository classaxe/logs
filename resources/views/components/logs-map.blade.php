{{--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMUy5Jogj6vJD5UvQevWeIQ-WuzdX71cE&callback=Map.init"></script>--}}
<script>
var center = {
    "lat": 27.25,
    "lon": -88.99985
}
var box = [
    {
        "lat": -25.4792,
        "lon": -142.458
    },
    {
        "lat": 79.9792,
        "lon": -35.5417
    }
];
var base_image = '';
var base_url = '/';
var gridColor = '#808080';
var gridOpacity = 0.5;
var layers = {
    grid: []}
;
var signals = [
    {
        "id": 459,
        "khz": 248,
        "call": "CG",
        "active": 1,
        "decommissioned": 0,
        "className": "ndb active",
        "type": "NDB",
        "typeId": 0,
        "pwr": "0",
        "qth": "&#039;Dutch&#039;  Cape Girardeau",
        "icon": "ndb",
        "itu": "USA",
        "sp": "MO",
        "lat": 37.2708,
        "lon": -89.7083,
        "gsq": "EM57dg",
        "lsb": "1036",
        "usb": "1024",
        "sec": "6.751",
        "fmt": "",
        "heard": "2023-11-15",
        "heard_in": "AZ  CA  CO  IL  IN  KS  MI  MO  NC  NE  NV  OH  ON  OR  PA  TN  TX  VA  WI"
    },
    {
        "id": 464,
        "khz": 248,
        "call": "IL",
        "active": 1,
        "decommissioned": 0,
        "className": "ndb active",
        "type": "NDB",
        "typeId": 0,
        "pwr": "50",
        "qth": "&#039;Hadin&#039;  Wilmington",
        "icon": "ndb",
        "itu": "USA",
        "sp": "DE",
        "lat": 39.5625,
        "lon": -75.625,
        "gsq": "FM29en",
        "lsb": "1047",
        "usb": "1043",
        "sec": "7.93",
        "fmt": "",
        "heard": "2024-01-23",
        "heard_in": "IL  MA  MD  MI  NC  NE  NH  NJ  NS  NY  OH  ON  PA  TX  VA  VT"
    },
    {
        "id": 7187,
        "khz": 248,
        "call": "WR",
        "active": 1,
        "decommissioned": 0,
        "className": "ndb active",
        "type": "NDB",
        "typeId": 0,
        "pwr": "500",
        "qth": "Woomera",
        "icon": "ndb",
        "itu": "AUS",
        "sp": "SA",
        "lat": -31.1458,
        "lon": 136.792,
        "gsq": "PF88ju",
        "lsb": "396",
        "usb": "403",
        "sec": "8.13",
        "fmt": "",
        "heard": "2024-02-02",
        "heard_in": "NFK  NI  NN  NW  QD  SA  TA  VI  WE  OR"
    }
];
var types = [
    1,
    6,
    4,
    3,
    0,
    2,
    5
];
var markers = [];
function strip_tags(input, allowed) {
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join('');
    return input.replace(commentsAndPhpTags, '')
        .replace(tags, function($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            }
        );
}
</script>
<script>
window.addEventListener("DOMContentLoaded", function() {
    let script = document.createElement("script");
    script.loading='async';
    script.src = "https://maps.googleapis.com/maps/api/js?key={{ getEnv('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=LMap.init";
    document.head.appendChild(script);
});
</script>
<div class="map" style="display: none">
    <div id="map" style="width: 100%; height: 1000px"></div>
    Coming soon!
</div>
