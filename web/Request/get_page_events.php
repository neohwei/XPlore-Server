<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];

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

		$cursor = null;

		if(isset($_POST[CURSOR])){
			$cursor = $_POST[CURSOR];
		}

        $events;
        if(isset($_POST[KEYWORD])){
        	$keyword = $_POST[KEYWORD];
        	$events = $db->searchPageEvents($user_uid, $page_uid, $keyword, $cursor, $pdo);
        }
        else{
        	$events = $db->getPageEvents($user_uid, $page_uid, $cursor, $pdo);
        } 

        if ($events != false) {
           	
           		$response[TAG_EVENT] = $events;
                $response[TAG_ERROR] = false;
                $response[TAG_MESSAGE] = "Success";
                echo json_encode($response);
           
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Get events failure!";
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
