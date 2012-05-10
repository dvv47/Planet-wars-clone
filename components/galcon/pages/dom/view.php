<?php
$this->site->addStyle($this->dir."/gamestyle.css");
?>
<div id="main">
    <div id="game">
        <div>
            <button onclick="game.start()" >Start</button>
            <button onclick="game.stop()">Stop</button>
        </div>
        <div id="field"></div>
    </div>
    <div class="clear" style="height:15px"></div>
    <div id="debug"></div>
</div>
<script src="<?=$this->dir?>/game.js"></script>
<script>var game = new Game("field");</script>