<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[FLAG_DESCRIPTION])
    && isset($_POST[FLAG_LATITUDE]) && isset($_POST[FLAG_LONGITUDE])
  	&& isset($_POST[FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $description = $_POST[FLAG_DESCRIPTION];
    $lat = $_POST[FLAG_LATITUDE];
    $lng = $_POST[FLAG_LONGITUDE];
    $address = $_POST[FLAG_ADDRESS];

    $total_files = $_POST[FILE_TOTAL];
    
    $checkin_page_uid = "NULL";
    if(isset($_POST[CHECKIN_PAGE_UID])){
    	$checkin_page_uid = $_POST[CHECKIN_PAGE_UID];
    }
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);

    if($access) {

        if ($total_files >= 0) {
            //file(s) attached, process file
            
            $flag = $db->newFlag($user_uid, $description, $lat, $lng, $address, $checkin_page_uid, $pdo);

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
                
                $total_result = $db->addImageToPost($flag['flag_uid'], $image_url, $image_description, $type, $width, $height, 'flag', $pdo);

            }

            if ($total_result) {

                    $response[TAG_ERROR] = false;
                    $response[TAG_FLAG] = $flag;
                    echo json_encode($response);
             
            } else {
	                //at least one file not uploaded successfully
	                
	                $db->deleteFlag($flag['flag_uid'], $pdo);
	                
	                $response[TAG_ERROR] = true;
	                $response[TAG_MESSAGE] = "Files not uploaded successfully ... Please try again!";
	                echo json_encode($response);
            }
        } else {
            //no image attached
            $response[TAG_ERROR] = true;
            $response[TAG_MESSAGE] = "No image attached!";
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

