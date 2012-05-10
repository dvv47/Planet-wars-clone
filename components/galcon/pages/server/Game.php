<?php
class Game {
    public function __construct($site) {
        $this->mysqli = $site->mysqli;
        $result = $this->mysqli->query(sprintf("SELECT id, room, last_move FROM galcon_players
                WHERE id='%s'", $site->usrId));
        if ($result->num_rows === 1)
            $this->player = $result->fetch_assoc();
    }
    
    public function update() {
        if (!$this->player) return Array("status" => false,
                    "message" => "Необходимо подключиться");
        if (!isset($_GET['galcon'])) return Array("status" => false,
                    "message" => "Нет параметров");
        
        $inp = json_decode($_REQUEST['galcon'], true);
        switch ($inp['task']) {
            case "doMove" :
                if ($this->doMove())
                    $result = Array("status" => true, "message" => "");
                else $result = Array("status" => false,
                    "message" => "Ход не удался");
                break;
            case "getMoves" :
                $list = $this->getMoves();
                if ($list)
                    $result = Array("status" => true,
                        "message" => $list);
                else $result = Array("status" => false,
                    "message" => "Получить ходы не удалось");
                break;
            case "getMap" :
                $this->generateMap();
                $result = $this->getMap();
                break;
            case "getPlayers" :
                $this->updatePlayers();
                $result = $this->getPlayers();
                break;
            default:
                $result = Array("status" => false,
                    "message" => "Неверная команда");
                break;
        }
        return $result;
    }
    
    private function _checkPlanet($x, $y, $R) {
        foreach ($this->map as $planet)
        if ((abs($planets['x'] - $x) < $planet['R'] + $R) &&
                (abs($planets['y'] - $y) < $planet['R'] + $R))
            return true;
        else return false;
    }
    
    private function generateMap() {
        $maxR = 40;
        $minR = 10;
        $minShips = 0;
        $maxShips = 100;
        $width = 600;
        $height = 400;
      
        $p = Array();
        $this->map = Array();
        while (count($this-map)<10) {
            $p['R'] = floor(random() * ($maxR-$minR+1)) + $minR;
            $p['x'] = floor(random() * ($width - $p['R']*2 - 50) + 25);
            $p['y'] = floor(random() * ($height - $p['R']*2 - 50) + 25);
            $p['shipsCount'] = floor(Math.random() * ($maxShips-$minShips+1)) + $minShips;
            if ($this->_checkPlanet($p['x'], $p['y'], $p['R']))
                $this->map[] = $p;
        }
    }
    
    private function getMap() {
        return $this->map;
    }
    
    private function getMoves() {
        $result = $this->mysqli->query(sprintf("SELECT move_time, move FROM galcon_moves
        WHERE room_id='%s' and move_time>='%s'", $this->player['room'], $this->player['last_move']));
        $aMoves = false;
        while ($row = $result->fetch_array())
            $aMoves[] = $row;
        return $aMoves;
    }
    
    private function updatePlayers() {
        $result = $this->mysqli->query(sprintf("SELECT name, color FROM galcon_players INNER JOIN users on galcon_players.id = users.id WHERE room = '%s'",$this->player['room']));
        $this->players = Array();
        while ($row = $result->fetch_assoc)
            $this->players[] = $row;
    }
    //name, color
    private function getPlayers() {
        return $this->players;
    }
    
    private function _checkMove($move) {
        if (substr($move, 0, 7) === "sendShip") {
            return true;
        } else return false;
    }
    
    private function doMove($move) {
        if ($this->_checkMove($move)) {
            $player = $this->mysqli->query(sprintf("SELECT room FROM galcon_players
                WHERE id='%s'", $player['id']))->fetch_assoc();
            if (!$player)
                return false;
            $this->mysqli->query(sprintf("INSERT INTO galcon_moves (room_id, move) VALUES
                (%d, '%s')", int($player['room']), $move));
        }
    }
    // System
    private $mysqli;
    
    // Game
    private $player;
    private $players;
    private $map;
}
?>
