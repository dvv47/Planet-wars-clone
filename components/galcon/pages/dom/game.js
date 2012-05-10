function debugOut(text) {
    var debug = document.getElementById('debug');
    if (debug !== null) {
        debug.innerHTML = text+"<br />\n"+debug.innerHTML;
    }
}

function GameModel() {
    var view;
    this.setView = function(v) {view = v};
    this.getView = function() {return view};

    var planetsCount = 6;
    var planets = new Array(planetsCount);
    
    var ships = new Array();
    
    this.getShips = function() {return ships}
    this.getPlanets = function() {return planets}
    
    this.Ship = function(from, to, count) {
        this.from=from;
        this.to=to;
        var x1 = planets[from].x;
        var y1 = planets[from].y;
        this.x2 = planets[to].x;
        this.y2 = planets[to].y;
        this.x = x1;
        this.y = y1;
        
        var speed = 3;
        this.angle = 0;
        
        if (x1 != this.x2) {
            this.angle = Math.atan((y1-this.y2)/(x1-this.x2));
            if (this.x2 < x1) this.angle += Math.PI;
        }
        this.dx = Math.cos(this.angle) * speed;
        this.dy = Math.sin(this.angle) * speed;
        this.count = count
    }
    this.Ship.prototype.doMove = function() {
        if ((Math.abs(this.x - this.x2) < planets[this.to].R) &&
            (Math.abs(this.y - this.y2) < planets[this.to].R)) return false;
        this.x += this.dx;
        this.y += this.dy;
        return true;
    }
    
    this.newLevel = function() {
        var width = view.getMaxX();
        var height = view.getMaxY();
        
        var minR = 15;
        var maxR = 40;
        
        for (i=0; i<planetsCount; i++) {
            planets[i] = {}
            planets[i].R = Math.floor(Math.random() * (maxR-minR+1)) + minR;
            planets[i].x = Math.floor(Math.random() * (width - planets[i].R*2));
            planets[i].y = Math.floor(Math.random() * (height - planets[i].R*2));
            planets[i].ships = 0;
            planets[i].player = 0;
        }
        
        planets[0].x = 1;
        planets[0].y = 1;
        planets[0].ships = 100;
        planets[0].player = 1;
        planets[5].ships = 100;
        planets[5].x = 400;
        planets[5].y = 240;
        planets[5].player = 2;
        
        view.start();
        this.sendShip(0, 5, 10);  
        this.sendShip(0, 2, 20);
        this.sendShip(0, 3, 30);
        this.sendShip(5, 1, 10);
        this.sendShip(5, 2, 20);
        this.sendShip(5, 0, 30);
    }
    this.sendShip = function(from , to, count) {
        if (planets[from].ships >= count) {
            planets[from].ships -= count;
            ships[ships.length] = new this.Ship(from, to, count);
        }
    }
    this.next = function() {
        for (i=0; i<ships.length; i++) {
            //если функция вернула false значит ход не сделала то есть цель достигнута
            if (!ships[i].doMove()) {
                var start = planets[ships[i].from];
                var end = planets[ships[i].to];
                if (start.player === end.player)
                    end.ships += ships[i].count;
                else end.ships -= ships[i].count;
                if (end.ships<0) {
                    end.player = start.player;
                    end.ships = -end.ships;
                } else if (end.ships == 0) end.player=0;
                ships[i] = null;
            }
        }
        view.update();
        for (i=0; i<ships.length; i++)
            if (ships[i] === null) {
                ships.splice(i, 1);
                i -= 1;
            }
    }
    this.done = function() {
        ships = null;
        planets = null;
    }
}

