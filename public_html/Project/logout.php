<?php
session_start();
session_unset();
session_destroy();


//setcookies("PHPESSID", "", time()-3600);

//setcookie("PHPESSID", "", time()-3600);

require_once(__DIR__ . "/../../partials/nav.php");
flash("You have been logged out!", "success");
die(header("Location: login.php")); 

setcookie("PHPESSID", "", time()-3600);
die(header("Location: login.php"));

?> 
