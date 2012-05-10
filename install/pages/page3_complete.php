<?php
defined('_ok') or die('Прямой доступ запрещен');
if (isset($_SESSION['page1_db']) && isset($_SESSION['page2_site'])) {
echo <<<EOD
   Настройка сайта успешно завершена<br />
   Перейти в панель управления сайтом -> <a href="../adminko">Админ панель</a><br />
   Перейти на сайт <a href="../">Сайт</a><br />
EOD;
} else {
echo "Настройка не завершена<br />";
if (!isset($_SESSION['page1_db'])) 
    echo "Не настроено подключение к базе данных <a href=\"{$_SERVER['PHP_SELF']}?page=1\">Настройка бд</a>";
else if (!isset($_SESSION['page2_site'])) echo "Не настроено подключение к базе данных <a href=\"<{$_SERVER['PHP_SELF']}?page=2\">Настройка сайта</a>";
}
?>