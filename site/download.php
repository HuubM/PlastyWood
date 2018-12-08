<?php

require_once '../class/user.php';
require_once 'config.php';

// debugger:
// $user->debug_to_console($uploaded_type);

if (isset($_POST['Download'])) {
    $user->getImages($_POST['user_id']);
}