var debug = document.getElementById("out");
function debugOut(text) {
    debug.innerHTML = text+"<br />\n"+debug.innerHTML;
}

var ws = new WebSocket("ws://mysite/galcon/serv");

ws.onopen = function() { debugOut("Connection opened...") };
ws.onclose = function() { debugOut("Connection closed...") };
ws.onmessage = function(evt) { debugOut(evt.data) };