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
	&& isset($_POST[EVENT_UID]) && isset($_POST[EVENT_TITLE])
    && isset($_POST[EVENT_DESCRIPTION])
    && isset($_POST[EVENT_LATITUDE]) && isset($_POST[EVENT_LONGITUDE])
    && isset($_POST[EVENT_ADDRESS]) 
    && isset($_POST[EVENT_START_TIME]) && isset($_POST[EVENT_END_TIME])
    && isset($_POST[EVENT_START_DATE]) && isset($_POST[EVENT_END_DATE])
    && isset($_POST[FILE_TOTAL]) && isset($_POST[DELETE_FILE_TOTAL])
    && isset($_POST[CHECKIN_PAGE_UID])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $event_uid = $_POST[EVENT_UID];
    $event_title = $_POST[EVENT_TITLE];
    $event_description = $_POST[EVENT_DESCRIPTION];
    $event_lat = $_POST[EVENT_LATITUDE];
    $event_long = $_POST[EVENT_LONGITUDE];
    $event_address = $_POST[EVENT_ADDRESS];
    $start_time = $_POST[EVENT_START_TIME];
    $end_time = $_POST[EVENT_END_TIME];
    $start_date = $_POST[EVENT_START_DATE];
    $end_date = $_POST[EVENT_END_DATE];
    $checkin_page_uid = $_POST[CHECKIN_PAGE_UID];
    
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
                
                $total_result = $db->addImageToPost($event_uid, $image_url, $image_description, $type, $width, $height, 'event', $pdo);

            }

		    for ($i = 0; $i < $total_delete_files; $i++) {
	
				$current_image = IMAGE_UID . $i;
				$image_uid = $_POST[$current_image];
		                
				$db->removeImageFromPost($image_uid, $pdo);
		    }

            if ($total_result) {

                //all files uploaded successfully
                $event = $db->editEvent($event_uid, $user_uid, $event_title, $event_description, $event_lat, $event_long, $event_address, $start_time, $end_time, $start_date, $end_date, $checkin_page_uid, $pdo);

                if ($event != false) {
                    $response[TAG_ERROR] = false;
                    $response[TAG_EVENT] = $rp->getEventFromQuery($event, $db, $user_uid, $pdo);
                    echo json_encode($response);
                    
                } else {
                    $response[TAG_ERROR] = true;
                    $response[TAG_MESSAGE] = "Event not updated ... Please try again!";
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

