<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
	$accountOptions = get_option($this->account_tab_key);
	if(isset($_POST['selfpushoptions']) && !empty($_POST['selfpushoptions'])){
		
            if(! wp_verify_nonce( $_POST['_wpnonce'], 'selfpushoptions' ))
            {
                print 'Sorry, your nonce did not verify. Please try again.';
                exit;
            }
                    
		$postid = intval($_POST['selfpushoptions']);
		$disableBulkSend = $accountOptions[pw_mobile_app_settings::$disableBulkSend];	
                $disableNotificationCache = $accountOptions[pw_mobile_app_settings::$disableNotificationCache];
                $postType = "post";
                $postdata = get_post( $postid); 
                $postType = $postdata->post_type;
                if(class_exists('WP_JSON_Posts')){
                    $wp_server_posts = new WP_JSON_Posts(new WP_JSON_Server() );
                    $response =  $wp_server_posts->get_post($postid, 'view');
                    if(isset($response) && isset($response->data)){
                            $wppost = $response->data;
                    }
                    $title = $wppost['title'];
                    $excerpt = $wppost['pwapp_excerpt'];
                }else if(class_exists('WP_REST_Posts_Controller')){
                    //TODO: handle page
                    $wp_server_posts = new WP_REST_Posts_Controller($postdata->post_type);
                    $request = array();
                    $request['id'] = $postid;
                    $response =  $wp_server_posts->get_item($request);   
                    //print_r($response);
                    if(isset($response) && isset($response->data)){
                        $wppost = $response->data;
                    }
                    $title = $wppost['title']['rendered'];
                    $excerpt = $wppost['excerpt']['rendered'];
                }	

                $postimage = $wppost['featuredimage'];
                if(empty($postimage)){
                    $postimage = $wppost['pwapp_post_image'];
                }
                $postlink = $wppost['link'];
                $cache = "yes";
                
                if(isset($disableNotificationCache) && $disableNotificationCache == 'yes'){
                    $cache = "no";
                }
                if(!empty($title)){
                    if(isset($_POST['immediate']) && $_POST['immediate'] == '1'){
                            _e('Push notification Logs','androapp');
                            echo "<div max-height='200px;'><pre>";

                            require_once PW_MOBILE_PATH.'gcm/send_message.php';

                            sendPushNotification(array("post_id" => $postid, 
                                "title" => $title, "excerpt" =>$excerpt,
                                "postImage" => $postimage, "link" => $postlink, "cache" => $cache,
                                "postType" => $postType,
                                "notification_type" => "stack"), 
                            $accountOptions[ANDROAPP_GCM_API_KEY], !($disableBulkSend == '1'));
                            echo "</pre></div>";
                    }else{
                            $this->schedule_push_notification($postid, $title, 
                                    $excerpt, $postimage, $postlink, $cache, $postType,
                                    $accountOptions[ANDROAPP_GCM_API_KEY],
                             !($disableBulkSend == 1));
                            $this->show_success_message("Scheduled Push Notification");
                    } 
                }else{
                    echo "<h2 style='color:red'>Invalid Post Id</h1>";
                }
		
		
	}
?>
<?php _e('Please ensure that you fill google api key and sender id in account settings tab and create a new apk, install that apk in your mobile.',
'androapp');?>
<h2><?php _e('SelfPush','androapp');?></h2>
<div>
<?php _e('trigger push notification for a post','androapp');?></br>
<form name="selfpushoptions" action="#selfpushoptions" method="post">
<b><?php _e('Enter Post Id','androapp');?>: </b><input type="text" name="selfpushoptions" value="" placeholder="Enter Post Id"/>
(<?php _e('please enter correct post id','androapp');?>)</br></br>
<b><?php _e('Send Immediate','androapp');?>: </b><input type="checkbox" name="immediate" value="1"/> </br>
<?php _e('By default notification is scheduled to go after 10 minutes, check this box to send push notification immediately, this might take some time depending on number of users and will show logs on this screen, your server might kill this request in middle depending on your server configuration, so we recommend not to select this option in general.',
'androapp');?></br></br>
<input class="button-primary" type="submit" id="selfpushoptions" value="Send Notification" onclick="return confirm(
'<?php _e('Click Yes to Confirm.','androapp')?>');" />
<?php
wp_nonce_field('selfpushoptions');
?>
</form>
</div>
<h2>Push Notifications in Queue</h2>
<?php 
    
    $crons = get_option('cron');
    
    //print_r($crons);
    echo '<table border="1px;" style="max-width:800px">';
    echo "<tr><th>Post Id</th><th>Title</th><th>Excerpt</th><th>Image Link</th>"
    . "<th>Post Link</th><th>Cached on Device</th><th>Bulk Send</th> </tr>";
    $pushPostCount = 0;
    foreach($crons as $key => $value){
        
        if(is_array($value)){
            //print_r($value);
            foreach($value as $k => $v){
                if($k == 'send_push_notification_after_publish'){
                    $pushPostCount++;
                    //print_r($vv);
                    echo "<tr>";
                    foreach ($v as $kk => $vv){
                        foreach ($vv['args'] as $ka => $va){
                            if($ka > 5 && $ka < 9){
                                continue;
                            }
                            echo "<td>$va</td>";
                        }
                    }
                    echo "</tr>";
                }
                
            }
        }
    }
    echo "</table>";
    echo "<h3>You have $pushPostCount posts in queue</h3>";
    if(defined('DISABLE_WP_CRON') &&  DISABLE_WP_CRON){
        echo "<span style='color:red'>Your cron is disabled, push notification will be attempted when you hit wp-cron.php manually</span>";
    }
    
