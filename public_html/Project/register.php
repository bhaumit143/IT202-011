<?php

 Feat-UserRegistration
require_once(__DIR__ . "/../../partials/nav.php"); //flash
if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false); 
    $password = trim(se($_POST, "password", null, false));   
    $confirm = trim(se($_POST, "confirm", null, false)); 
    $username = trim(se($_POST, "username", null, false)); 
    
    $isValid = true; 
    if(!isset($email) || !isset($password) || !isset($confirm) || !isset($username))  {
        se("Must provide email, password, and confirm password"); //flash
        $isValid =false; 
    }   
    if ($password !== $confirm){  
        se("Passwords don't match"); //flash
        $isValid = false;  
    } 
    if (strlen($password) < 3) {
        se("Password must be 3 or more characters"); //flash
        $isValid = false; 
    }   
    $email = sanitize_email($email);
    if(!is_valid_email($email)){
        se("Invalid email"); //flash
        $isValid = false;
    }
    //TODO add validation for username (length? disallow special chars? etc)
=======
require_once(__DIR__ . "/../../partials/nav.php");

if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));   
    $confirm = trim(se($_POST, "confirm", null, false));
    
    $isValid = true;
    if(!isset($email) || !isset($password) || !isset($confirm)){
        se("Must provide email, password, and confirm password");
        $isValid =false;

    }

    if ($password !== $confirm){
        se("Passwords don't match");
        $isValid = false;
    } 
    if (strlen($password) < 3) {
        se("Password must be 3 or more characters");
        $isValid = false; 
    }
    
    $email = sanitize_email($email);
    if(!is_valid_email($email)){
        se("Invalid email");
        $isValid = false;
    }

    if($isValid){
        //do our registration
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password) VALUES (:email, :password)");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {

            $stmt->execute([":email" => $email, ":password" => $hash]);
        } catch(PDOException $e) {
            $code = se($e->errorInfo, 0, "00000", false);
            if ($code === "23000") {

                se("An account with this email already exists"); //flash
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }    
         
=======
                se("An account with this email already exists");
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }    
        

        
        }    
    } 
}



require(__DIR__ . "/../../partials/nav.php"); 
 Milestone1
?>
<form onsubmit="return validate(this)" method="POST">
    <div><br/>
        <label for="email">Email: </label><br/>
        <input type="email" id = "email" name="email" required />
    </div> 
    <div> 
        <label for="username">Username: </label><br/>
        <input type="text" id = "username" name="username" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password: </label><br/>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div> 
    <div>
        <label for="cpw">Confirm Password: </label><br/>
        <input type="password" id ="cpw" name="confirm" required minlength="8" />
    </div> <br/>
    <input type="submit" name = "submit" value="Register" />
</form>
<script>
    function validate(form) { 
        let email = form.email 
        let username = form.username.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;
        if (email) {
            email = email.trim();
        }
        if (username) {
            username = username.trim();
        }
        if (password) {
            password = password.trim();
        }
        if (confirm){
            confirm = confirm.trim();
        }
        if (!username || username.length === 0){
        isValid = false;
        alert("Must provide a username");
        }

        if (email.indexOf("@") === -1){
            isValid = false;
            alert("Invalid email");
        }
 Feat-UserRegistration
        if(password != confirm){
            isValid = false; 
            alert("password don't match"); 
        }
        if(password.length < 3){
            isValid = false;
            alert("password must be 3 or more characters");      
=======
?>
<div> 
    <h1>Register</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <lable for="email">Email: </lable>
            <input type="email" id="email" name="email" required />
        </div>
        <div>
            <lable for="pw">Password: </lable>
            <input type="password" id="pw" name="password" required />
        </div>
        <div>
            <lable for="cpw">Confirm Password: </lable>
            <input type="password" id="cpw" name="confirm" required />   
        </div>
        <div>
            <input type="submit" name="submit" value="Register" />
        </div>
    </form>
<div>
<script>
    function validate(form){
        let email = form.email.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true; 
        if (email){
            email = email.trim();
        }
        if(password){
            password = password.trim();
        }
        if(confirm ){
            confirm = confirm.trim();
        }
        if(email.indexOf("@") === -1){
            isValid = false;
            alert("Invalid email");
        }
        if(password != confirm){
            isValid = false; 
            alert("password don't match");
        }
        if(password.length < 3){
            isValid = false;
            alert("password must be 3 or more characters");     

        }
        return isValid;
    }


</script>  


        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
 Milestone1

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", "", false));
    $confirm = trim(se($_POST,"confirm", "",false));
    $username = trim(se($_POST, "username", null, false));
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!preg_match('/^[a-z0-9_-]{3,16}$/i', $username)) {
        flash("Username must only be alphanumeric and can only contain - or _", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {  
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT); 
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("You registered!"); 
        } catch (Exception $e) {
            flash("There was a problem registering", "danger");
            flash("<pre>" . var_export($e, true) . "</pre>", "danger");
        } 
    }
}
?>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
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
</script>



