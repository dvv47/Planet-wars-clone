<?php
$this->site->addStyle($this->dir."/gamestyle.css");
if ($this->task === "game") {
?>
    <canvas id="field"></canvas>
    <script src="<?=$this->dir?>/game.js"></script>
    <script>
        var game = new Game("/galcon/server");
        game.start();
    </script>
<?php
} else if ($this->task === "list") { ?>
Текущие игры<br>
<table id='game_list' border="1">
    <tr>
        <td>Название</td>
        <td>Игроки</td>
        <td>Подключиться</td>
    </tr>
<?php
foreach ($this->list as $row) 
echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['players_now']} из {$row['players_max']}</td>
        <td><a href='?connect={$row['id']}'>Подключиться</a></td>
    </tr>";
?>
</table><br>
Создать игру
<form action="#" method="post">
    Название<br>
    <input name="name" type="text" value="<?=$this->userName?>_game"><br>
    Максимум игроков<br>
    <input name="players" type="text" value="16"><br>
    <input type="hidden" name="create" value="true">
    <input type="submit" value="Создать"></input>
</form>
<?php } ?>