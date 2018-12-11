<?php

require_once '../class/user.php';
require_once 'config.php';

if (isset($_POST['upload'])) {

    $error = $_FILES['uploaded']['error'];
    echo "Error: " . $error;
    echo "<br>";
    // Name of the file
    $uploaded_name = $_FILES['uploaded']['name'];
    echo "Name: " . $uploaded_name;
    echo "<br>";
    // Extension of the file
    $uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);
    echo 'Extension: ' . $uploaded_ext;
    echo "<br>";
    // Size of the file
    $uploaded_size = $_FILES['uploaded']['size'];
    echo 'Size: ' . $uploaded_size;
    echo "<br>";
    // Type of the file
    $uploaded_type = $_FILES['uploaded']['type'];
    echo 'Type: ' . $uploaded_type;
    echo "<br>";
    // The file itself
    $uploaded_tmp = $_FILES['uploaded']['tmp_name'];
    echo 'File: ' . $uploaded_tmp;
    echo "<br>";

    // Rename file
    $target_file = addslashes(md5(uniqid() . $uploaded_name . time()) . '.' . strtoupper($uploaded_ext));
    echo 'Renamed name: ' . $target_file;
    echo "<br>";
    echo "<br>";

    // Check if it is an image
    if(!(strtolower($uploaded_ext) == 'jpg' || strtolower($uploaded_ext) == 'jpeg' || strtolower($uploaded_ext) == 'png')) {
        echo 'The extension is not of the following: .jpg/.jpeg/.png';
    } else if(!($uploaded_size < 100000)) {
        echo 'file is too big';
    } else if( !($uploaded_type == 'image/jpeg' || $uploaded_type == 'image/png' || $uploaded_type == 'image/jpg') ) {
        echo 'This is not an image';
    } else if(getimagesize($uploaded_tmp) === false) {
        echo 'HAHA, do not try to hack us!!!!';
    }
    else if ((strtolower($uploaded_ext) == 'jpg' || strtolower($uploaded_ext) == 'jpeg' || strtolower($uploaded_ext) == 'png')
        && ($uploaded_size < 100000)
        && ($uploaded_type == 'image/jpeg' || $uploaded_type == 'image/png')
        && getimagesize($uploaded_tmp)) {

        // Strip any metadata by re-encoding image
        if ($uploaded_type == 'image/jpeg') {
            $img = imagecreatefromjpeg($uploaded_tmp);
            imagejpeg($img, $target_file, 100);
        } else {
            $img = imagecreatefrompng($uploaded_tmp);
            imagepng($img, $target_file, 9);
        }
        imagedestroy($img);

        // Real image to upload to the db
        $imgToUpload = base64_encode(file_get_contents(addslashes($target_file)));

        // Check if file is writable to db
        if ($user->uploadImage($target_file, $imgToUpload, $_POST['user_id'])) {
            echo '<pre>Success by console.</pre>';
        } else {
            echo '<pre>Your image was not uploaded by console.</pre>';
        }

        // Delete any temp files
        if (file_exists($target_file))
            unlink($target_file);
    } else {
        // Invalid file
        echo '<pre>Your image was not uploaded due to an unknown error.</pre>';
    }
}