<?php
//$constants = parse_ini_file("../../Config/config.ini");

ini_set( "display_errors", true );
date_default_timezone_set( "Europe/Amsterdam" );  // http://www.php.net/manual/en/timezones.php

define( "DB_DSN", "mysql:host=localhost;dbname=plasticbase" );
define( "DB_USERNAME", "root" );
define( "DB_PASSWORD", "" );

define( "CLASS_PATH", "classes" );
define( "TEMPLATE_PATH", "templates" );

define( "ADMIN_USERNAME", "admin" );
define( "ADMIN_PASSWORD", "" );

require( CLASS_PATH . "/User.php" );

if(function_exists('handleException')) {
  echo "Niks doen lekker";  
} else {
  function handleException( $exception ) {
    echo "Sorry, a problem occurred. Please try later.";
    error_log( $exception->getMessage() );
  }
} 
set_exception_handler( 'handleException' );
?>