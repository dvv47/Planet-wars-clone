<?php
defined('_ok') or die('Прямой доступ запрещен');
if (!isset($_SESSION['page1_db'])) echo 'Настройка базы не выполнена';
else
if (isset($_POST['siteName'])) {
    $mysqli = new mysqli($_SESSION['db_hostname'], $_SESSION['db_username'], $_SESSION['db_password'], $_SESSION['db_database']);
    if ($mysqli->connect_errno === 0) {
        $mysqli->set_charset('utf8');
        $siteName = $mysqli->real_escape_string($_POST['siteName']);
        $adminName = $mysqli->real_escape_string($_POST['adminName']);
        $adminPass = $mysqli->real_escape_string($_POST['adminPass']);
        $adminPass = md5($adminPass);
        $mysqli->query('DROP TABLE users, configs, components, menu, menu_list, modules, relations, works, works_list, groups');
        $mysqli->query('CREATE TABLE users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) UNIQUE,
            password VARCHAR(32),
            group_id INT UNSIGNED)');
        $mysqli->query('CREATE TABLE configs (
            name VARCHAR(30) NOT NULL PRIMARY KEY,
            value VARCHAR(128))');
        $mysqli->query('CREATE TABLE components (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(128) NOT NULL)');
        $mysqli->query('CREATE TABLE modules (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(128),
            name VARCHAR(128),
            pos VARCHAR(128),
            params TEXT)');
        $mysqli->query('CREATE TABLE works_list (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(128),
            description TEXT,
            admin_id INT UNSIGNED,
            status VARCHAR(30),
            created DATE,
            updated DATE)');
        $mysqli->query('CREATE TABLE works (
            work_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL)');
        if ($mysqli->error) die($mysqli->error);
        $mysqli->query('CREATE TABLE relations (
            component_id INT UNSIGNED NOT NULL,
            module_id INT UNSIGNED NOT NULL)');
        $mysqli->query('CREATE TABLE menu_list (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            menu_id INT UNSIGNED,
            name VARCHAR(128),
            href VARCHAR(128))');
        $mysqli->query("INSERT INTO menu_list (menu_id, name, href) VALUES
            ('1', 'Главная', '/'),
            ('1', 'Настройки', '/install'),
            ('1', 'Админка', '/adminPanel'),
            ('1', 'Управляющая часть', '/managePanel'),
            ('1', 'Галактика', '/galcon'),
            ('1', 'Тесты', '/tests'),
            ('2', 'Настройка менюшек', '/adminPanel/config/menu'),
            ('2', 'Настройка сайта', '/adminPanel/config/site'),
            ('2', 'Рега пользователей', '/adminPanel/users/new')");
        $mysqli->query("CREATE TABLE menu (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(128))");
        $mysqli->query("INSERT INTO menu (title) VALUES 
            ('Главное меню'),
            ('Админ меню')");
        if ($mysqli->error) die($mysqli->error);
        /* $mysqli->query('CREATE TABLE groups (
            id INT(10) NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            left_key INT(10) NOT NULL DEFAULT 0,
            right_key INT(10) NOT NULL DEFAULT 0,
            level INT(10) NOT NULL DEFAULT 0,
            PRIMARY KEY id,
            INDEX left_key (left_key, right_key, level))');
         */
        $mysqli->query('CREATE TABLE groups (
            id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(128) NOT NULL,
            level INT NOT NULL)');
        if ($mysqli->error) die($mysqli->error);
        $mysqli->query("INSERT INTO groups (name, level) VALUES
            ('Кодеры', 0),
            ('Модеры', 1),
            ('Юзеры', 2),
            ('Гости', 2)");
        $mysqli->query("INSERT INTO components (name) VALUES 
            ('adminPanel'),
            ('galcon')");
        $mysqli->query("INSERT INTO relations (component_id, module_id) VALUES 
            ('1', '1'),
            ('1', '2'),
            ('1', '3'),
            ('2', '1'),
            ('2', '2')");
        $mysqli->query("INSERT INTO modules (title, name, pos, params) VALUES 
            ('Главное меню', 'mod_menu', 'menu', '1'),
            ('Логинилка', 'mod_login', 'login', ''),
            ('Админ меню', 'mod_menu', 'menu', '2')");
        $mysqli->query(sprintf("INSERT INTO users (name, password, group_id) VALUES 
            ('%s', '%s', '1')", $adminName, $adminPass));
        $mysqli->query(sprintf("INSERT INTO configs VALUES
            ('sitename', '%s')", $siteName));
$conf = '<?php
class config {
    public $db_hostname = \''.$_SESSION['db_hostname'].'\';
    public $db_username = \''.$_SESSION['db_username'].'\';
    public $db_password = \''.$_SESSION['db_password'].'\';
    public $db_database = \''.$_SESSION['db_database'].'\';
}?>';
        file_put_contents('../config.php', $conf);
        $_SESSION['page2_site'] = TRUE;
        header("Location: {$_SERVER['PHP_SELF']}?page=3");
    } else echo 'Не удалось подключиться к базе '.$mysqli->connect_error;
} else {
?>
<form action="<?=$_SERVER['PHP_SELF']?>?page=2" method="post">
    <table>
        <tr>
            <td>Название сайта</td>
            <td><input name="siteName" type="text" required /></td>
        </tr>
        <tr>
            <td>Логин админа</td>
            <td><input name="adminName" type="text" required /></td>
        </tr>
        <tr>
            <td>Пароль админа</td>
            <td><input name="adminPass" type="password" required /></td>
        </tr>
    </table>
    <br />
    <input type="submit" value="Далее" />
</form>
<?php
}
?>