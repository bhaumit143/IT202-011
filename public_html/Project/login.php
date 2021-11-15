<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>

        <label for="email"><br/>Username/Email</label><br/>
        <input type="email" name="email" required />

        <label for="email">Username/Email</label>
        <input type="text" name="email" required />

    </div>
    <div>
        <label for="pw">Password</label><br/>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div><br/>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation

        //ensure it returns false for an error and true for the success.

        //ensure it returns false for an error and true for success


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
    if (str_contains($email, "@")) {
        //sanitize
        $email = sanitize_email($email);
        //validate
        if (!is_valid_email($email)) {
            flash("Invalid email address", "warning");
            $hasError = true;
        }
    } else {
        if (!preg_match('/^[a-z0-9_-]{3,30}$/i', $email)) {
            flash("Username must only be alphanumeric and can only contain - or _", "warning");
            $hasError = true;
        }
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users where email = :email OR username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        flash("Welcome $email");

                        $_SESSION["user"] = $user; 

                       $_SESSION["user"] = $user;
                        //lookup potential roles
                        $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        //save roles or empty array
                        if ($roles) {

                            $_SESSION["user"]["roles"] = $roles; //at least 1 role

                            $_SESSION["user"]["roles"] = $roles; //at least 1 roles.

                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        die(header("Location: home.php"));
                    } else {
                        flash("Invalid password", "danger");
                    }
                } else {
                    flash("Email not found", "danger");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php 
require(__DIR__ . "/../../partials/flash.php");

?>


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
<br>
<br/>
<input type="submit" name="btn" value="Submit">
</form>

</body>
</html>
<?php
require_once(__DIR__ . "/../../partials/nav.php");
if(isset($_POST["submit"])){
    $email = se($_POST, "email", null, false);
    $password = trim(se($_POST, "password", null, false));     
    
    $isValid = true;
    if(!isset($email) || !isset($password)){
        se("Must provide email, and password");
        $isValid =false;
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

