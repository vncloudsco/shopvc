<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class GCM {
 
    //put your code here
    // constructor
    function __construct() {
         
    }
     /**
     * Sending Push Notification
     */
    public function send_notification_ios_topic($topic_name, $message, $google_api_key, $registatoin_ids) {
        //Creating the notification array.
        $google_api_key = trim($google_api_key);
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        
        $body = $message['excerpt'];
        $body = strip_tags($body);
	$body = html_entity_decode($body);
       
	$title = $message['title'];
	$title = strip_tags($title);
	$title = html_entity_decode($title);
 
        $notification = array( 
            'title' => $title , 'body' => $body,
        	'mutable_content' => true,
	'attachment-url' => $message['postImage'] 
	);
	$indata = array('post_id' => $message['post_id'], 'postType' => $message['postType'], 'link' => $message['link'],
            'notification_type' => $message['notification_type'],
            'cache' => $message['cache'],
		'attachment-url' => $message['postImage']);

	echo "Indata = ".json_encode($indata, JSON_UNESCAPED_SLASHES);
	$data = array('data' => $indata);

        if(empty($topic_name)){
            //This array contains, the token and the notification. The 'to' attribute stores the token.
            $arrayToSend = array('registration_ids' => $registatoin_ids, 'data' => $data,
                'notification' => $notification,'priority'=>'high');
        }else{
            //This array contains, the token and the notification. The 'to' attribute stores the token.
            $arrayToSend = array('to' => "/topics/".$topic_name, 'data' => $data, 
                'notification' => $notification, 'priority'=>'high');    
        }
        
        
        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend, JSON_UNESCAPED_SLASHES);

	echo "JSon = ".$json;
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        $headers[] = "Authorization: key=".$google_api_key;

        //Setup curl, add headers and post parameters.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);       

        //To return the output as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //Send the request
        $response = curl_exec($ch);

        //Close request
        curl_close($ch);
        return $response;
    }
     /**
     * Sending Push Notification
     */
    public function send_notification_topic($topic_name, $message, $google_api_key) {
        $google_api_key = trim($google_api_key);
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $fields = array(
            'to' => "/topics/".$topic_name,
            'data' => $message,
        );
 
        $headers = array(
            'Authorization: key=' . $google_api_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        return $result;
    }

    /*
     * Sending Push Notification for IOS
     */
    public function send_notification_ios($registatoin_ids, $message, $google_api_key) {
        return $this->send_notification_ios_topic("", $message, $google_api_key, $registatoin_ids);
    }
    /**
     * Sending Push Notification
     */
    public function send_notification($registatoin_ids, $message, $google_api_key) {
        $google_api_key = trim($google_api_key);
        // Set POST variables
        //$url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
 
        $headers = array(
            'Authorization: key=' . $google_api_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        return $result;
    }
 
}
 
?>
