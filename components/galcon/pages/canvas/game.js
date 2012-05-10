function extend(Child, Parent) {
    var F = function() {}
    F.prototype = new Parent();
    
    for (var p in F.prototype)
        if (F.prototype.hasOwnProperty(p)) delete F.prototype[p];
    Child.prototype = new F();
    Child.prototype.constructor = Child;
}

function Point(x, y) {
    this.x = x;
    this.y = y;
    this.selected = false;
}

Point.prototype.doMove = function(dx, dy) {
    this.x += dx;
    this.y += dy;
}

Point.prototype.click = function() {
    this.selected = true;
}

const GAMER = 0;
const BOT = 1;
const NEUTRAL = 2;

function Player(playerConf) {
    this.color = playerConf.color;
    this.name = playerConf.name;
    this.type = playerConf.type;
}

function Ship(from, to, shipsCount) {
    Point.call(this, from.x, from.y);
    this.player = from.player;
    this.type = "Ship";
    this.destination = to;
    this.shipsCount = shipsCount;
    this.speed = 4;
    this.sX = 12;
    this.sY = 12;
    this.cos = 1;
    this.sin = 0;
    this.angle = 0;
    this.angleCompute();
    this.done = false;
}

extend(Ship, Point);

Ship.prototype.triangle = new Array(
        {x: -1, y: -1},
        {x: -1, y: 1},
        {x: 1, y: 0});

Ship.prototype.draw = function() {
    with (this) {
        ctx.setTransform(cos, sin, -sin, cos, Math.floor(x), Math.floor(y));
        if (selected) {
            ctx.fillStyle = "black";
            ctx.beginPath();
            ctx.moveTo(triangle[0].x * (sX+3), triangle[0].y * (sY+3));
            ctx.lineTo(triangle[1].x * (sX+3), triangle[1].y * (sY+3));
            ctx.lineTo(triangle[2].x * (sX+3), triangle[2].y * (sY+3));
            ctx.lineTo(triangle[0].x * (sX+3), triangle[0].y * (sY+3));
            ctx.fill();
        }
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.moveTo(triangle[0].x * sX, triangle[0].y * sY);
        ctx.lineTo(triangle[1].x * sX, triangle[1].y * sY);
        ctx.lineTo(triangle[2].x * sX, triangle[2].y * sY);
        ctx.lineTo(triangle[0].x * sX, triangle[0].y * sY);
        ctx.fill();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = "white";
        ctx.fillText(shipsCount, x-5, y);
    }
}

Ship.prototype.doMove = function() {
    if (!this.done) {
        with (this) {
            if (destination.collision(x, y)) {
                if (destination.player === player)
                    destination.shipsCount += shipsCount;
                else {
                    destination.shipsCount -= shipsCount;
                    if (destination.shipsCount < 0) {
                        destination.shipsCount = -destination.shipsCount;
                        destination.changePlayer(player);
                    }
                }
                this.done = true;
            }
            var dx = cos * speed;
            var dy = sin * speed;
        }
        Point.prototype.doMove.call(this, dx, dy);
    }
}

Ship.prototype.angleCompute = function() {
    with (this) {
        if (x != destination.x)
            this.angle = Math.atan((y-destination.y)/(x-destination.x));
        else {
            if (y < destination.y)
                this.angle = Math.PI/2;
            else this.angle = -Math.PI/2
        }
        if (destination.x < x) 
            angle += Math.PI;
        this.cos = Math.cos(angle);
        this.sin = Math.sin(angle);
    }
}

Ship.prototype.collision = function(x, y) {
    if ((Math.abs(this.x - x) < this.sX+4) && (Math.abs(this.y - y) < this.sY+4))
        return true;
    else return false;
}

Ship.prototype.setDestination = function(to) {
    this.destination = to;
    this.angleCompute();
    this.done = false;
}

function CanvasText(x, y) {
    Point.call(this, x, y);
    this.type = "Text";
    this.text = "";
    this.color = "black";
    this.style = "10px Arial";
}
extend(CanvasText, Point);

CanvasText.prototype.setText = function(text) {
    this.text = text.toString();
}

CanvasText.prototype.setStyle = function(style) {
    this.style = style;
}

CanvasText.prototype.draw = function() {
    this.ctx.fillStyle = this.color;
    this.ctx.font = this.style;
    var dy = 0;
    var out = "";
    for (var i=0; i<this.text.length; i++) {
        if (this.text[i] === "\n") {
            this.ctx.fillText(out, this.x, this.y + dy);
            out = "";
            dy += parseInt(this.ctx.font)+2;
            continue;
        }
        out += this.text[i];
    }
    this.ctx.fillText(out, this.x, this.y + dy);
}

