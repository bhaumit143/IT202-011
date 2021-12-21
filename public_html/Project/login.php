<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST"> 

</style> 


    <div>
        <label for="email"><br/>Email</label>
        <input type="email" name="email" required/><br/>
        
<style>
form {
width:100%;
height:280px;
font-size:20px;
margin:auto;
position:relative;
}
input {
width:100%;
size:1000px;
height: 25px;
}
</style> 
    
</div>


        
<style>
form {
width:100%;
height:280px;
font-size:20px;
margin:auto;
position:relative;
}
input {
width:100%;
size:1000px;
height: 25px;
}
</style> 
    
</div>
    <div>

        <label for="pw"><br/>Password</label>
        <input type="password" id="pw" name="password" required minlength="8"/><br/>
        


    </div><br/>
    <br/><input type="submit" value="Login"/>
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validations 
        //ensure it returns false for an error and true for successs

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
    //sanitize
    $email = sanitize_email($email);
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
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users where email = :email");
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
<br>
<br/>
<input type="submit" name="btn" value="Submit">
</form>


</body>

</html>

</html>


</body>
</html>