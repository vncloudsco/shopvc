<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Template Name: Send Notifications
*/
require_once ('GCM.php');
 

 //sendPushNotification(array("post_id" => "5716", "notification_type" => "stack"), "YOUR_GCM_API_KEY");
 function sendPushNotification($message, $google_api_key, $isBulk){
	if(empty($google_api_key)){
		echo "Key Empty";
		return;
	}

	$success = 0;
	$notRegistered = 0;
	$bulkSent = 0;
        $iosbulkSent = 0;
        $iossent = 0;
        $ioseligible = 0;
        $iosNotRegistered = 0;
	$mismatchsenderid = 0;
	$otherError = 0;
	$remove_ids = array();
	
	print_r($message);
	
	global $wpdb;
	$gcm = new GCM();
        $table_name = $wpdb->prefix . 'pw_gcmusers';
	$stats_table_name = $wpdb->prefix . 'androapp_stats';
	
	//$message = array("post_id" => "5716", "link" => "http://puzzlersworld.com/math-puzzles/four-digit-number-aabb/", "title"=>"Two Eggs Puzzle", "excerpt"=>"there are two eggs, 100 floor building, you have to find the max building number from which the egg will not brake in minimum number of attempts");
	
	$eligible = 0;

	$title = $message['title'];
	if(empty($title)){
		$title = "post id ".$message['post_id'];
	}
	$id = updateStats($title, $eligible, $bulkSent, $success, $notRegistered, 
                $mismatchsenderid, $otherError, 'START','', $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
	
	if($isBulk){
            echo "\nSending notifications to android devices\n";
            $sql = "SELECT COUNT(*) FROM $table_name where topics='all' and status = 1" ;
            echo "bulk fetch query = $sql";
            $eligible += $wpdb->get_var($sql);
            echo "Eligible = ".$eligible;
            updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                    $otherError, 'BULK_ANDROID_STARTS',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
            $gcmResult = $gcm->send_notification_topic("all", $message, $google_api_key);
            echo ($gcmResult);

            if(isBulkSentSuccessful(json_decode($gcmResult))){
                $bulkSent = $eligible;			
            }

            updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                    $otherError, 'BULK_ANDROID_END',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
            echo "Sent notifications to android devices\n";
            
            
            echo "\nSending notifications to IOS devices\n";
            $sql = "SELECT COUNT(*) FROM $table_name where topics='allIOS' and status = 1" ;
            echo "bulk fetch query = $sql";
            $ioseligible += $wpdb->get_var($sql);
            updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                    $otherError, 'BULK_IOS_START',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
            $gcmResult = $gcm->send_notification_ios_topic("allIOS", $message, $google_api_key, null);
            echo ($gcmResult);

            if(isBulkSentSuccessful(json_decode($gcmResult))){
                $iosbulkSent += $ioseligible;			
            }
            updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                    $otherError, 'BULK_IOS_END',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
            
            echo "Sent notifications to IOS devices\n";
	}
        
        androapp_send_single_notification($message, $google_api_key, $success, 
        $notRegistered, $mismatchsenderid, $otherError, $iossent, $iosNotRegistered, $iosbulkSent, $ioseligible,
         $bulkSent, $eligible, $title, $id, $isBulk, 'android' );
	
        updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                $otherError, 'ANDROID_SINGLE_FINISH',$id, $iossent,  $iosbulkSent, $ioseligible, $iosNotRegistered);
        
        androapp_send_single_notification($message, $google_api_key, $success, 
        $iosNotRegistered, $mismatchsenderid, $otherError, $iossent,  $iosNotRegistered, $iosbulkSent, $ioseligible,
         $bulkSent, $eligible, $title, $id, $isBulk, 'IOS' );
	updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                $otherError, 'FINISH',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
 }
 
 function androapp_send_single_notification($message, $google_api_key, &$success, 
        &$notRegistered, &$mismatchsenderid, &$otherError, &$iossent,  &$iosNotRegistered, $iosbulkSent, &$ioseligible,
         $bulkSent, &$eligible, $title, &$id, $isBulk, $deviceType ){
        global $wpdb;
        $remove_ids = array();
        $gcm = new GCM();
     	$table_name = $wpdb->prefix . 'pw_gcmusers';
     	$outArraySize = 0;
        $offset = 0;
	$batch_size = 5000;
	while(true){
            $registration_ids  = array();
            $primary_ids = array();
            $devices = array();
            $sql = "select id, gcm_regid, device from $table_name where status = 1 ";
            if($deviceType == 'IOS'){
                $sql .= " and device = '$deviceType'";
            }else{
                $sql .= " and device != 'IOS'";
            }
            if($isBulk){
                    //IF not bulk than send one by one to all
		    $sql .= " and (topics is null or topics = '')";
            }
            $sql .= " limit $offset, $batch_size;";
            echo "</br>fetch query $sql";
            $results = $wpdb->get_results($sql);
            if(empty($results)){
                    break;
            }
            foreach($results as $entry){
                    $registration_ids[] = $entry->gcm_regid;
                    $primary_ids[]  = $entry->id;
                    $devices[] = $entry->device;
                    if($deviceType == 'IOS'){
                        $ioseligible++;    
                    }else{
                        $eligible++;    
                    }
                    
            }

            unset($results);
            $results = null;
            $outArray = array_chunk($registration_ids, 90);
            unset($registration_ids);
            $registration_ids = null;
            $outArraySize = count($outArray);
            $primaryIdsArr = array_chunk($primary_ids, 90);
            unset($primary_ids);
            $primary_ids = null;
            $deviceArr = array_chunk($devices, 90);
            
            $i=0;
            foreach($outArray as $regIds){
                //echo "Sending ".count($regIds) ." notifications";
                if($deviceType == 'IOS'){
                    $gcmResult = $gcm->send_notification_ios($regIds, $message, $google_api_key);
                    
                    echo  "ios results = ".$gcmResult;
                    
                    updateCounts(json_decode($gcmResult), $iossent, $iosNotRegistered, $mismatchsenderid, 
                        $otherError, $remove_ids, $primaryIdsArr[$i] );
                    
                }else{
                    $gcmResult = $gcm->send_notification($regIds, $message, $google_api_key);
                    
                    echo  ($gcmResult);
                    
                    updateCounts(json_decode($gcmResult), $success, $notRegistered, $mismatchsenderid, 
                        $otherError, $remove_ids, $primaryIdsArr[$i] );  
                }
                
                $outArray[$i] = null;
                $primaryIdsArr[$i] = null;
                $i++;
            }

            updateStats($title, $eligible, $bulkSent, $success, $notRegistered, $mismatchsenderid, 
                    $otherError, 'WIP',$id, $iossent, $iosbulkSent, $ioseligible, $iosNotRegistered);
            //echo "</br>";
            //Deleting entries only if there are substantial entries in the table

            $offset += $batch_size;
	}
	if($outArraySize > 1)
	{
            removeInvalidRegistrationIds($remove_ids);
	}
 }
 
 function isBulkSentSuccessful($jsonArray){
     	if($jsonArray->message_id)
 	    return true;
        return false;
 }
 
 function updateCounts($jsonArray, &$success, &$notRegistered, &$mismatchsenderid, &$otherError, &$remove_ids, $primary_ids ){
    if(!empty($jsonArray->results)){
        for($i=0; $i<count($jsonArray->results);$i++){
            if(isset($jsonArray->results[$i]->error)){
                if($jsonArray->results[$i]->error == "NotRegistered" || $jsonArray->results[$i]->error == "InvalidRegistration"){
                    $notRegistered++;
                    $remove_ids[] = $primary_ids[$i];
                    //echo "remove ";
                    //print_r($remove_ids);
                }else if($jsonArray->results[$i]->error == "MismatchSenderId"){
                    $mismatchsenderid++;
                }else if($jsonArray->results[$i]->error == "InvalidRegistration"){

                }else{
                    $otherError++;
                }
            }else{
                $success++;
            }
        }
    }
 }
 
 function updateStats($title, $eligible, $bulkSent, $success, $notRegistered, 
        $mismatchsenderid, $otherError, $status, $id, $iossent, $iosbulksent, $ioseligible, $iosNotRegistered){
	global $wpdb;
        $title = addslashes($title);
	$table_name = $wpdb->prefix . 'androapp_stats';
	if(empty($id)){
		$query = "insert into $table_name (`title`,`eligible`, `bulk_sent`,"
                        . " `success`,`notRegistered`,`mismatchsenderid`,`other`"
                        . ",`status`, `ios_sent`, `ios_bulk_sent`, `ios_eligible` , `ios_notRegistered`) VALUES('$title',$eligible,$bulkSent,"
                        . " $success,$notRegistered,$mismatchsenderid,$otherError,'$status' , $iossent, $iosbulksent, $ioseligible, $iosNotRegistered)";
		echo $query;
		$res = $wpdb->query(
			$query
		);
		return $wpdb->insert_id;
	}else{
		$query = "update $table_name set title = '$title', eligible = $eligible, bulk_sent=$bulkSent, "
                        . "success = $success, notRegistered = $notRegistered,mismatchsenderid = $mismatchsenderid, "
                        . "other = $otherError, status = '$status', ios_sent = $iossent, ios_bulk_sent = $iosbulksent, "
                        . "ios_eligible = $ioseligible, ios_notRegistered =$iosNotRegistered where id = $id";
		echo $query;
		$wpdb->query(
			$query
		);
	}
	
	echo $res;
 }
 
 function removeInvalidRegistrationIds($remove_ids){
	global $wpdb;
	$table_name = $wpdb->prefix . 'pw_gcmusers';
	
	if(count($remove_ids) > 0){
		//print_r($remove_ids);
		$remove_ids = implode(',', $remove_ids);
		//$deleteQuery = "DELETE FROM $table_name WHERE id in ({$remove_ids});";
		$updateQuery = "update $table_name set status = 0 where id in ({$remove_ids});";
		echo "update query ".$updateQuery;
		$wpdb->query(
			$updateQuery
		);
	}
 }

?>
