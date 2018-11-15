<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[PAGE_UID]) && isset($_POST[PAGE_TITLE])
    && isset($_POST[PAGE_DESCRIPTION])
    && isset($_POST[PAGE_LATITUDE]) && isset($_POST[PAGE_LONGITUDE])
    && isset($_POST[PAGE_ADDRESS]) && isset($_POST[FILE_TOTAL]) && isset($_POST[DELETE_FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $page_uid = $_POST[PAGE_UID];
    $page_title = $_POST[PAGE_TITLE];
    $page_description = $_POST[PAGE_DESCRIPTION];
    $page_lat = $_POST[PAGE_LATITUDE];
    $page_long = $_POST[PAGE_LONGITUDE];
    $page_address = $_POST[PAGE_ADDRESS];
    $banner_images = $_POST[PAGE_BANNER_IMAGES];
    
    $total_files = $_POST[FILE_TOTAL];
    $total_delete_files = $_POST[DELETE_FILE_TOTAL];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);

    if($access) {
    	
    	if ($db->isExistingPageName($page_title, $pdo) && $db->isPageOwnName($page_uid, $page_title, $pdo) == false) {
		        // user already existed
		        $response[TAG_ERROR] = TRUE;
		        $response[TAG_MESSAGE] = "Page title has been taken!";
		        echo json_encode($response);
		        
		} else {

            $total_result = true;

    	    $current_filename = FILE_NAME_PROFILE;

	    	$profile_filename = $_POST[$current_filename];
	    	$profile_image_url = $profile_filename;
    
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
                
                $total_result = $db->addImageToPost($page_uid, $image_url, $image_description, $type, $width, $height, 'page', $pdo);

            }

		    for ($i = 0; $i < $total_delete_files; $i++) {
	
				$current_image = IMAGE_UID . $i;
				$image_uid = $_POST[$current_image];
		                
				$db->removeImageFromPost($image_uid, $pdo);
		    }

            if ($total_result) {

                //all files uploaded successfully
                $page = $db->editPage($page_uid, $user_uid, $page_title, $page_description, $page_lat, $page_long, $profile_image_url, $banner_images, $page_address, $pdo);

                if ($page != false) {
                    $response[TAG_ERROR] = false;
                    $response[TAG_PAGE] = $rp->getPageFromQuery($page, $db, $user_uid, $pdo);
                    echo json_encode($response);
                    
                } else {
                    $response[TAG_ERROR] = true;
                    $response[TAG_MESSAGE] = "Page not updated ... Please try again!";
                    echo json_encode($response);
                }

            } else {
                //at least one file not uploaded successfully
                $response[TAG_ERROR] = true;
                $response[TAG_MESSAGE] = "Files not uploaded successfully ... Please try again!";
                echo json_encode($response);
            }
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

