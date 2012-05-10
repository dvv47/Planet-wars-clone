<?php
class Game {
    public function __construct($site) {
        $this->mysqli = $site->mysqli;
        $result = $this->mysqli->query(sprintf("SELECT id, room, last_move FROM galcon_players
                WHERE id='%s'", $site->usrId));
        if ($result->num_rows === 1)
            $this->player = $result->fetch_assoc();
    }
    
    public function create($name, $players_max) {
        $map = json_encode($this->generateMap());
        $query = sprintf("INSERT INTO galcon_rooms (name, players_now, players_max, map) VALUES
        ('%s', '1', '%d', '%s')", 
        $mysqli->real_escape_string($name), (int) $players_max, $map);
        $this->mysqli->query($query);
    }
    
    public function connect($roomId) {
        $roomId = (int) $roomId;
        $players = $mysqli->query(sprintf("SELECT players_now, players_max 
        FROM galcon_rooms WHERE id='%d'", $roomId))->fetch_assoc();
        if ($players['players_now'] < $players['players_max']) {
            $this->mysqli->query(sprintf("INSERT INTO galcon_players 
                (id, room, last_move) VALUES ('%d', '%d', 0)", 
                    $this->site->usrId, $roomId));
            $this->mysqli->query("UPDATE galcon_rooms SET players_now = players_now + 1
                WHERE id = '{$roomId}'");
        }
    }
    
    public function doMove($move) {
        if ($this->_checkMove($move)) {
            $player = $this->mysqli->query(sprintf("SELECT room FROM galcon_players
                WHERE id='%s'", $player['id']))->fetch_assoc();
            if (!$player)
                return false;
            $this->mysqli->query(sprintf("INSERT INTO galcon_moves (room_id, move) VALUES
                (%d, '%s')", int($player['room']), $move));
        }
    }
    
    public function getMap() {
        $map = $this->mysqli->query(sprintf("SELECT map FROM galcon_rooms WHERE room = '%d'", 
                $this->player['room']))->fetch_array();
        return $map[0];
    }
    
    public function getMoves() {
        $result = $this->mysqli->query(sprintf("SELECT move_time, move FROM galcon_moves
        WHERE room_id='%s' and move_time>='%s'", $this->player['room'], $this->player['last_move']));
        $aMoves = false;
        while ($row = $result->fetch_array())
            $aMoves[] = $row;
        return $aMoves;
    }
    
    //name, color
    public function getPlayers() {
        $result = $this->mysqli->query(sprintf("SELECT name, color FROM galcon_players INNER JOIN users on galcon_players.id = users.id WHERE room = '%s'",$this->player['room']));
        $players = Array();
        while ($row = $result->fetch_assoc)
            $players[] = $row;
        return $players;
    }
    
    private function checkPlanet($x, $y, $R) {
        foreach ($this->map as $planet)
        if ((abs($planet['x'] - $x) < $planet['R'] + $R) &&
                (abs($planet['y'] - $y) < $planet['R'] + $R))
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
        $map = Array();
        while (count($map)<10) {
            $p['R'] = rand($minR, $maxR);
            $p['x'] = rand(0, $height);
            $p['y'] = rand(0, $width);
            $p['shipsCount'] = rand($minShips, $maxShips);
            if (!$this->checkPlanet($p['x'], $p['y'], $p['R']))
                $map[] = $p;
        }
        return $map;
    }
    
    private function _checkMove($move) {
        if (substr($move, 0, 7) === "sendShip") {
            return true;
        } else return false;
    }
    
    
    // System
    private $mysqli;
    
    // Game
    private $player;
}
?>
