<?php
include_once dirname(__FILE__) . "/app.debug.php";
include_once dirname(__FILE__) . "/app.config.php";
include_once dirname(__FILE__) . "/app.connect.php";

class App
{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    function getUrlContent($url) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            // CURLOPT_USERAGENT      => "test", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
        ); 
    
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
    
        $content  = curl_exec($ch);
    
        curl_close($ch);
    
        return $content;
    }

    function parseUrl($websiteId) {

        $data = array();

        $website = $this->db->website[$websiteId];

        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML($this->getUrlContent($website['url']));

        $xpath = new DOMXpath($doc);

        $wrapper = $xpath->query($website['content_wrapper']);

        if(!is_null($wrapper)) {
            $idx=0;
            foreach ($wrapper as $item) {

                $data[$idx]['item_id'] = $xpath->query('.' . $website['content_id'], $item)->item(0)->nodeValue;

                for($i=1; $i<6; $i++) {

                    if($website['content_item_'.$i]!="") {

                        if(isset($xpath->query('.' . $website['content_item_'.$i], $item)->item(0)->nodeValue)) {
                            $data[$idx]['item_'.$i] = $xpath->query('.' . $website['content_item_'.$i], $item)->item(0)->nodeValue;
                        } else {
                            $data[$idx]['item_'.$i] = null;
                        }

                    } else {
                        $data[$idx]['item_'.$i] = null;
                    }

                }

                $idx++;
            }
        }

        return $data;

    }

    function getMessageTemplate($websiteId) {
        $website = $this->db->website[$websiteId];
        return $website['message'];
    }

    function createMessage($message, $data) {

        $message = str_replace('{id}', $data['item_id'], $message);
        $message = str_replace('{1}', $data['item_1'], $message);
        $message = str_replace('{2}', $data['item_2'], $message);
        $message = str_replace('{3}', $data['item_3'], $message);
        $message = str_replace('{4}', $data['item_4'], $message);
        $message = str_replace('{5}', $data['item_5'], $message);

        return $message;
    }

}

$App = new App($db);