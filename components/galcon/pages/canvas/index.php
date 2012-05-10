<?php
$mysqli = $this->site->mysqli;

$this->userName = $mysqli->query("SELECT name 
        FROM users WHERE id='{$this->site->usrId}'")->fetch_array();
$this->userName = $this->userName[0];

if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $players = $_POST['players'];
    $query = sprintf("INSERT INTO galcon_rooms (name, players_now, players_max) VALUES
        ('%s', '1', '%d')", $mysqli->real_escape_string($name), (int) $players);
    $mysqli->query($query);
    $query = sprintf("INSERT INTO galcon_players (id, room, last_move) VALUES
        ('%d', '%d', 0)", $this->site->usrId, $mysqli->insert_id);
    $mysqli->query($query);
    if ($mysqli->error) die($mysqli->error);
}

if (isset($_GET['connect'])) {
    $room = (int) $_GET['connect'];
    $players = $mysqli->query(sprintf("SELECT players_now, players_max 
        FROM galcon_rooms WHERE id='%d'", $room))->fetch_assoc();
    if ($players['players_now'] < $players['players_max']) {
        $query = sprintf("INSERT INTO galcon_players (id, room, last_move) VALUES
        ('%d', '%d', 0)", $this->site->usrId, $room);
        $mysqli->query($query);
        $mysqli->query("UPDATE galcon_rooms SET players_now = players_now + 1
            WHERE id = '{$room}'");
    }
}

$room = $mysqli->query(sprintf("SELECT room
    FROM galcon_players WHERE id = '%s'",$this->site->usrId))->fetch_array();
if ($room[0]) {
    $this->task = "game";
} else {
    $this->task = "list";
    $result = $mysqli->query("SELECT id, name, players_now, players_max FROM galcon_rooms");
    $this->list = Array();
    while ($row = $result->fetch_assoc())
        $this->list[] = $row;
}
?>