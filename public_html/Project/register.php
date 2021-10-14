<?php
require_once(__DIR__ . "/../../partials/nav.php");

if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));   
    $confirm = trim(se($_POST, "confirm", null, false));
    
    $isValid = true;
    if(!isset($email) || !isset($password) || !isset($confirm) || !isset($username)) {
        flash("Must provide email,username, password, and confirm password", "warning");
        $isValid =false;

    }

    if ($password !== $confirm){
        flash("password don't match", "warning");
        $isValid = false;
    } 
    if (strlen($password) < 3) {
        flash("Password must be 3 or more characters", "warning");
        $isValid = false; 
    }
    
    $email = sanitize_email($email);
    if(!is_valid_email($email)){
        flash("Invalid email", "warning");
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
                flash("An account with this email already exists", "danger");
            } else {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }    
        
        
        }    
    } 
}

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
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>