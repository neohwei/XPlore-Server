<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[POST_DESCRIPTION])
    && isset($_POST[POST_LATITUDE]) && isset($_POST[POST_LONGITUDE])
    && isset($_POST[POST_TYPE]) && isset($_POST[POST_VISIBILITY])
    && isset($_POST[POST_LOCATION_TYPE]) && isset($_POST[FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $description = $_POST[POST_DESCRIPTION];
    $lat = $_POST[POST_LATITUDE];
    $lng = $_POST[POST_LONGITUDE];
    $visibility = $_POST[POST_VISIBILITY];
    $location_type = $_POST[POST_LOCATION_TYPE];
    $post_type = $_POST[POST_TYPE];

    $total_files = $_POST[FILE_TOTAL];

    $address = "NULL";
    if(isset($_POST[POST_ADDRESS])){
		$address = $_POST[POST_ADDRESS];
    }
    
    $checkin_page_uid = "NULL";
    if(isset($_POST[CHECKIN_PAGE_UID])){
    	$checkin_page_uid = $_POST[CHECKIN_PAGE_UID];
    }

    $access = $db->accessCheck($user_uid, $user_token);

    if($access) {

        if ($total_files >= 0) {
            //file(s) attached, process file
            
            $poster_uid;
		
			if($post_type == TYPE_USER){
				$poster_uid = $user_uid;
			}else{
				$poster_uid = $_POST[PAGE_UID];
			}
            
            $post = $db->newPost($user_uid, $poster_uid, $description, $lat, $lng, $total_files, $address, $visibility, $location_type, $post_type, $checkin_page_uid);

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
                
                //$width = $size[0];
                //$height = $size[1];
                
                $total_result = $db->addImageToPost($post['post_uid'], $image_url, $image_description, $type, $width, $height, 'post');

            }

            if ($total_result) {

                    $response[TAG_ERROR] = false;
                    $response[TAG_POST] = $rp->getPostFromQuery($post, $db, $user_uid);
                    echo json_encode($response);
             
            } else {
	                //at least one file not uploaded successfully
	                
	                $db->deletePost($post['post_uid']);
	                
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

