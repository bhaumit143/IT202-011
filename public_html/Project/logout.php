<?php
session_start();
session_unset();
session_destroy();
//setcookie("PHPESSID", "", time()-3600);
require_once(__DIR__ . "/../../partials/nav.php");
flash("You have been logged out", "success");
die(header("Location: login.php"));