function Planet(x, y, R, player, shipsCount) {
    Point.call(this, x, y);
    this.player = player;
    this.type = "Planet";
    this.R = R;
    this.shipsCount = shipsCount;
}

extend(Planet, Point);

Planet.prototype.doMove = function() {
    if (this.player.type !== NEUTRAL)
        this.shipsCount += this.R / 80;
}

Planet.prototype.draw = function() {
    with (this) {
        if (selected) {
            ctx.fillStyle = "black";
            ctx.beginPath();
            ctx.arc(x, y, R+2, 0, 2*Math.PI, true);
            ctx.fill();
        }
        
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.arc(x, y, R, 0, 2*Math.PI, true);
        ctx.fill();
        
        ctx.fillStyle = "white";
        ctx.fillText(Math.floor(shipsCount), x-10, y);
    }
}

Planet.prototype.collision = function(x, y) {
    if ((Math.abs(this.x - x) < this.R) && (Math.abs(this.y - y) < this.R))
        return true;
    else return false;
}

function CanvasButton(x, y, game) {
    CanvasText.call(this, x, y);
    this.type = "Button";
    this.game = game;
    this.text = game.sendshipsCount*100+"%";
    this.color = "white";
}

extend(CanvasButton, CanvasText);

CanvasButton.prototype.collision = function(x, y) {
    if ((Math.abs(this.x - x) < 20) && (Math.abs(this.y - y) < 10))
        return true;
    else return false;
}

CanvasButton.prototype.click = function() {
    if (this.game.sendCount === 1)
        this.game.sendCount = 0.25;
    else this.game.sendCount += 0.25;
    this.setText(this.game.sendCount*100+"%");
}

CanvasButton.prototype.draw = function() {
    with (this) {
        ctx.fillStyle = "gray";
        ctx.fillRect(x-10, y-14, 40, 20);
    }
    CanvasText.prototype.draw.call(this);
}

