<?php
defined('_ok') or die('Прямой доступ запрещен');
$mysqli = $this->site->mysqli;

$query = "SELECT id, title FROM menu";
$result = $mysqli->query($query);
if ($result->num_rows > 0)
while ($row = $result->fetch_assoc())
    $this->menuSwitch[] = $row;
$menuSelect = isset($_REQUEST['menuselect']) ? (int) $_REQUEST['menuselect'] : 1;

if (isset($_GET['del'])) {
    $del = (int) $_GET['del'];
    $mysqli->query("DELETE FROM menu_list WHERE id='{$del}'");
}

if (isset($_POST['menu'])) {
    $menu = $_POST['menu'];
    if (is_array($menu)) {
        foreach ($menu as $key => $row) {
            $mysqli->query(sprintf("UPDATE menu_list SET name='%s', href='%s' WHERE id='%d'", $row['name'], $row['href'], $key));
            if (!empty($mysqli->error))
                $this->site->messages[] = $this->mysqli->error;
        }
    }
}

if (isset($_POST['addname'])) {
    $name = $_POST['addname'];
    $href = $_POST['addhref'];
    $menuId = (int) $_POST['menuid'];
    $query = sprintf("INSERT INTO menu_list (menu_id, name, href) VALUES
        ('%d', '%s', '%s')", $menuId, $name, $href);
    $mysqli->query($query);
}

if ($menuSelect !== 0) {
    $query = "SELECT id, name, href FROM menu_list WHERE menu_id='{$menuSelect}'";
    $result = $mysqli->query($query);
    while ($row = $result->fetch_assoc())
        $this->menuConf[] = $row;
    $this->menuSelect = $menuSelect;
}
?>