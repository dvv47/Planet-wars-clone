<?php
defined('_ok') or die('Прямой доступ запрещен');

function error($msg) {
    die(json_encode(Array("status" => false,
            "message" => $msg)));
}

$mysqli = $this->site->mysqli;
$dbCheck = $mysqli->query("SELECT 1 FROM information_schema.tables
    WHERE table_name = 'galcon_rooms' or 
    table_name = 'galcon_players' or 
    table_name = 'galcon_moves'")->num_rows;
if ($dbCheck !== 3)
    include $this->dir."/init.php";

if (!$this->site->logined)
    error("Необходимо подключиться");

include $this->dir."/Game.php";
$game = new Game($this->site);

if (!isset($_REQUEST['galcon'])) 
    error("Нет параметров");

$inp = json_decode($_REQUEST['galcon'], true);
switch ($inp['task']) {
    case "doMove" :
        if ($game->doMove($inp['move']))
            $this->result = Array("status" => true, "message" => "");
        else error("Ход не удался");
        break;
    case "getMoves" :
        $list = $game->getMoves();
        if ($list)
            $this->result = Array("status" => true,
                "message" => $list);
        else error("Получить ходы не удалось");
        break;
    case "getMap" :
        $this->generateMap();
        $this->result = $game->getMap();
        break;
    case "getPlayers" :
        $this->updatePlayers();
        $this->result = $game->getPlayers();
        break;
    case "createGame" :
        $game->create($inp['name'], $inp['players']);
        break;
    case "connectGame" :
        $game->connect($inp['roomId']);
        break;
    default:
        error("Неверная команда");
        break;
}

if (isset($_REQUEST['debug'])) {
    foreach ($_REQUEST as $key => $value) {
            echo $key." ".$value."<br>\n";    
    }
    var_dump($this->result);
}
?>