function Game(serverUrl) {
    var game = this;
    var canvas = document.getElementById("field");
    var server = serverUrl;
    var players = new Array();
    var obj = new Array();
    var text;
    var timer;
    var botTimer1;
    var botTimer2;
    this.sendCount = 0.5;

    var firstSelect = null;
    var secondSelect = null;
    
    var ofsL = canvas.offsetLeft;
    var ofsT = canvas.offsetTop;
    
    var ajaxSend = function(handler) {
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    debugOut(this.responseText);
                } else debugOut("This is FAIL");
            }
        }
        ajax.open("POST", server, true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("galcon="+params);
    }
    
    var getMap = function() {
        var send = 'galcon="{"task":"getMap"}"';
        var hnd = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    result = eval("("+this.responseText+")");
                    for (var p in result)
                    obj[obj.length] = new Planet(p.x, p.y, p.R, p.player, p.shipsCount);
                } else debugOut("This is FAIL");
            }
        }
        ajaxSend(hnd, send, server);
    }
    
    var getPlayers = function() {
        var send = 'galcon="{"task":"getMap"}"';
        var hnd = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    result = eval("("+this.responseText+")");
                    for (var p in result)
                    players[players.length] = new Planet(p.x, p.y, p.R, p.player, p.shipsCount);
                } else debugOut("This is FAIL");
            }
        }
        ajaxSend(hnd, send, server);
    }
    
    var generateMap = function() {
        var maxR = 40;
        var minR = 10;
        var minShips = 0;
        var maxShips = 100;
      
        var p = {};
        for (var i=0; i<10; i++) {
            p.R = Math.floor(Math.random() * (maxR-minR+1)) + minR;
            p.x = Math.floor(Math.random() * (canvas.width - p.R*2 - 50) + 25);
            p.y = Math.floor(Math.random() * (canvas.height - p.R*2 - 50) + 25);
            p.shipsCount = Math.floor(Math.random() * (maxShips-minShips+1)) + minShips;
            if (!selectXY(p.x, p.y))
                obj[obj.length] = new Planet(p.x, p.y, p.R, 0, p.shipsCount);
        }
        obj[0].shipsCount = 100;
        obj[0].changePlayer(1);
        obj[0].R = 30;
        obj[1].shipsCount = 100;
        obj[1].changePlayer(2);
        obj[1].R = 30;
    }
    
    var botMove = function(player) {
        var selfPlanets = new Array();
        var enemyPlanets = new Array();
        //var selfShips = new Array();
        //var enemyShips = new Array();
        
        for (var i=0; i<obj.length; i++)
            if (obj[i].type === "Planet") {
//                if (obj[i].player === player)
//                    selfShips[selfShips.length] = obj[i];
//                else enemyShips[enemyShips.length] = obj[i];
//            } else {
                if (obj[i].player === player)
                    selfPlanets[selfPlanets.length] = obj[i];
                else enemyPlanets[enemyPlanets.length] = obj[i];
            }
        if ((enemyPlanets.length > 0) && (selfPlanets.length > 0)) {
            selfPlanets.sort(function(a, b) {
            return b.shipsCount - a.shipsCount; 
            });
            enemyPlanets.sort(function(a, b) {
                var result;
                if (a.shipsCount === 0) result = -1;
                else if (b.shipsCount === 0) result =  1;
                else {
                    result = (b.R/b.shipsCount) - (a.R/a.shipsCount);
                    //if (a.player === 2) result -= 0.4;
                    //if (b.player === 2) result += 0.4;
                }
                return result;
            });
    //        var text = "";
    //        for (i=0; i<enemyPlanets.length; i++) {
    //            text += enemyPlanets[i].R + " " + enemyPlanets[i].ships + "\n";
    //        }
    //        debug.setText(text);
            sendShip(selfPlanets[0], enemyPlanets[0]);
        }
    }
    
    window.onresize = function() {
        ofsL = canvas.offsetLeft;
        ofsT = canvas.offsetTop;
    }
    
    canvas.onmousedown = function(event) {
        if ((event.which === 1) || (event.which === 2)) {
            var x = event.pageX - ofsL;
            var y = event.pageY - ofsT;
            if (firstSelect !== null) {
                secondSelect = selectXY(x, y);
                if (secondSelect !== null) 
                    if (firstSelect.type === "Planet") {
                        sendShip(firstSelect, secondSelect, game.sendshipsCount);
                        //ajaxSend("from=");
                    }
                    else firstSelect.setDestination(secondSelect);
                firstSelect.selected = false;
                firstSelect = null;
                secondSelect = null;
            } else {
                firstSelect = selectXY(x, y);
                if (firstSelect)
                    if ((firstSelect.type === "Button") || (firstSelect.player === 1)) {
                        firstSelect.click();
                        if (firstSelect.type === "Button")
                            firstSelect = null;
                    }
            }
            //text.setText("mouse x " + x + " y " + y + " " + firstSelect + " " + secondSelect);
        }
    }

    var selectXY = function(x, y) {
        for (var i=0; i<obj.length; i++) {
            if (obj[i].collision(x, y))
                return obj[i];
        }
        return null;
    }
    
    var debugOut = function(text) {
        
        text.setText(out);
        text.draw();
    }
    
    var next = function() {
        var p1 = 0;
        var p2 = 0;
        for (var i=obj.length; i--;)
            if (obj[i].player) {
                obj[i].doMove();
                if (obj[i].player === 1) p1 += obj[i].shipsCount;
                else p2 += obj[i].shipsCount;
                if (obj[i].done) obj.splice(i, 1);
            }
        
        Point.prototype.ctx.clearRect(0,0,canvas.width,canvas.height);
        for (i=0; i<obj.length; i++) obj[i].draw();
        debugOut(text);
        if ((p1 === 0) || (p2 === 0)) {
            game.stop();
            text = new CanvasText(canvas.width / 2 - 100, canvas.height / 2 -50);
            var out = "Game Over \nPlayer ";
            if (p1 > 0)
                out += "1";
            else out += "2";
            out += " win!";
            text.setText(out);
            Point.prototype.ctx.fillStyle = "rgba(155,155,155,0.5)";
            Point.prototype.ctx.fillRect(0, 0, canvas.width, canvas.height);
            text.setStyle("16px Arial");
            text.draw();
        }
    }
    var sendShip = function(from, to, shipsCount) {
        if (typeof shipsCount === "undefined")
            shipsCount = 0.5;
        if ((shipsCount > 0) && (shipsCount <= 1)) {
            var send = Math.floor(from.shipsCount * shipsCount);
            if (from.player !== 0) {
                from.shipsCount -= send;
                obj[obj.length] = new Ship(from, to, send);
            }
        }
    }
    this.start = function() {
        if (timer) {
            this.stop();
            this.start();
        } else {
            canvas.width = 600;
            canvas.height = 480;
            Point.prototype.ctx = canvas.getContext("2d");
            Point.prototype.ctx.font = "10px Arial";
            generateMap();
            //testMap();
            obj[obj.length] = new CanvasButton(canvas.width - 50, canvas.height-20, game);
            text = new CanvasText(canvas.width - 200, 50);
            timer = setInterval(function() {next()}, 33);
            //botTimer1 = setInterval(function() {botMove(1)}, 800);
            //botTimer2 = setInterval(function() {botMove(2)}, 800);
        }
    }
    this.stop = function() {
        clearInterval(timer);
        clearInterval(botTimer1);
        clearInterval(botTimer2);
        obj = null;
        text = null;
        canvas.onmousedown = null;
    }
}