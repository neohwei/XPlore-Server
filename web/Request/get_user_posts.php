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
        
        $cursor = null;

		if(isset($_POST[CURSOR])){
			$cursor = $_POST[CURSOR];
		}

        $posts;
        if(isset($_POST[KEYWORD])){
        	$keyword = $_POST[KEYWORD];
        	$posts = $db->searchUserPosts($user_uid, $search_user_uid, $keyword, $cursor, $pdo);
        }
        else{
        	$posts = $db->getUserPosts($user_uid, $search_user_uid, $cursor, $pdo);
        } 

        if ($posts != false) {

				$response[TAG_POST] = $posts;
                $response[TAG_ERROR] = false;
                $response[TAG_MESSAGE] = "Success";
                echo json_encode($response);
           
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Get posts failure!";
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
