<?php
defined('_ok') or die('Прямой доступ запрещен');
foreach ($_REQUEST as $key => $value) {
        echo $key." ".$value."<br>\n";    
    }

$mysqli = $this->site->mysqli;
//$mysqli = new mysqli();
$dbCheck = $mysqli->query("SELECT 1 FROM information_schema.tables
    WHERE table_name = 'galcon_rooms' or 
    table_name = 'galcon_players' or 
    table_name = 'galcon_moves'")->num_rows;
if ($dbCheck !== 3)
    include $this->dir."/init.php";

include $this->dir."/Game.php";
$game = new Game($this->site);
$this->result = $game->update();
var_dump($this->result);
?>