?>
<h2><?php _e('Push notification Statistics','androapp');?></h2>
<?php

global $wpdb;

$stats_table = $wpdb->prefix . 'androapp_stats';
$time = "7days";
if(isset($_GET['time'])){
	$time = sanitize_text_field($_GET['time']);
}
$date = date('Y-m-d', strtotime('-6 days'));
$date .= " 00:00:00";
$todate = null;
$last7days = "<span class=\"active\"><a href=\"?page=pw_mobile_app_options&tab=androapp_tools_tab\">".
__('Last 7 Days','androapp')."</a></span>";
$thismonth = "<span class=\"active\"><a href=\"?page=pw_mobile_app_options&tab=androapp_tools_tab&time=currentmonth\">".
__('This Month','androapp')."</a></span>";
$lastmonth = "<span class=\"active\"><a href=\"?page=pw_mobile_app_options&tab=androapp_tools_tab&time=lastmonth\">".
__('Last Month','androapp')."</a></span>";
$alltime = "<span class=\"active\"><a href=\"?page=pw_mobile_app_options&tab=androapp_tools_tab&time=alltime\">".
__('All Time','androapp')."</a></span>";
if(empty($time) || $time == "7days"){
	$last7days  = "<span class=\"passive\">".__('Last 7 Days','androapp')."</span>";
}
if($time == "currentmonth"){
	$m = date('m');
	$y = date('Y');
	$date  = $y."-".$m."-01 00:00:00";
	$thismonth  = "<span class=\"passive\">".__('This Month','androapp')."</span>";
}else if($time == 'lastmonth'){
	$m = date('m');
	$y = date('Y');
	$todate  = $y."-".$m."-01 00:00:00";
	$m = intval($m) - 1;
	if($m == 0){
		$m = 12;
	}
	if($m < 10){
		$m = "0".strval($m);
	}else{
		$m = strval($m);
	}
	$date  = $y."-".$m."-01 00:00:00";
	$lastmonth = "<span class=\"passive\">".__('Last Month','androapp')."</span>";
}else if($time == 'alltime'){
	$date = null;
	$alltime = "<span class=\"passive\">".__('All Time','androapp')."</span>";
}
$query = "SELECT * FROM $stats_table";
if(!empty($date)){
	$query .=  " where created_at > '$date'";
}
if(!empty($todate)){
	$query.= " and created_at < '$todate'";
}
//echo "Query ".$query;
$result = $wpdb->get_results($query); 

//echo "<pre>"; print_r($result); echo "</pre>";

echo $last7days . $thismonth . $lastmonth.$alltime;
if(empty($result)){
 echo "</br></br><b>".__('No Records found for the duration','androapp')." </b>";
}else{
?>
<table border="1px;" style="max-width:800px">
<tr><th><?php _e('Notification Title','androapp');?></th>
<th><?php _e('Trigger Time','androapp');?></th>
<th><?php _e('Attempted','androapp');?></th>
<th>*<?php _e('Bulk Sent','androapp');?></th>
<th><?php _e('Sent','androapp');?></th>
<th><?php _e('Uninstalled','androapp');?></th>
<th><?php _e('IOS Attempted','androapp');?></th>
<th>*<?php _e('IOS Bulk Sent','androapp');?></th>
<th><?php _e('IOS Sent','androapp');?></th>
<th><?php _e('IOS Uninstalled','androapp');?></th>
<th><?php _e('SenderId MisMatch','androapp');?></th>
<th><?php _e('Other Errors','androapp');?></th>
<th><?php _e('Status','androapp');?></th></tr>
<?php
foreach($result as $row)
 {

 echo "<tr><td style='width:35%'>$row->title</td><td>$row->created_at</td><td>$row->eligible</td>";
 echo "<td>$row->bulk_sent</td><td>$row->success</td><td>$row->notRegistered</td>"
         . "<td>$row->ios_eligible</td><td>$row->ios_bulk_sent</td><td>$row->ios_sent</td><td>$row->ios_notRegistered</td>"
         . "<td>$row->mismatchsenderid</td><td>$row->other</td><td>$row->status</td>";

 }
?>
</table>
*Bulk Sent might not reflect the exact send count as it does not take care of un-installs and other errors.
<?php }?>