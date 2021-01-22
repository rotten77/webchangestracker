<?php
include dirname(__FILE__) . "/../app/app.config.php";

function adminer_object() {
    include_once dirname(__FILE__) . '/plugins/plugin.php';
    include_once dirname(__FILE__) . '/plugins/app.database.php';
    
    $plugins = array(
       new AppDatabase(),
    );
    
    return new AdminerPlugin($plugins);
}

include "./editor.php";