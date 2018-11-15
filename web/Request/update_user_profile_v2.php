<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[USER_NAME]) && isset($_POST[USER_EMAIL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $token = $_POST[USER_TOKEN];
    $name = $_POST[USER_NAME];
    $email = $_POST[USER_EMAIL];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $token, $pdo);
    if($access) {
    	
    	$user = $db->getUserProfile($user_uid, $user_uid, $pdo);
    
    	if ($db->isExistingEmail($email, $pdo) && $user[USER_EMAIL] != $email) {
    		// user already existed
    		$response[TAG_ERROR] = TRUE;
    		$response[TAG_MESSAGE] = "User already existed with $email";
    		echo json_encode($response);
    	}else{
    		
    		$update_result;
	
    		if(isset($_POST[FILE_NAME_PROFILE])){
    		
    			$current_filename = FILE_NAME_PROFILE;
    
    			$filename = $_POST[$current_filename];
    
    			$profile_image_url = $filename;
    	
    			$update_result = $db->updateUserProfile($user_uid, $name, $email, $profile_image_url, $pdo);
    			
    		}else{
    			
    			$update_result = $db->updateUserProfile($user_uid, $name, $email, null, $pdo);
    		}

    		if ($update_result) {
    			
    				$response[TAG_USER] = $rp->getUserFromQuery($update_result, $db, $user_uid, $pdo);
    				$response[TAG_USER][USER_TOKEN] = $update_result[USER_TOKEN];
    		   		$response[TAG_ERROR] = FALSE;
    		    	$response[TAG_MESSAGE] = "Update profile success!";
    		    	echo json_encode($response);
    		} else {
    		    	$response[TAG_ERROR] = TRUE;
    		    	$response[TAG_MESSAGE] = "Update profile failure!";
    		    	echo json_encode($response);
    		}
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

