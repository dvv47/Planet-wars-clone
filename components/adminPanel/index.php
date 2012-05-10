<?php
defined('_ok') or die('Прямой доступ запрещен');
class Component {
    private $site;
    private $params;
    private $_page;
    private $_load = false;
    
    public function __construct($site) {
        $this->site = $site;
        if ($this->site->logined) {
            $mysqli = $this->site->mysqli;
            $usrId = $this->site->usrId;
            $query = "SELECT level FROM groups INNER JOIN users ON users.group_id = groups.id WHERE users.id='{$usrId}'";
            $result = $mysqli->query($query);
            $row = $result->fetch_array();
            if ($row[0] == 0) {
                $this->_load = true;
                $params = array('', '', '');
                if (!empty($this->site->params)) {
                    $params = explode('/', $this->site->params, 3);
                    $this->params = isset($params[2]) ? $params[2] : '';
                }
                switch ($params[0]) {
                    default :
                    case 'config' :
                        switch ($params[1]) {
                            default :
                            case 'site' :
                                $this->_page = 'siteConfig';
                                break;
                            case 'menu' :
                                $this->_page = 'menuConfig';
                                break;
                        }
                        break;
                    case 'users' :
                        switch ($params[1]) {
                            default :
                            case 'new' :
                                $this->_page = 'newUser';
                                break;
                        }
                }
            include $this->site->moduleRoot.'/pages/'.$this->_page.'/index.php';
            } else $this->site->messages[] = 'Нельзя';
        } else $site->messages[] = 'Хорошо бы залогиниться';
    }
    public function display() {
        if ($this->_load)
            include $this->site->moduleRoot.'/pages/'.$this->_page.'/view.php';
    }
}
?>