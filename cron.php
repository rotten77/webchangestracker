<?php
header("Content-Type: text/plain");
include dirname(__FILE__) . "/app/app.php";

# Track page
$website = $db->cron_list->limit(1)->fetch();

if($website['id']) {
    echo 'Tracking: '.$website['label'].PHP_EOL;

    $data = ($App->parseUrl($website['id']));

    $now = new NotORM_Literal("NOW()");
    
    foreach($data as $item) {
        echo '  '.$item['item_id'].PHP_EOL;
        
        $item['occurrence_first'] = $now;
        $item['occurrence_last'] = $now;

        $db->records()->insert_update(
            array("item_id" => $item['item_id']),
            $item,
            array("occurrence_last" => $now),
        );
    }

    $website->update(array('tracking_last' => $now, 'tracking_priority' => 'shedule'));
}