<?php
defined('_ok') or die('Прямой доступ запрещен');
?>
<form action="" method="post">
    <table>
        <tr>
            <td>Логин</td>
            <td><input name="userName" type="text" required /></td>
        </tr>
        <tr>
            <td>Пароль</td>
            <td><input name="userPass" type="password" required /></td>
        </tr>
        <tr>
            <td colspan="2"><select name="userGroup">
                <?php 
                for ($i=0; $i<count($this->groups); $i++)
                    echo "<option value='{$this->groups[$i]['id']}'>{$this->groups[$i]['name']}</option>";
                ?>
            </select></td>
        </tr>
    </table>
    <br />
    <input type="submit" value="Далее" />
</form>