<?php
session_start();
session_unset();
session_destroy();
setcookie("PHPESSID", "", time()-3600);
die(header("Location: login.php"));

?> 
