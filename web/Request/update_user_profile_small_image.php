<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $token = $_POST[USER_TOKEN];

    $access = $db->accessCheck($user_uid, $token);
    if($access) {

    			$current_file = FILE_TAG_PROFILE;
    			$current_filename = FILE_NAME_PROFILE;
    
    			$file = $_FILES[$current_file];
    			$filename = $_POST[$current_filename];
    
    			$profile_image_url = $storage->storeFile($file['tmp_name'], $file['type']);
    	
    			$update_result = $db->updateUserProfileImage($user_uid, $profile_image_url);
    			
    			if ($update_result) {
    				$response[TAG_USER] = $rp->getUserFromQuery($update_result, $db, $user_uid);
    		   		$response[TAG_ERROR] = false;
    		    	$response[TAG_MESSAGE] = "Update profile image success!";
    		    	echo json_encode($response);
    		} else {
    		    	$response[TAG_ERROR] = TRUE;
    		    	$response[TAG_MESSAGE] = "Update profile image failure!";
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

