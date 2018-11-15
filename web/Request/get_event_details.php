<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[EVENT_UID])) {
    
    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $event_uid = $_POST[EVENT_UID];
    
    $pdo = $db->getPDO();
    
    $access = $db->accessCheck($user_uid, $user_token, $pdo);
    
    if($access) {
        
        $event = $db->getEventDetails($event_uid, $user_uid, $pdo);
        
        if ($event != false) {
            
            $response[TAG_EVENT] = $rp->getEventFromQuery($event, $db, $user_uid, $pdo);
            $response[TAG_ERROR] = FALSE;
            $response[TAG_MESSAGE] = "Event found!";
            echo json_encode($response);
            
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Get details failure!";
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
