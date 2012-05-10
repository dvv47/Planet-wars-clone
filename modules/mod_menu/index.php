<?php
defined('_ok') or die('Прямой доступ запрещен');
$site = $this;
$result = $site->mysqli->query(sprintf("SELECT title FROM menu WHERE id='%s'",$site->params));
$row = $result->fetch_array();
$menuName = $row[0];
$result = $site->mysqli->query(sprintf("SELECT name, href FROM menu_list WHERE menu_id='%s'",$site->params));
if ($result->num_rows>0) {
    echo $menuName;
    echo '<ul>';
    for ($i=0; $i<$result->num_rows; $i++) {
        $row = $result->fetch_assoc();
        $href = $site->siteRoot.$row['href'];
        echo "<li><a href='{$href}'>{$row['name']}</a></li>\n";
    }
    echo '</ul>';
}
?>