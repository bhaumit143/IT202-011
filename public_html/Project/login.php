<?php

require_once(__DIR__ . "/../../partials/nav.php");
if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));     
    
    $isValid = true;
    if(!isset($email) || !isset($password)){
        se("Must provide email, and password");
        $isValid =false;
=======
require(__DIR__ . "/../../partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST"> 
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div> 
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validations 
        //ensure it returns false for an error and true for successs.

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"])) { 
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;

    }
    if (strlen($password) < 3) {
        se("Password must be 3 or more characters");
        $isValid = false; 
    }    
    $email = sanitize_email($email);

    if(!is_valid_email($email)){
        se("Invalid email");
        $isValid = false;
=======
    //validate
    if (!is_valid_email($email)) { 
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;

    }
    if($isValid){
        //do our registration
        $db = getDB();
        //$stmt = $db->prepare("INSERT INTO Users (email, password) VALUES (:email, :password)");
        //$hash = password_hash($password, PASSWORD_BCRYPT); 
        $stmt = $db->prepare("SELECT id, email, password from Users where email = :email LIMIT 1");
        try {
            $stmt->execute([":email" => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user){
                $upass = $user["password"];
                if(password_verify($password, $upass)){
                    se("Yay we logged in");
                    unset($user["password"]);
                    $_SESSION["user"] = $user;
                    echo "<pre>" .var_export($_SESSION, true) . "</pre>";
                    die(header("Location: home.php"));
                } else {
                    se("Password don't match");

                }
            } else {
                se("User doesn't exist");
            }
        } catch(Exception $e) {
            $code = se($e->errorInfo, 0, "00000", false);
            if ($code === "23000") {
                se("An account with this email already exists");
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }    
        
        
        }    
    } 
}
?>
<div> 
    <h1>Login</h1>
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
            <input type="submit" name="submit" value="Login" />
        </div>
    </form>
<div>
<script>
    function validate(form){
        let email = form.email.value;
        let password = form.password.value;
        let isValid = true; 
        if (email){
            email = email.trim();
        }
        if(password){
            password = password.trim();
        }
        
        if(email.indexOf("@") === -1){
            isValid = false;
            alert("Invalid email");
        }
        if(password.length < 3){
            isValid = false;
            alert("password must be 3 or more characters");     
        }
        return isValid;
    }

</script> 