<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/24/2018
 * Time: 10:12 AM
 */

class FileProcessor
{

    public function __construct(){
    }

    public function uploadFile($user_uid, $file, $filename){
        $folder_path = "/var/www/html/TrenderServer/Uploads/".$user_uid."/";

	if(!file_exists($folder_path)){
		$oldmask = umask(0);
		mkdir($folder_path, 0777);
		umask($oldmask);
	}

        $file_path = $folder_path . $filename;
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
	    $size = getimagesize($file_path);
            return $size;
        } else {
            return false;
        }
    }

    public function uploadVideoFile($user_uid, $file, $filename){
        $folder_path = "/var/www/html/TrenderServer/Uploads/".$user_uid."/";

	if(!file_exists($folder_path)){
		$oldmask = umask(0);
		mkdir($folder_path, 0777);
		umask($oldmask);
	}

        $file_path = $folder_path . $filename;
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return true;
        } else {
            return false;
        }
    }
}

?>
