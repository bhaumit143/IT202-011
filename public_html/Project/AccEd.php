<?php
require(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
  flash("You cannot access this page");
  die(header("Location: login.php"));
}
?>
<?php
  if(isset($_GET["id"])){
    $id = $_GET["id"];
  }
?>
<?php
if(isset($_POST["save"])){
      $account_number = $_POST["account_number"]; 
      $account_type = $_POST["account_type"]; 
      $balance = $_POST["balance"];
      $db = getDB();
      $user = get_user_id();
      if(isset($id)){
        $stmt = $db->prepare("UPDATE Accounts set account_number=:account_number, account_type=:account_type, balance=:balance where id=:id");
        $r = $stmt->execute([
          ":account_number"=> $account_number,
          ":account_type"=>$account_type,
          ":balance"=>$balance,
          ":id"=>$id
        ]);
  
        if($r){
          flash("Updated successfully with id: " . $id);
        }
        else{
          $e = $stmt->errorInfo();
          flash("Error updating: " . var_export($e, true));
        }  
      }
}
?>
<?php
$result = []; 
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Accounts where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<form method="POST">
  <label>Account Number</label>
  <input type="number" name="account_number" value="<?php echo $result["account_number"];?>"/>
  <label>Account Type</label>
  <select name="account_type" value="<?php echo $result["account_type"];?>">
		<option value="checking" <?php se ($result["account_type"] == "0"?'selected="selected"':'');?>>checking</option>
               
                <option value="world" <?php se ($result["account_type"] == "4"?'selected="selected"':'');?>>world</option>
	</select>
  <label>Balance</label> 
  <input type="number" min="10.00" name="balance" value="<?php echo $result["balance"];?>" />
  <input type="submit" name="save" value="update"/>
</form>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>