<?php
define('_ok', 1);
session_start();

function inc($path) {
    ob_start();
    include $path;
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

if (isset($_GET['page']))
    $page = (int) $_GET['page'];
else $page = 1;

switch ($page) {
    case 1: 
        $file = 'page1_db.php';
        $titlePostfx = 'Первый этап настройка подключения к базе';
     break;
    case 2:
        $file = 'page2_site.php';
        $titlePostfx = 'Второй этап настройка сайта';
     break;
    case 3:
        $file = 'page3_complete.php';
        $titlePostfx = 'Настройка сайта завершена';
     break;
    default:
        $file = 'page1_db.php';
        $titlePostfx = 'Первый этап настройка подключения к базе';
        break;
}
$title = 'Настройка сайта - '.$titlePostfx;
$self = $_SERVER['PHP_SELF'];
$menu = <<<EOD
<ul>
    <li><a href="$self?page=1">Настройка базы</a></li>
    <li><a href="$self?page=2">Настройка сайта</a></li>
</ul>
EOD;
$content = inc('pages/'.$file);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?=$title?></title>
        <link href="style.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <div id="all">
            <header>
            <?=$title?>
            </header>
            <nav><?=$menu?></nav>
            <article>
            <?=$content?>
            </article>
        </div>
    </body>
</html>