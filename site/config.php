<?php
session_start();
define('conString', 'mysql:host=localhost;dbname=db_plastywood');
define('dbUser', 'root');
define('dbPass', '');

define('userfile', 'user.php');
define('loginfile', 'login.php');
define('activatefile', 'activate.php');
define('registerfile', 'register.php');

//template files
define('indexHead', 'inc/indexhead.htm');
define('indexTop', 'inc/indextop.htm');
define('loginForm', 'inc/loginform.php');
define('activationForm', 'inc/activationform.php');
define('indexMiddle', 'inc/indexmiddle.htm');
define('registerForm', 'inc/registerform.php');
define('indexFooter', 'inc/indexfooter.htm');
define('userPage', 'inc/userpage.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// disable XML external entity loading explicitly
$previous = libxml_disable_entity_loader(true);
// and restore XML external entity loading on exit
register_shutdown_function('libxml_disable_entity_loader', $previous);

$user = new User();
$user->dbConnect(conString, dbUser, dbPass);
