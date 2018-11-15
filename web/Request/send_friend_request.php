<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[SEARCH_USER_UID])) {
    
    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $search_user_uid = $_POST[SEARCH_USER_UID];
    
    $pdo = $db->getPDO();
    
    $access = $db->accessCheck($user_uid, $user_token, $pdo);
    
    if($access) {
        
        $result = $db->sendFriendRequest($user_uid, $search_user_uid, $pdo);
        
        if ($result) {
            $response[TAG_ERROR] = false;
            $response[TAG_MESSAGE] = "Request sent!";
            echo json_encode($response);
        } else {
            $response[TAG_ERROR] = true;
            $response[TAG_MESSAGE] = "Request not sent ... Please try again!";
            echo json_encode($response);
        }
        
    }else{
        //access denied
        //no image attached
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

