<?php
header("Content-Type: text/plain");
include dirname(__FILE__) . "/app/app.php";

$log = '';

# Track page
foreach($db->cron_list->limit(10) as $website) {
    $log .= PHP_EOL.'Tracking: '.$website['label'].PHP_EOL;

    $data = ($App->parseUrl($website['id']));

    $now = new NotORM_Literal("NOW()");
    
    foreach($data as $item) {
        
        $item['item_id'] = trim($item['item_id']);
        
        // Check if exists
        $rowWhere = array("item_id" => $item['item_id']);
        if($website['content_id_context']=='website') $rowWhere['website_id'] = $website['id'];
        $row = $db->records($rowWhere)->fetch();
        
        
        if($row) {
            // update occurrence_last if exists
            $update = $row->update(array("occurrence_last" => $now));

            if($update) {
                $log .= '  * '.trim(str_replace(PHP_EOL, " ", $item['item_id'])).PHP_EOL;
            } else {
                $log .= 'ERROR:  * '.trim(str_replace(PHP_EOL, " ", $item['item_id'])).PHP_EOL;
            }
        } else {
            // add new
            $item['website_id'] = $website['id'];
            $item['occurrence_first'] = $now;
            // print_r($item);
            $insert = $db->records()->insert($item);

            if($insert) {
                $log .= '  + '.trim(str_replace(PHP_EOL, " ", $item['item_id'])).PHP_EOL;
            } else {
                $log .= 'ERROR:  + '.trim(str_replace(PHP_EOL, " ", $item['item_id'])).PHP_EOL;
            }
        }
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
    echo PHP_EOL.'No tracking scheduled'.PHP_EOL;
}

# E-mail
$messageBody = "";
foreach($db->records->where('message_sent=0') as $record) {

    // $messageBody.=PHP_EOL.PHP_EOL;
    $messageBody.='<div>';
    $messageBody.=$App->createMessage($record->website['message'], $record);
    $messageBody.='<small>source: <a href="'.$record->website['url'].'">'.$record->website['label'].'</a></small>';
    $messageBody.='<hr />';
    $messageBody.='</div>';
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
    if($App->sendHtmlEmail(EMAIL_ADDRESS, 'WebChangesTracker ('.date('Y-m-d H:i').')', $messageHtml, (EMAIL_SENDER!='' ? 'From: ' . EMAIL_SENDER : ''))) {
        echo PHP_EOL.'CONFIRMATION: Message sent';
        $messageLog->update(array('message_status' => 'ok'));
    } else {
        echo PHP_EOL.'ERROR: the message was not sent';
    }
}

# Maintenance
$db->records()->where("occurrence_last < '".date("Y-m-d H:i:s", strtotime("now - 300 day"))."'")->delete();
$db->tracking_log()->where("tracking_timestamp < '".date("Y-m-d H:i:s", strtotime("now - 30 day"))."'")->delete();
$db->message_log()->where("message_sent < '".date("Y-m-d H:i:s", strtotime("now - 30 day"))."'")->delete();