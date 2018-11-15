<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[PAGE_UID])) {
    
    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $page_uid = $_POST[PAGE_UID];
    
    $pdo = $db->getPDO();
    
    $access = $db->accessCheck($user_uid, $user_token, $pdo);
    
    if($access) {
      
        $posts = $db->getPageRecentCheckinPosts($user_uid, $page_uid, null, $pdo);
        $events = $db->getPageRecentEvents($user_uid, $page_uid, null, $pdo);
        
        if ($posts != false && $events != false) {
          
                $response[TAG_POST] = $posts;
                $response[TAG_EVENT] = $events;
                $response[TAG_ERROR] = false;
                $response[TAG_MESSAGE] = "Success";
                echo json_encode($response);
           
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Get recent checkin posts failure!";
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
