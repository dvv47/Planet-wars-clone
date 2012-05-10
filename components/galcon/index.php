<?php
defined('_ok') or die('Прямой доступ запрещен');
class Component {
    private $site;
    private $_load = false;
    private $dir;

    public function __construct($site) {
        $this->site = $site;
        $this->_load = true;
        $params = array('', '', '');
        if (!empty($this->site->params))
            $params = explode('/', $this->site->params, 3);
        switch ($params[0]) {
            default :
            case 'canvas' :
                $this->dir = 'canvas';
                break;
            case 'dom' :
                $this->dir = 'dom';
                break;
            case 'server' :
                $this->dir = "server";
                break;
        }
        $this->dir = $this->site->moduleRoot.'/pages/'.$this->dir;
        if (file_exists($this->dir.'/index.php'))
            include $this->dir.'/index.php';
    }
    public function display() {
        if ($this->_load) {
            //echo "<a href='{$this->site->siteRoot}/galcon/dom'>dom</a> <a href='{$this->site->siteRoot}/galcon/canvas'>canvas</a><div class='clear'></div>";
            include $this->dir.'/view.php';
        }
    }
}
?>
