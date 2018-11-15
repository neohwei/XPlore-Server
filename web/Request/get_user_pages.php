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

		$cursor_checkin = null;
		$cursor_date = null;
	
		if(isset($_POST[CURSOR_CHECKIN]) && isset($_POST[CURSOR_DATE])){
			$cursor_checkin = $_POST[CURSOR_CHECKIN];
			$cursor_date = $_POST[CURSOR_DATE];
		}
		
		$pages;
		
        if(isset($_POST[KEYWORD]) || isset($_POST[PAGE_CATEGORY])){
        	$keyword = "";
			$category = "";
			if(isset($_POST[KEYWORD])){
				$keyword = $_POST[KEYWORD];
			}
			if(isset($_POST[PAGE_CATEGORY])){
				$category = $_POST[PAGE_CATEGORY];
			}
        	 $pages = $db->searchUserPages($user_uid, $search_user_uid, $keyword, $category, $cursor_checkin, $cursor_date, $pdo);
        }
        else{
        	 $pages = $db->getUserPages($user_uid, $search_user_uid, $cursor_checkin, $cursor_date, $pdo);
        } 

        if ($pages != false) {
           	
           		$response[TAG_PAGE] = $pages;
                $response[TAG_ERROR] = false;
                $response[TAG_MESSAGE] = "Success";
                echo json_encode($response);
           
        } else {
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Get pages failure!";
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
