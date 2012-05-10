<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
    "http://www.w3.org/TR/html4/strict.dtd">
<?php
$dat = &$this->content;
$this->addStyle($this->siteRoot."/template/style.css");
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$this->charset?>">
        <title><?=$this->siteName?></title>
        <?=$this->styles?>
    </head>
    <body>
        <div id="all">
            <div id="header">
                <div id="logo"><?=$this->siteName?></div>
            </div>
            <div class="clear"></div>
            <?php if (isset($dat['menu'])) { ?>
            <div id="menu">
                <?=$dat['menu']?>
            </div>
            <?php } ?>
            <?php if (isset($dat['login'])) { ?>
            <div id="login"><?=$dat['login']?></div>
            <?php } ?>
            <div id="content">
                <?php
                //if (!empty($this->messages)) {
                echo '<div id="messages">';
                    foreach ($this->messages as $message)
                        echo '<div>'.$message.'</div>';
                echo '</div>
                <div class="clear"></div>';
                //} 
                ?>
                <?=$dat['component']?>
            </div>
            <div class="clear"></div>
            <div id="footer"><?=$dat['footer']?></div>
        </div>
        <script>
            var messages = document.getElementById("messages");
            function message(text) {
                var message = document.createElement("div");
                message.innerHTML = text;
                messages.appendChild(message);
            }
            
            var ajaxSend = function(handler, params, url) {
                url = url || window.location.href;
                var ajax = new XMLHttpRequest();
                ajax.onreadystatechange = handler;
                ajax.open("POST", url, true);
                ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                ajax.send("ajax=true&"+params);
            }
        </script>
    </body>
</html>