<?php


require 'king-base.php';
require_once QA_INCLUDE_DIR . 'king-db/selects.php';

reset($_FILES);
$temp    = current($_FILES);
$TempSrc = $temp['tmp_name'];

if ( isset( $TempSrc ) && is_uploaded_file( $TempSrc ) ) {


    $UploadDirectory = 'uploads/'; //specify upload directory ends with / (slash)
    $ffmpeg          = qa_opt('video_ffmpeg'); // where ffmpeg is located, such as /usr/bin/ffmpeg
    $second          = 2;
    $ImageName       = str_replace(' ', '-', strtolower($temp['name']));
    $temptype        = $temp['type'];
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        die();
    }

    switch ( strtolower( $temptype ) ) {
        case 'video/mp4':
            break;
        default:
            die('Unsupported File!');
    }

    $Random_Number = rand(0, 999999);
    $NewFileName = $Random_Number . '-' . basename($ImageName);


    $NewFileName2  = $Random_Number;
    $year_folder   = $UploadDirectory . date("Y");
    $month_folder  = $year_folder . '/' . date("m");

    !file_exists($year_folder) && mkdir($year_folder, 0777);
    !file_exists($month_folder) && mkdir($month_folder, 0777);
    $path  = $month_folder . '/' . $NewFileName;
    $image = $month_folder . '/' . $Random_Number . '.jpg';
    $ret['thumb'] = '';
    $ret['prev']  = '';
    if ( isset( $TempSrc ) ) {
        move_uploaded_file($TempSrc, $path);
        $output = king_insert_uploads($path, $temptype);
        if ( $ffmpeg ) {
            $cmd    = "$ffmpeg -i $path -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
            $return = `$cmd`;
            if (file_exists($image)) {
                list($CurWidth, $CurHeight) = getimagesize($image);
                $output_image               = king_insert_uploads( $image, 'image/jpeg', $CurWidth, $CurHeight );
                $ret['thumb']               = $output_image;
                $ret['prev']                = $image;
            }
        }


        $ret['main'] = $output;
        echo json_encode($ret);
    }
    
} else {
    die('Something wrong with upload! Is "upload_max_filesize" set correctly?');
}
