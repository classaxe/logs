var COOKIE = {
    clear: function(which, path) {
        document.cookie =
            which +
            '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=' +
            ('string' === typeof path ? path : '/');
    },
    get: function(which) {
        var cookies =		document.cookie;
        var pos =		cookies.indexOf(which+"=");
        if (pos === -1) {
            return false;
        }
        var start =	pos + which.length+1;
        var end =	cookies.indexOf(";",start);
        if (end === -1) {
            end =	cookies.length;
        }
        return unescape(cookies.substring(start, end));
    },
    set: function(which, value, path) {
        var nextYear =	new Date();
        nextYear.setFullYear(nextYear.getFullYear()+1);
        document.cookie =
            which +
            '=' + value + ';expires=' + nextYear.toGMTString() + '; path=' +
            ('string' === typeof path ? path : '/');
    },
}
