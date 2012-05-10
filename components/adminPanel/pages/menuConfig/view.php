<?php
defined('_ok') or die('Прямой доступ запрещен');

if (isset($this->menuSwitch)) { ?>
<div id="menuselect">
    <form method="get">
        <select name="menuselect">
        <?php
            foreach ($this->menuSwitch as $row) {
                echo "<option value='{$row['id']}'>{$row['title']}</option>";
            }
        ?>
        </select>
    <input type="submit" value="Выбрать" />
    </form>
</div>
<?php 
} else echo 'Нету мешюшек';
if (isset($this->menuConf)) { ?>
<div id="menuconf">
    <form action="#" method="post">
        <table>
            <tr>
                <td>Название</td>
                <td>Ссылка</td>
                <td>Удалить</td>
            </tr>
            <?php
                foreach ($this->menuConf as $row) {
                    echo "<tr><td><input type='text' name=\"menu[{$row['id']}][name]\" value='{$row['name']}' /></td>";
                    echo "<td><input type='text' name=\"menu[{$row['id']}][href]\" value='{$row['href']}' /></td>";
                    echo "<td><a href=\"?del={$row['id']}\">Удалить</a></td></tr>";
                }
            ?>
        </table>
    <input type="submit" value="Сохранить" />
    </form>
    <form action="#" method="post">
        <table>
            <tr>
                <td><input type="text" name="addname" value="Заголовок" /></td>
                <td><input type="text" name="addhref" value="url" /></td>
            </tr>
        </table>
        <input type="hidden" name="menuid" value="<?=$this->menuSelect?>" />
        <input type="submit" value="Добавить" />
    </form>
    
</div>
<?php } ?>