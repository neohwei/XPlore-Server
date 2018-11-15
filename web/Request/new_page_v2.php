<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[PAGE_TITLE])
    && isset($_POST[PAGE_DESCRIPTION])
    && isset($_POST[PAGE_LATITUDE]) && isset($_POST[PAGE_LONGITUDE])
    && isset($_POST[PAGE_ADDRESS]) && isset($_POST[FILE_TOTAL])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $title = $_POST[PAGE_TITLE];
    $description = $_POST[PAGE_DESCRIPTION];
    $lat = $_POST[PAGE_LATITUDE];
    $lng = $_POST[PAGE_LONGITUDE];
    $address = $_POST[PAGE_ADDRESS];
    $category = $_POST[PAGE_CATEGORY];
    $banner_images = $_POST[PAGE_BANNER_IMAGES];
    $editable_by_others = $_POST[PAGE_EDITABLE_BY_OTHERS];
    
    $total_files = $_POST[FILE_TOTAL];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);

    if($access) {
    	
    	if ($db->isExistingPageName($title, $pdo)) {
		        // user already existed
		        $response[TAG_ERROR] = TRUE;
		        $response[TAG_MESSAGE] = "Page title has been taken!";
		        echo json_encode($response);
		        
		} else {

			$current_filename = FILE_NAME_PROFILE;
	    
	    	$profile_filename = $_POST[$current_filename];
	    
	    	$profile_image_url = $profile_filename;
	    	    
	    	$save_page_category = $db->storeNewPageCategory($category, $user_uid, $pdo);
	    	    
	    	$page = $db->newPage($user_uid, $title, $description, $lat, $lng, $profile_image_url, $banner_images, $address, $category, $editable_by_others, $pdo);

	        if ($total_files > 0) {
	            //file(s) attached, process file
	
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
	                
	                $total_result = $db->addImageToPost($page['page_uid'], $image_url, $image_description, $type, $width, $height, 'page', $pdo);
	
	            }
	
	            if ($total_result) {
	
	                //all files uploaded successfully
	               
	                $response[TAG_ERROR] = false;
	                $response[TAG_PAGE] = $rp->getPageFromQuery($page, $db, $user_uid, $pdo);
	                echo json_encode($response);
	                    
	             
	            } else {
	            	
	            	$db->deletePage($page['page_uid'], $pdo);
	            	
	                //at least one file not uploaded successfully
	                $response[TAG_ERROR] = true;
	                $response[TAG_MESSAGE] = "Files not uploaded successfully ... Please try again!";
	                echo json_encode($response);
	            }
	        } else {
	              
	            //page without additional images uploaded successfully  
	            $response[TAG_ERROR] = false;
	            $response[TAG_PAGE] = $rp->getPageFromQuery($page, $db, $user_uid, $pdo);
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

