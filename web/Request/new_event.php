<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[EVENT_TITLE]) && isset($_POST[EVENT_DESCRIPTION])
    && isset($_POST[EVENT_LATITUDE]) && isset($_POST[EVENT_LONGITUDE])
    && isset($_POST[EVENT_START_DATE]) && isset($_POST[EVENT_END_DATE]) && isset($_POST[EVENT_START_TIME]) && isset($_POST[EVENT_END_TIME])
    && isset($_POST[EVENT_ORGANISER_TYPE]) && isset($_POST[EVENT_ORGANISER_UID]) && isset($_POST[EVENT_VISIBILITY])
    && isset($_POST[EVENT_ADDRESS]) && isset($_POST[FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $title = $_POST[EVENT_TITLE];
    $description = $_POST[EVENT_DESCRIPTION];
    $lat = $_POST[EVENT_LATITUDE];
    $lng = $_POST[EVENT_LONGITUDE];
    $address = $_POST[EVENT_ADDRESS];
    $start_date = $_POST[EVENT_START_DATE];
    $end_date = $_POST[EVENT_END_DATE];
    $start_time = $_POST[EVENT_START_TIME];
    $end_time = $_POST[EVENT_END_TIME];
    $visibility = $_POST[EVENT_VISIBILITY];
    $organiser_type = $_POST[EVENT_ORGANISER_TYPE];
    $organiser_uid = $_POST[EVENT_ORGANISER_UID];

    $total_files = $_POST[FILE_TOTAL];

	$checkin_page_uid = "NULL";
    if(isset($_POST[CHECKIN_PAGE_UID])){
    	$checkin_page_uid = $_POST[CHECKIN_PAGE_UID];
    }

    $access = $db->accessCheck($user_uid, $user_token);

    if($access) {

        if ($total_files >= 0) {
        	
        	$event = $db->newEvent($user_uid, $organiser_uid, $title, $description, $start_date, $end_date, $start_time, $end_time, $lat, $lng, $total_files, $address, $visibility, $organiser_type);
        	
            //file(s) attached, process file

            $total_result = true;

            for ($i = 0; $i < $total_files; $i++) {

               $current_file = FILE_TAG . $i;
                $current_filename = FILE_NAME . $i;
                $current_description = IMAGE_DESCRIPTION . $i;
                $current_type = IMAGE_TYPE . $i;
                $current_width = WIDTH . $i;
                $current_height = HEIGHT . $i;
                
                $file = $_FILES[$current_file];
                $filename = $_POST[$current_filename];
                $image_description = $_POST[$current_description];
                $type = $_POST[$current_type];
                $width = $_POST[$current_width];
                $height = $_POST[$current_height];
                
                $image_url;
				//$size;
                
                if($type == TYPE_IMAGE){
                	//$size = getimagesize($file['tmp_name']);
                    $image_url = $storage->storeFile($file['tmp_name'], $file['type']);
                }
                else{
                    $result2 = $storage->storeFile($file['tmp_name'], $file['type']);
                    
                    $current_video_bitmap = VIDEO_BITMAP_TAG . $i;
                    $current_video_bitmap_name = VIDEO_BITMAP_NAME . $i;
                    
                    $bitmap = $_FILES[$current_video_bitmap];
                    $bitmap_name = $_POST[$current_video_bitmap_name];
                    
                    //$size = getimagesize($bitmap['tmp_name']);
                    $image_url = $storage->storeFile($bitmap['tmp_name'], $bitmap['type']);
                }
                
                
                $total_result = $db->addImageToPost($event['event_uid'], $image_url, $image_description, $type, $width, $height, 'event');

            }

            if ($total_result) {

                    $response[TAG_ERROR] = false;
                    $response[TAG_EVENT] = $rp->getEventFromQuery($event, $db, $user_uid);
                    echo json_encode($response);

            } else {
            	
            		$db->deleteEvent($event['event_uid']);
            	
	                //at least one file not uploaded successfully
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

