<?php

//require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
include_once __DIR__ . '/../vendor/autoload.php';
use google\appengine\api\cloud_storage\CloudStorageTools;
$util = require __DIR__ . '/../src/util.php';

$options = ['gs_bucket_name' => $util['malp.bucketname']];

$upload_url = CloudStorageTools::createUploadUrl('/upload_handler.php', $options);
echo $upload_url;

?>
