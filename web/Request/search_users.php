<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];


// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[KEYWORD])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $keyword = $_POST[KEYWORD];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);
    
    if($access) {
        
       	$cursor = null; 
       
        if(isset($_POST[CURSOR])){
            $cursor = $_POST[CURSOR];
        }

        $users = $db->searchUsers($keyword, $user_uid, $cursor, $pdo);
        
        if ($users != false) {

			$response[TAG_USER] = $users;
            $response[TAG_ERROR] = false;
            $response[TAG_MESSAGE] = "Success";
            echo json_encode($response);
                
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Search users failure!";
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
