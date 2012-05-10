<?php
defined('_ok') or die('Прямой доступ запрещен');
if (isset($_POST['db_hostname'])) {
    $mysqli = new mysqli($_POST['db_hostname'], $_POST['db_username'], $_POST['db_password'], $_POST['db_database']);
    if ($mysqli->connect_errno === 0) {
        
        $_SESSION['db_hostname'] = $_POST['db_hostname'];
        $_SESSION['db_database'] = $_POST['db_database'];
        $_SESSION['db_username'] = $_POST['db_username'];
        $_SESSION['db_password'] = $_POST['db_password'];

        $_SESSION['page1_db'] = TRUE;
        header("Location: {$_SERVER['PHP_SELF']}?page=2");
    } else
        echo 'Невозможно подключиться ' . $mysqli->connect_error;
}
?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <table>
        <tr>
            <td>Адрес сервера базы данных</td>
            <td><input name="db_hostname" type="text" value="localhost" title="Обычно localhost" required /></td>
        </tr>
        <tr>
            <td>Имя базы</td>
            <td><input name="db_database" type="text" required <?php if (isset($_POST['db_database'])) echo 'value="' . $_POST['db_database'] . '"'; ?>/></td>
        </tr>
        <tr>
            <td>Логин к базе</td>
            <td><input name="db_username" type="text" required <?php if (isset($_POST['db_database'])) echo 'value="' . $_POST['db_database'] . '"'; ?>/></td>
        </tr>
        <tr>
            <td>Пароль к базе</td>
            <td><input name="db_password" type="password" required <?php if (isset($_POST['db_database'])) echo 'value="' . $_POST['db_database'] . '"'; ?>/></td>
        </tr>
    </table>
    <br />
    <input type="submit" value="Далее" />
</form>