<?php
 
require_once(__DIR__ . "/../../partials/nav.php");
if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false)); // here s 
    $confirm = trim(se($_POST, "confirm", null, false));
    $username = trim(se($_POST, "username", null, false)); // do it here
    
    $isValid = true; 
    if(!isset($email) || !isset($password) || !isset($confirm) || !isset($username))  {
        se("Must provide email, password, and confirm password"); // do it here 
        $isValid =false; 
    }  
    if ($password !== $confirm){  
        se("Passwords don't match"); //do it here  
        $isValid = false; 
    } 
    if (strlen($password) < 3) {
        se("Password must be 3 or more characters"); // do it here 
        $isValid = false; 
    }   
    $email = sanitize_email($email);
    if(!is_valid_email($email)){ 
        se("Invalid email"); // do it here 
        $isValid = false;
    }
    //TODO add validation for username (length? disallow special chars? etc)
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
                se("An account with this email already exists"); // do it here 
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }    
         
        
        }    
    } 
}


require(__DIR__ . "/../../partials/nav.php"); 
 
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email: </label>
        <input type="email" id = "email" name="email" required />
    </div> 
    <div> 
        <label for="username">Username: </label>
        <input type="text" id = "username" name="username" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password: </label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div> 
    <div>
        <label for="cpw">Confirm Password: </label>
        <input type="password" id ="cpw" name="confirm" required minlength="8" />
    </div> 
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
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

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
