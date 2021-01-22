<?php
date_default_timezone_set('Europe/Prague'); //datová zóna
setlocale(LC_ALL, 'cs_CZ.UTF-8');

if(file_exists(dirname(__FILE__) . '/../config.dev.php')) {
    include dirname(__FILE__) . '/../config.dev.php';
} else {
    include dirname(__FILE__) . '/../config.php';
}