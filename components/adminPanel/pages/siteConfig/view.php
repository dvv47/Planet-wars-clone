<?php
defined('_ok') or die('Прямой доступ запрещен');
?>
<form method="post">
    <table>
        <tr>
            <td>Название сайта</td>
            <td><input type="text" value="<?=$this->site->siteName?>" name="sitename" /></td>
        </tr>
    </table>
    <input type="submit" value="Изменить" />
</form>