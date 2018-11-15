<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../src/DataModel/DataDefinitions.php';
$util = require __DIR__ . '/../../src/util.php';
$rp = $util['malp.reply'];
$db = $util['malp.model'];

// json response array
$response = array(TAG_ERROR => FALSE);

if (isset($_POST[USER_ID]) && isset($_POST[USER_PASSWORD])) {

    // receiving the post params
    $id = $_POST[USER_ID];
    $password = $_POST[USER_PASSWORD];

    // get the user by email and password
    $user = $db->login($id, $password);

    if ($user != false) {
        // user is found
        $response[TAG_ERROR] = FALSE;
        $response[TAG_USER] = $rp->getUserFromQuery($user, $db, $user[USER_UID], null);
        $response[TAG_USER][USER_TOKEN] = $user[USER_TOKEN]; //new token
        $response[TAG_USER][USER_EMAIL] = $user[USER_EMAIL];
      
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response[TAG_ERROR] = TRUE;
        $response[TAG_MESSAGE] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
   
   echo json_encode($response);
   
} else {
   // required post params is missing
    $response[TAG_ERROR] = TRUE;
    $response[TAG_MESSAGE] = "Required parameters missing!";
    echo json_encode($response);
}
?>