function InputController(div) {
    var model;
    var field = div;
    
    this.setModel = function(m) {model = m};
    this.getModel = function() {return model};
    
    this.keyDown = function(event) {
        debugOut(event.keyCode);
    }
    document.onkeydown = this.keyDown;
    
    //var planets = new Array();
    
    var ofsL = field.offsetLeft;
    var ofsT = field.offsetTop;
    this.domMouse = function(event) {
        if ((event.which === 1) || (event.which === 2)) {
            var x = event.pageX - ofsL;
            var y = event.pageY - ofsT;
            debugOut("mouse x " + x + " y " + y + " " + event.which);
        }
    }
    field.onmousedown = this.domMouse;
    
    this.done = function() {
        document.onkeydown = null;
        field.onmousedown = null;
    }
}

function DOMView(div) {
    var model;
    this.setModel = function(m) {model = m};
    this.getModel = function() {return model};
    
    var field = div;
    this.getMaxX = function() {return field.clientWidth};
    this.getMaxY = function() {return field.clientHeight};
    
    var rotate = "null";
    if (document.body.style.webkitTransform !== undefined)
        rotate = "webkitTransform";
    else if (document.body.style.MozTransform !== undefined)
        rotate = "MozTransform";
    else if (document.body.style.oTransform !== undefined)
        rotate = "oTransform";
    else if (document.body.style.msTransform !== undefined)
        rotate = "msTransform";
    
    var domShips = new Array();
    this.done = function() {
        field.innerHTML = '';
    }
    this.start = function() {
        var planets = model.getPlanets();
        var domPlanets = document.createElement('div');
        domPlanets.className='planets';
        for (i=0; i<planets.length; i++) {
            var planet = document.createElement('div');
            planet.className='planet';
            planet.style.width = planets[i].R*2+'px';
            planet.style.height = planets[i].R*2+'px';
            planet.style.left = planets[i].x+'px';
            planet.style.top = planets[i].y+'px';
            domPlanets.appendChild(planet);
        }
        field.appendChild(domPlanets);
    }
    this.syncPlanets = function() {
        var planets = model.getPlanets();
        for (i=0; i<planets.length; i++) {
            var planet = field.childNodes[0].childNodes[i];
            switch (planets[i].player) {
                    case 1:
                            planet.style.backgroundColor = "#F00";
                            break;
                    case 2:
                            planet.style.backgroundColor = "#00F";
                            break;
                    default:
                            planet.style.backgroundColor = "#666";
            }
            planet.innerHTML="<p>"+planets[i].ships+"</p>";
        }
    }
    this.syncShips = function() {
        var ships = model.getShips();
        for (i=0; i<ships.length; i++) {
            if (ships[i] === null) {
                field.removeChild(domShips[i]);
                domShips[i] = null;
            } else if (typeof domShips[i] === "undefined") {
                domShips[i] = document.createElement('div');
                domShips[i].className='ship';
                domShips[i].style.left = ships[i].x+'px';
                domShips[i].style.top = ships[i].y+'px';
                domShips[i].style[rotate] = "rotate(" + ships[i].angle + "rad)";
                domShips[i].innerHTML="<p>"+ships[i].count+"</p>";
                field.appendChild(domShips[i]);
            } else {
                domShips[i].style.left = ships[i].x+'px';
                domShips[i].style.top = ships[i].y+'px';
            }
        }
        for (i=0; i<domShips.length; i++) {
            if (domShips[i] === null) {
                domShips.splice(i, 1);
                i -= 1;
            }
        }
    }
    this.update = function() {
        this.syncPlanets();
        this.syncShips();
    }
}

function Game(gameField) {
    var view;
    var model;
    var ctrl;
    
    var field = document.getElementById(gameField);
    var timer;
    this.start = function() {
        if (timer) {
            this.stop();
            this.start();
        } else {
            view = new DOMView(field);
            model = new GameModel();
            ctrl = new InputController(field);
            
            view.setModel(model);
            ctrl.setModel(model);
            model.setView(view);
            
            model.newLevel();
            timer = setInterval(function() {model.next()}, 33);
        }
    }
    this.stop = function() {
        clearInterval(timer);
        timer = null;
        view.done();
        ctrl.done();
        model.done();
        
        view = null;
        ctrl = null;
        model = null;
    }
}