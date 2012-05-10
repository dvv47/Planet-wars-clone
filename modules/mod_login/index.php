<?php
defined('_ok') or die('Прямой доступ запрещен');
$site = $this;
$ajax = isset($_REQUEST['ajax']) ? true : false;
$mysqli = $site->mysqli;
if ($site->logined) {
    if (isset($_REQUEST['logout'])) {
        unset($_SESSION['id']);
        if ($ajax) {
            $result = array("status" => true);
            die(json_encode($result));
        }
        $site->messages[] = "<script>window.location.href='{$site->siteRoot}/'</script>";
    }
    $query = "SELECT name FROM users WHERE id='{$site->usrId}'";
    $result = $mysqli->query($query);
    $row = $result->fetch_array();
?>
<table>
    <tr>
        <td>Ку</td>
        <td><?=$row[0]?></td>
    </tr>
    <tr>
        <td><button onclick="ajaxSend('logout=true&ajax=true', ajaxResult)">Выйти</button></td>
    </tr>
</table>
<?php 
} else {
    if (isset($_POST['name'])) {
        $userName = $mysqli->real_escape_string($_POST['name']);
        $userPass = md5($_POST['pass']);
        if (isset($_POST['login'])) {
            $query = sprintf("SELECT id FROM users WHERE name='%s' and password='%s'", $userName, $userPass);
            $result = $mysqli->query($query);
            if ($mysqli->affected_rows === 1) {
                $row = $result->fetch_array();
                $_SESSION['id'] = $row[0];
                $result = array("status" => true,
                    "message" => "weee it works! Refresh page
                <script>window.location.reload(true);</script>");
                if ($ajax) die(json_encode($result));
                $site->messages[] = $result['message'];
            } else {
                $result = array("status" => false,
                    "message" => "login or pass fail, 
                <br />Возможно вы имели в виду dvv47 VjqGfcRhextXfrf!");
                if ($ajax) die(json_encode($result));
                $site->messages[] = $result['message'];
            }
        }
        if (isset($_POST['reg'])) {
            $result = $mysqli->query("SELECT name FROM users WHERE name='{$userName}'");
            if ($result->num_rows === 0) {
                $mysqli->query(sprintf("INSERT INTO users (name, password, group_id) VALUES 
                    ('%s', '%s', '%d')", $userName, $userPass, 4));
                $_SESSION['id'] = $mysqli->insert_id;
                $result = array("status" => true,
                    "message" => "Аккаунт зарегистрирован удачно");
            } else {
                $result = array("status" => false,
                    "message" => "Этот логин занят, используйте другой");
            }
            if ($ajax) die(json_encode($result));
            $site->messages[] = $result["message"];
        }
}
?>
<form name="auth" action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return false">
    <table>
        <tr>
            <td>Логин</td>
            <td><input type="text" name="name" required></td>
        </tr>
        <tr>
            <td>Пас</td>
            <td><input type="password" name="pass" required></td>
        </tr>
    </table>
    <p><button onclick="send('login')">Войти</button> <button onclick="send('reg')">Зарегистрироваться</button></p>
</form>
<script>
    function send(param) {
        var form = document.forms['auth'];
        var hnd = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    var result = eval("("+this.responseText+")");
                    if (result.status)
                        window.location.reload(true);
                    else message(result.message);
                }
            }
        }
        if (form.checkValidity())
            ajaxSend(hnd, "name="+form.name.value+"&pass="+form.pass.value+"&"+param+"=true");
    }
</script>
<?php } ?>