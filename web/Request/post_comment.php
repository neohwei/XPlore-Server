<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];
$storage = $util['malp.storage'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_UID]) && isset($_POST[USER_TOKEN]) && isset($_POST[POST_UID]) && isset($_POST[COMMENT_DESCRIPTION]) && isset($_POST[TAG])) {

    // receiving the post params
    $user_uid = $_POST[USER_UID];
    $user_token = $_POST[USER_TOKEN];
    $post_uid = $_POST[POST_UID];
    $comment_description = $_POST[COMMENT_DESCRIPTION];
    $tag = $_POST[TAG];
    
    $pdo = $db->getPDO();

    $access = $db->accessCheck($user_uid, $user_token, $pdo);

    if($access) {

                $result = $db->postComment($user_uid, $post_uid, $comment_description, $tag, $pdo);

                if ($result) {
                    $response[TAG_ERROR] = false;
                    $response[TAG_MESSAGE] = "Comment posted!";
                    echo json_encode($response);
                } else {
                    $response[TAG_ERROR] = true;
                    $response[TAG_MESSAGE] = "Comment not posted ... Please try again!";
                    echo json_encode($response);
                }

    }else{
      
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

