<?php
defined('_ok') or die('Прямой доступ запрещен');
$mysqli->query("DROP TABLE IF EXISTS galcon_rooms, galcon_players, galcon_moves");
$query = "CREATE TABLE galcon_rooms (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128),
    players_now TINYINT UNSIGNED,
    players_max TINYINT UNSIGNED)";
$mysqli->query($query);
if ($mysqli->error) 
    die($mysqli->error);
$query = "CREATE TABLE galcon_players (
    id TINYINT UNSIGNED,
    room TINYINT UNSIGNED,
    color VARCHAR(32),
    last_move TIMESTAMP(4))";
$mysqli->query($query);
if ($mysqli->error) 
    die($mysqli->error);
$query = "CREATE TABLE galcon_moves (
    room_id TINYINT UNSIGNED,
    move_time TIMESTAMP(4),
    move VARCHAR(128))";
$mysqli->query($query);
if ($mysqli->error) die($mysqli->error);
?>