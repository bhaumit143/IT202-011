<?php
session_start();
session_unset();
session_destroy();

//setcookie("PHPESSID", "", time()-3600);
require_once(__DIR__ . "/../../partials/nav.php");
flash("You have been logged out!", "success");
die(header("Location: login.php")); 

?>

<html>

<head>

</head>
<body bgcolor="<?php
if (isset($_POST['btn']))
{
$col=$_POST['t1'];
if(isset($col))
{
echo $p=$col;
}
else
{
echo $p="#ffffff";
}
}
?>">

<form action="" method="post" >
<strong> Choose Color to Change Background :- </strong>
<select name="t1">
<option value="">Choose Color </option>
<option value="#000000"> Black </option>
<option value="#0000ff"> Blue </option>
<option value="#a52a2a"> Brown </option>
<option value="#00ffff"> Cyan </option>
<option value="#006400"> Dark Green </option>
<option value="#808080"> Grey </option>
<option value="#008000"> Green </option>
<option value="#ffa500"> Orange </option>
<option value="#ffc0cb"> Pink </option>
<option value="#800080"> Purple </option>
<option value="#ff0000"> Red </option>
<option value="#ffffff"> White </option>
<option value="#ffff00"> Yellow </option>
</select>
<br><br/>
<input type="submit" name="btn" value="Submit">
</form>

</body>
</html>
=======
setcookie("PHPESSID", "", time()-3600);
die(header("Location: login.php"));

?> 

