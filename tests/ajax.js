var debug = document.getElementById("out");
function debugOut(text) {
    debug.innerHTML = text+"<br />\n"+debug.innerHTML;
}
var server = "/galcon/server";

var ajaxSend = function(handler, params, url) {
    url = url || window.location.href;
    var ajax = new XMLHttpRequest();
    ajax.onreadystatechange = handler;
    ajax.open("POST", url, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send("ajax=true&"+params);
}

var hnd = function() {
    if (this.readyState === 4) {
        if (this.status === 200) {
            debugOut(this.responseText);
        } else debugOut("This is FAIL");
    }
}
ajaxSend(hnd, '{"task":"getMap"}', "/galcon/server");