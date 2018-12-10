<?php

require_once '../class/user.php';
require_once 'config.php';

if (isset($_POST['download'])) {
    $user->showImagesByUserID($_POST['user_id']);
}