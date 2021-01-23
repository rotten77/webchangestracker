<?php
header("Content-Type: text/plain");
include dirname(__FILE__) . "/app/app.php";

$log = '';

# Track page
foreach($db->cron_list->limit(10) as $website) {
    $log .= 'Tracking: '.$website['label'].PHP_EOL;

    $data = ($App->parseUrl($website['id']));

    $now = new NotORM_Literal("NOW()");
    
    foreach($data as $item) {
        $log .= '  '.$item['item_id'].PHP_EOL;
        
        $item['website_id'] = $website['id'];
        $item['occurrence_first'] = $now;
        $item['occurrence_last'] = $now;

        $db->records()->insert_update(
            array("item_id" => $item['item_id']),
            $item,
            array("occurrence_last" => $now)
        );
    }

    $website->update(array('tracking_last' => $now, 'tracking_priority' => 'schedule'));
}

if($log!='') {
    echo $log;
    $messageLog = $db->tracking_log()->insert(array(
        'tracking_timestamp' => new NotORM_Literal("NOW()"),
        'tracking_log' => $log
    ));  
} else {
    echo 'No tracking scheduled'.PHP_EOL;
}

# E-mail
$messageBody = "";
foreach($db->records->where('message_sent=0') as $record) {

    $messageBody.=$App->createMessage($record->website['message'], $record);
    $messageBody.=PHP_EOL.PHP_EOL;
    $messageBody.='<small>source: <a href="'.$record->website['url'].'">'.$record->website['label'].'</a></small>';
    $messageBody.='<hr />';
    $messageBody.=PHP_EOL.PHP_EOL;

    $record->update(array('message_sent' => 1));

}

if($messageBody!="") {

     $messageLog = $db->message_log()->insert(array(
        'message_sent' => new NotORM_Literal("NOW()"),
        'message_body' => $messageBody
    ));

    $messageHtml='<html><body>';
    $messageHtml.=$messageBody;
    $messageHtml.='</body></html>';
    if($App->sendHtmlEmail(EMAIL_ADDRESS, 'WebChangesTracker: new records ('.date('Y-m-d H:i').')', $messageHtml)) {
        echo 'CONFIRMATION: Message sent';
        $messageLog->update(array('message_status' => 'ok'));
    } else {
        echo 'ERROR: the message was not sent';
    }
}

# Maintenance
$db->records()->where("occurrence_last < '".date("Y-m-d H:i:s", strtotime("now - 300 day"))."'")->delete();
$db->tracking_log()->where("tracking_timestamp < '".date("Y-m-d H:i:s", strtotime("now - 30 day"))."'")->delete();
$db->message_log()->where("message_sent < '".date("Y-m-d H:i:s", strtotime("now - 30 day"))."'")->delete();