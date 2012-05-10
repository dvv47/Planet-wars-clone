<?php
defined('_ok') or die('Прямой доступ запрещен');
$mysqli = $this->site->mysqli;
if (isset($_POST['sitename'])) {
    $siteName = htmlspecialchars($_POST['sitename']);
    if (!empty($siteName)) {
        $query = "UPDATE configs SET value='{$siteName}' WHERE name='sitename'";
        $mysqli->query($query);
        $this->site->siteName = $siteName;
    }
}
?>