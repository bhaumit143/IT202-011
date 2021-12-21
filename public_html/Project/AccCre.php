<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<form method="POST"><br/>
  <label> Account Number </label><br/>
  <input type="number" name="account_number" value="<?php echo $result["account_number"];?>" />
  <br/><br/><label>Account Type</label><br/>
  <select name="account_type"><br/>
  <option value = "checking">checking</option>
  </select>
  <br/><br/><label> Account Balance</label><br/>
  <input type="number" min="10.00" name="balance" value="<?php echo $result["balance"];?>" />
	<input type="submit" name="save" value="Create"/>
</form><br/>
<?php 
if(isset($_POST["save"])){
    $account_number = $_POST["account_number"];
    $account_type = $_POST["account_type"]; 
    $user= get_user_id();
    $balance = $_POST["balance"];
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance) VALUES(:account_number, :account_type, :user, :balance)");
    $r = $stmt->execute([
        ":account_number" => $account_number,
        ":account_type"=> $account_type,
        ":user" => $user,
        ":balance" => $balance
    ]);
    if($r){
      flash("Created successfully with id: " . $db->lastInsertId());
    }
    else{
      $e = $stmt->errorInfo();
      flash("Error creating: " . var_export($e, true));
    }

}   
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