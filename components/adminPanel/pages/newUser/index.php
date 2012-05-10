<?php
defined('_ok') or die('Прямой доступ запрещен');
$mysqli = $this->site->mysqli;
if (isset($_POST['userName'])) {
    $userName = $mysqli->real_escape_string($_POST['userName']);
    $userPass = $mysqli->real_escape_string($_POST['userPass']);
    $userPass = md5($userPass);
    $userGroup = $mysqli->real_escape_string($_POST['userGroup']);
    
    $result = $mysqli->query(sprintf("SELECT name FROM groups WHERE id='%d'", $userGroup));
    $row = $result->fetch_array();
    $groupName = $row[0];
    
    $mysqli->query(sprintf("INSERT INTO users (name, password, group_id) VALUES 
                ('%s', '%s', '%d')", $userName, $userPass, $userGroup));
    $this->site->messages[] = "Юзер {$userName} успешно зарегистрирован в группе {$groupName}";
}
$result = $mysqli->query("SELECT id, name FROM groups");
    while ($row = $result->fetch_assoc()) {
        $this->groups[] = $row;
    }
?>
