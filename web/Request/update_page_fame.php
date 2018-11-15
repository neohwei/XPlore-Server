<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[PAGE_UID]) && isset($_POST[PAGE_FAME_STATUS])) {
    
    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $page_uid = $_POST[PAGE_UID];
    $fame_status = $_POST[PAGE_FAME_STATUS];
    
    $access = $db->accessCheck($user_uid, $user_token);
    
    if($access) {
        
        $allow = $db->allowUserUpdatePageFame($user_uid);
        
        if($allow){
        	
        	$result = $db->updatePageFame($user_uid, $page_uid, $fame_status);
        	
        	if($result){
        		$response[TAG_ERROR] = false;
	            $response[TAG_MESSAGE] = "Fame update success! 1 day until your next fame update";
	            echo json_encode($response);
        	}
        	else{
        		$response[TAG_ERROR] = true;
	            $response[TAG_MESSAGE] = "Fame update failed! Please try again ... ";
	            echo json_encode($response);
        	}
        	
        }
        else{
        	$response[TAG_ERROR] = true;
            $response[TAG_MESSAGE] = "You have used your fame update today";
            echo json_encode($response);
        }
        
    }else{
        //access denied
        $response[TAG_ERROR] = true;
        $response[TAG_MESSAGE] = "Access denied!";
        echo json_encode($response);
    }
    
} else {
    $response[TAG_ERROR] = TRUE;
    $response[TAG_MESSAGE] = "Required parameters missing!";
    echo json_encode($response);
}
?>

