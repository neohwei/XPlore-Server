<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';

$rp = $util['malp.reply'];
$db = $util['malp.model'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_ID]) && isset($_POST[USER_NAME]) && isset($_POST[USER_EMAIL]) && isset($_POST[USER_PASSWORD])) {

    // receiving the post params
    $id = $_POST[USER_ID];
    $name = $_POST[USER_NAME];
    $email = $_POST[USER_EMAIL];
    $password = $_POST[USER_PASSWORD];

    // check if user is already existed with the same email
    if ($db->isExistingEmail($email)) {
        // user already existed
        $response[TAG_ERROR] = TRUE;
        $response[TAG_MESSAGE] = "User already existed with $email";
        echo json_encode($response);
    } else if ($db->isExistingID($id)){
    	 $response[TAG_ERROR] = TRUE;
        $response[TAG_MESSAGE] = "User already existed with $id";
    } else {
        // create a new user
        $user = $db->register($id, $name, $email, $password);
        if ($user) {
            // user stored successfully
            $response[TAG_ERROR] = FALSE;
            $response[TAG_USER] = $rp->getUserFromQuery($user, $db, $user[USER_UID]);
            $response[TAG_USER][USER_TOKEN] = $user[USER_TOKEN];
            $response[TAG_USER][USER_EMAIL] = $user[USER_EMAIL];
    
            echo json_encode($response);
        } else {
            // user failed to store
            $response[TAG_ERROR] = TRUE;
            $response[TAG_MESSAGE] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response[TAG_ERROR] = TRUE;
    $response[TAG_MESSAGE] = "Required parameters missing!";
    echo json_encode($response);
}
?>

