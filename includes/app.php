<?php
defined('_ok') or die('Прямой доступ запрещен');
class App {
    public $siteName;
    public $charset = 'utf-8';
    public $params;
    public $siteRoot;
    public $moduleRoot;
    public $messages = array();
    public $logined = false;
    public $usrId = 0;
    public $mysqli;
    private $charsetMysqli = 'utf8';
    private $content;
    private $styles = "";
    
    public function addStyle($src) {
        $this->styles .= '<link href="'.$src.'" type="text/css" rel="stylesheet">'."\n";
    }
    
    private function inc($path) {
        ob_start();
        include $path;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    public function __construct() {
        session_start();
        if (file_exists('config.php'))
            include 'config.php';
        $c = new config();
        $this->mysqli = new mysqli($c->db_hostname, $c->db_username, $c->db_password, $c->db_database);
        unset($c);
        if ($this->mysqli->connect_error)
            die('Подключение к базе не удачно, нужно проверить настройки ' . $this->mysqli->connect_error);
        else {
            $this->mysqli->set_charset($this->charsetMysqli);
            $result = $this->mysqli->query('SELECT value FROM configs WHERE name="sitename"');
            if ($this->mysqli->affected_rows === 1) {
                $row = $result->fetch_assoc();
                $this->siteName = $row['value'];
                if (isset($_SESSION['id'])) {
                    $this->logined = true;
                    $this->usrId = $_SESSION['id'];
                }
            }
            else
                die("Траблы, пересоздайте базу<br /><a href='install'>Настройка</a><br />\n");
        }
    }
    public function pageLoad() {
        $url = isset($_GET['url']) ? $_GET['url'] : 'galcon';
        $this->siteRoot = dirname($_SERVER['PHP_SELF']);
        if ($this->siteRoot === '\\') $this->siteRoot = '';
        $component = $this->componentLoad($url);
        if ($component) {
            $dat = &$this->content;
            //загружаю все модули для этого компонента
            $query = "SELECT module_id FROM relations WHERE component_id='$component[0]'";
            $result = $this->mysqli->query($query);
            while ($row = $result->fetch_array()) {
                $module = $this->moduleLoad($row[0]);
                if ($module)
                    $dat[$module['pos']] = 
                        isset($dat[$module['pos']]) ? $dat[$module['pos']]."<br />".$module['content'] :
                            $module['content'];
            }
            $dat['component'] = $component[1];
            $dat['footer'] = 'Сайт сделан при помощи нанотехнологий';
            include 'template/view.php';
        }
    }
    
    private function moduleLoad($id) {
        $result = $this->mysqli->query("SELECT name, pos, params FROM modules WHERE id='$id'");
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $path = 'modules/'.$row['name'].'/index.php';
            if (file_exists($path)) {
                $this->moduleRoot = dirname($path);
                $this->params = $row['params'];
                $out = array("pos" => $row['pos'], "content" => $this->inc($path));
                return $out;
            }
        }
        return false;
    }
    
    private function componentLoad($url) {
        $url = explode('/', htmlspecialchars($url), 2);
        $component = $this->mysqli->real_escape_string($url[0]);
        $result = $this->mysqli->query(sprintf('SELECT id FROM components WHERE name=%s', "'" . $component . "'"));
        if ($this->mysqli->errno === 0) {
            if ($result->num_rows === 1) {
                $row = $result->fetch_array();
                $path = 'components/' . $component . '/index.php';
                if (file_exists($path)) {
                    $this->moduleRoot = dirname($path);
                    if (isset($url[1]))
                        $this->params = $url[1];
                    include $path;
                    if (class_exists('Component')) {
                        $comp = new Component($this);
                        ob_start();
                        $comp->display();
                        $output = ob_get_contents();
                        ob_end_clean();
                        $out = array($row[0], $output);
                        return $out;
                    } echo 'Неправильно создан index.php у компонента '.$url[0];
                } else echo 'Нету файла ' . $path;
            } else echo 'Компонент не найден в базе';
        } else echo 'Пересоздайте базу данных ' . $this->mysqli->error;
    }
}
?>