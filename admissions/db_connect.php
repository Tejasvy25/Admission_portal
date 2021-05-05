<?php
date_default_timezone_set('Asia/Kolkata');
define("HOST", "*******"); // The host you want to connect to.
define("USER", "*******"); // The database username.
define("PASSWORD", "*******"); // The database password. 
define("DATABASE", "*******"); // The database name.
define("prefix","");
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);// If you are connecting via TCP/IP rather than a UNIX socket remember to add the port number as a parameter.
$con=mysqli_connect(HOST,USER,PASSWORD, DATABASE);

if (!$con) {
    echo "Unable to connect to DB: " . mysql_error();
    exit;
}





?>
