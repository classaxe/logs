{{--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMUy5Jogj6vJD5UvQevWeIQ-WuzdX71cE&callback=Map.init"></script>--}}
<script>
var msg = {
    "cancelled": "Operation cancelled",
    "cart_none": "(No awards have been selected)",
    "cart_1": "NDB LIST AWARD REQUEST:",
    "cart_2": "To:",
    "cart_3": "From:",
    "cart_4": "Url:",
    "cart_5": "I would like to request the following awards, based upon my published logs in RNA / REU / RWW.",
    "cart_6": "I confirm that have not previously received these awards.",
    "cart_7": "Sincerely,",
    "cart_conf_1": "CONFIRM ORDER",
    "cart_conf_2": "Please verify the details in this form, including the 'Reply To:' email address where awards will be sent.",
    "cart_conf_3": "* Press 'Cancel' if you wish to go back and make changes.",
    "cart_conf_4": "* Press 'OK' to send your request now.",
    "close": "Close",
    "copied_x": "Copied '%s' to Clipboard.",
    "cookie": {
        "reset": "Would you also like to clear previously saved preferences for this form?",
        "save": "Save these settings as the default for this form?",
        "saved": "Your preferences have been saved.\nPressing 'Clear' will reset the form and remove the cookie.",
        "usesCookie": "A cookie will be used to store this setting."
    },
    "copy_token": "Click to copy this token.",
    "continue": "Continue?",
    "daytime": "Daytime Logging",
    "decommissioned": "Decommissioned",
    "del_listener": "Delete this Listener?  Are you sure?",
    "del_log": "Delete this Log entry?  Are you sure?",
    "del_log_session": "Delete this entire Log Session?  Are you sure?",
    "del_signal": "Delete this Signal?  Are you sure?",
    "email_needed": "Please provide an email address",
    "error": "Error",
    "excel": "Excel",
    "export": "Export entire :system listing to :format?",
    "export2": "'Signal Types' filter will be used, all other settings are ignored.",
    "export3": "PSKOV requires you to rename the file to 'export_RWW.xls'",
    "data_append": "Set or Clear Signal filtering on this value",
    "data_gsq": "Show map (accuracy limited to nearest Grid Square)",
    "data_set": "Set or Clear filtering on this value",
    "gsq": "GSQ",
    "heard_in": "Heard In",
    "id": "ID",
    "inactive": "Inactive",
    "ilg": "ILGRadio Database Format",
    "ip_needed": "Please provide a valid IP4 or IP6 address",
    "itu": "ITU",
    "khz": "KHz",
    "lat_lon": "Lat / Lon",
    "last_logged": "Last Logged",
    "loading": "Loading...",
    "log_upload": {
        "confirm": {
            "1": "Submit Log - are you sure?",
            "2": "There are COUNT unresolved issues remaining.",
            "3": "Please capture the remaining logs for later processing."
        },
        "copy_remaining": "Copied remaining logs to clipboard.",
        "prepare_email": "Copied Enquiry Email to clipboard.",
        "last_item": "You are at the last item",
        "prompt": {
            "a": "Save this format for future logs by this listener?",
            "b": "Cancelled"
        }
    },
    "logged": "Logged",
    "logged_by": "Logged by Personalised Listener",
    "name_qth": "'Name' and Location",
    "no": "No",
    "nooptions": "All options you selected in 'Customise Report' above will be ignored.",
    "options": "Signal types, 'offsets' setting and 'active' status will be taken from the options you selected in 'Customise Report' above, other options will be ignored.",
    "paging_dn": "of %s donations",
    "paging_dr": "of %s donors",
    "paging_l": "of %s listeners",
    "paging_s": "of %s signals",
    "paging_u": "of %s users",
    "pdf": "PDF Document",
    "power": "Power",
    "qth_pri": "Primary QTH",
    "qth_sec": "Secondary QTH",
    "reset": "Reset this form?",
    "s_map_eu": "European Reception Map",
    "s_map_na": "North American Reception Map",
    "sec_format": "Secs / Format",
    "share": {
        "listeners": {
            "links": {
                "export": "Export",
                "list": "Listing",
                "map": "Map"
            },
            "title": "Shareable Links",
            "text1": "<b>Left-click</b> on a link to visit.",
            "text2": "<b>Right-click</b> to copy link address."
        },
        "signals": {
            "links": {
                "export": "Export",
                "list": "Listing",
                "map": "Map",
                "seeklist": "Seeklist"
            },
            "title": "Shareable Links",
            "text1": "<b>Left-click</b> on a link to visit.",
            "text2": "<b>Right-click</b> to copy link address."
        }
    },
    "show_hide": "Show / Hide this section",
    "sidebands": "Sidebands",
    "signals": {
        "personalise": "Personalised for %s",
        "title": {
            "both": "Showing Logged and Unlogged Signals for All systems",
            "normal": "%s Signals List",
            "unlogged": "Showing Unlogged Signals for all systems"
        }
    },
    "sp": "S/P",
    "time": "This can be a time consuming process - typically a minute or more.",
    "tools": {
        "coords": {
            "lat_dec": "Latitude must be a decimal number between -90 and 90",
            "lon_dec": "Longitude must be a decimal number between -180 and 180",
            "lat_dms_1": "Latitude must be given in one of these formats:",
            "lat_dms_2": "(H is N or S, but defaults to N if not given)",
            "lon_dms_1": "Longitude must be given in one of these formats:",
            "lon_dms_2": "(H is E or W, but defaults to E if not given)",
            "gsq_format": "GSQ must be in one of these formats:\nXXnnxx or XXnn"
        },
        "dgps": {
            "inactive": "(Inactive)",
            "multiple": "Multiple matches",
            "nomatch": "Station not recognised"
        }
    },
    "type": "Type",
    "unlogged_by": "Unlogged by Personalised Listener",
    "yes": "Yes"
}
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
var base_image = '/images';
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
