<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) 
	&& isset($_POST[FLAG_UID]) && isset($_POST[FLAG_DESCRIPTION])
    && isset($_POST[FLAG_LATITUDE]) && isset($_POST[FLAG_LONGITUDE])
    && isset($_POST[FILE_TOTAL]) && isset($_POST[DELETE_FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $flag_uid = $_POST[FLAG_UID];
    $flag_description = $_POST[FLAG_DESCRIPTION];
    $flag_lat = $_POST[FLAG_LATITUDE];
    $flag_lng = $_POST[FLAG_LONGITUDE];
    $checkin_page_uid = $_POST[CHECKIN_PAGE_UID];
    $address = $_POST[FLAG_ADDRESS];
    
    $total_files = $_POST[FILE_TOTAL];
    $total_delete_files = $_POST[DELETE_FILE_TOTAL];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);

    if($access) {

            $total_result = true;
    
            for ($i = 0; $i < $total_files; $i++) {

                $current_filename = FILE_NAME . $i;
                $current_description = IMAGE_DESCRIPTION . $i;
                $current_type = IMAGE_TYPE . $i;
                $current_width = WIDTH . $i;
                $current_height = HEIGHT . $i;
                
                $filename = $_POST[$current_filename];
                $image_description = $_POST[$current_description];
                $type = $_POST[$current_type];
                $width = $_POST[$current_width];
                $height = $_POST[$current_height];
                
                $image_url = $filename;
                
                $total_result = $db->addImageToPost($flag_uid, $image_url, $image_description, $type, $width, $height, 'flag', $pdo);

            }

		    for ($i = 0; $i < $total_delete_files; $i++) {
	
				$current_image = IMAGE_UID . $i;
				$image_uid = $_POST[$current_image];
		                
				$db->removeImageFromPost($image_uid, $pdo);
		    }

            if ($total_result) {

                //all files uploaded successfully
                $flag = $db->editFlag($flag_uid, $user_uid, $flag_description, $flag_lat, $flag_lng, $address, $checkin_page_uid, $pdo);

                if ($flag != false) {
                    $response[TAG_ERROR] = false;
                    $response[TAG_FLAG] = $rp->getFlagFromQuery($flag, $db, $user_uid, $pdo);
                    echo json_encode($response);
                    
                } else {
                    $response[TAG_ERROR] = true;
                    $response[TAG_MESSAGE] = "Flag not updated ... Please try again!";
                    echo json_encode($response);
                }

            } else {
                //at least one file not uploaded successfully
                $response[TAG_ERROR] = true;
                $response[TAG_MESSAGE] = "Files not uploaded successfully ... Please try again!";
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

