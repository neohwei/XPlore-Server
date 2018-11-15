<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[EVENT_UID]) && isset($_POST[ATTENDER_STATUS])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $event_uid = $_POST[EVENT_UID];
    $attender_status = $_POST[ATTENDER_STATUS];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);
    
    if($access) {

		$cursor = null;

		if(isset($_POST[CURSOR])){
			$cursor = $_POST[CURSOR];
		}
		
		$attenders;

 		if(isset($_POST[KEYWORD])){
        	 $keyword = $_POST[KEYWORD];
        	 $attenders = $db->searchSpecificEventAttenders($event_uid, $attender_status, $user_uid, $keyword, $cursor, $pdo);
        }
        else{
        	 $attenders = $db->getSpecificEventAttenders($event_uid, $attender_status, $user_uid, $cursor, $pdo);
        }

        if ($attenders != false) {
           
				$response[TAG_EVENT_ATTENDER] = $attenders;
                $response[TAG_ERROR] = false;
                $response[TAG_MESSAGE] = "Success";
                echo json_encode($response);

        } else {
	            $response[TAG_ERROR] = TRUE;
	            $response[TAG_MESSAGE] = "Get event attenders failure!";
	            echo json_encode($response);
        }
    }else{
        $response[TAG_ERROR] = TRUE;
        $response[TAG_MESSAGE] = "Access denied!";
        echo json_encode($response);
    }

} else {
    $response[TAG_ERROR] = TRUE;
    $response[TAG_MESSAGE] = "Required parameters missing!";
    echo json_encode($response);
}
?>
