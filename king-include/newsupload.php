<?php

require 'king-base.php';
require_once QA_INCLUDE_DIR . 'king-db/selects.php';
require_once QA_INCLUDE_DIR . 'king-app/video.php';
$imageFolder = "uploads/";
$MaxSize     = 800;
$Quality     = 90;
reset($_FILES);
$temp    = current($_FILES);
$TempSrc = $temp['tmp_name'];
if (is_uploaded_file($TempSrc)) {

    $ImageType = $temp['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.

    $NewImageName = $temp['name'];

    $ret    = king_uploadthumb($NewImageName, $TempSrc, $ImageType);

    echo json_encode(array('location' => $ret['path'], 'id' => $ret['id']));


}
