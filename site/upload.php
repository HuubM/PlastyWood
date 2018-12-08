<?php

require_once '../class/user.php';
require_once 'config.php';

// debugger:
// $user->debug_to_console($uploaded_type);

if (isset($_POST['Upload'])) {

    // File information
    $uploaded_name = $_FILES['uploaded']['name'];
    $uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);
    $uploaded_size = $_FILES['uploaded']['size'];
    $uploaded_tmp = $_FILES['uploaded']['tmp_name'];
    $uploaded_type = $_FILES['uploaded']['type'];

    // Where are we going to be writing to?
    $target_path = 'images/';
    //$target_file   = basename( $uploaded_name, '.' . $uploaded_ext ) . '-';
    $target_file = md5(uniqid() . $uploaded_name . time()) . '.' . $uploaded_ext;
    $temp_file = ((ini_get('upload_tmp_dir') == '') ? (sys_get_temp_dir()) : (ini_get('upload_tmp_dir')));
    $temp_file .= DIRECTORY_SEPARATOR . md5(uniqid() . $uploaded_name) . '.' . $uploaded_ext;

    // Is it an image?
    if ((strtolower($uploaded_ext) == 'jpg' || strtolower($uploaded_ext) == 'jpeg' || strtolower($uploaded_ext) == 'png')
        && ($uploaded_size < 100000)
        && ($uploaded_type == 'image/jpeg' || $uploaded_type == 'image/png') &&
        getimagesize($uploaded_tmp)) {

        // Strip any metadata, by re-encoding image (Note, using php-Imagick is recommended over php-GD)
        if ($uploaded_type == 'image/jpeg') {
            $img = imagecreatefromjpeg($uploaded_tmp);
            imagejpeg($img, $temp_file, 100);
        } else {
            $img = imagecreatefrompng($uploaded_tmp);
            imagepng($img, $temp_file, 9);
        }
        imagedestroy($img);

        // Can we move the file to the web root from the temp folder AND save it into the database?
        if (rename($temp_file, (getcwd() . DIRECTORY_SEPARATOR . $target_path . $target_file)) && $user->uploadImage($target_file, $temp_file, $_POST['user_id'])) {
            // Yes!
            echo "<pre><a href='${target_path}${target_file}'>${target_file}</a> successfully uploaded!</pre>";
        } else {
            // No
            echo '<pre>Your image was not uploaded.</pre>';
        }

        // Delete any temp files
        if (file_exists($temp_file))
            unlink($temp_file);
    } else {
        // Invalid file
        echo '<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
    }
}

?>

<!--
if (isset($_POST['Upload'])) {

// File information
$uploaded_name = $_FILES['uploaded']['name'];   // user uploaded file name
$uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);   // file extension
$uploaded_size = $_FILES['uploaded']['size'];   // size of the file
$uploaded_type = $_FILES['uploaded']['type'];   // type of the file
$uploaded_tmp = $_FILES['uploaded']['tmp_name'];    // temporary name for the file

// Path to store images
//$target_path = 'images/';

// Rename image
$target_file = md5(uniqid() . $uploaded_name . time()) . '.' . $uploaded_ext;

// Check if it is an image
if ((strtolower($uploaded_ext) == 'jpg' || strtolower($uploaded_ext) == 'jpeg' || strtolower($uploaded_ext) == 'png')
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

// Check if file is writable to db
if ($user->uploadImage($target_file, $target_file, $_POST['user_id'])) {
echo '
<pre>Success</pre>';
} else {
echo '
<pre>Your image was not uploaded</pre>';
}

// Delete any temp files
if (file_exists($target_file))
unlink($target_file);
} else {
// Invalid file
echo '
<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
}
}

?>
-->
