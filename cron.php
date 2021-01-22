<?php
header("Content-Type: text/plain");
include dirname(__FILE__) . "/app/app.php";

# Track page
foreach($db->cron_list->limit(10) as $website) {
    echo 'Tracking: '.$website['label'].PHP_EOL;

    $data = ($App->parseUrl($website['id']));

    $now = new NotORM_Literal("NOW()");
    
    foreach($data as $item) {
        echo '  '.$item['item_id'].PHP_EOL;
        
        $item['website_id'] = $website['id'];
        $item['occurrence_first'] = $now;
        $item['occurrence_last'] = $now;

        $db->records()->insert_update(
            array("item_id" => $item['item_id']),
            $item,
            array("occurrence_last" => $now)
        );
    }

    $website->update(array('tracking_last' => $now, 'tracking_priority' => 'shedule'));
}

# E-mail
$messageBody = "";
foreach($db->records->where('message_sent=0') as $record) {

    $messageBody.=$App->createMessage($record->website['message'], $record);
    $messageBody.=PHP_EOL.PHP_EOL;
    $messageBody.='<small>zdroj: <a href="'.$record->website['url'].'">'.$record->website['label'].'</a></small>';
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