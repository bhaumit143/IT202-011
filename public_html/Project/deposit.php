<?php
error_reporting(0);
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You can signed in to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if (isset($_SESSION["user"])) {
	$email = $_SESSION["user"]["email"];
}
if (!empty($email)) {
	$db = getDB();
	$stmt = $db->prepare("SELECT acc.account_number FROM Accounts as acc JOIN Users on acc.user_id = Users.id WHERE Users.email =:email");
   	$r = $stmt->execute([":email" => "$email"]);
   	if ($r) {
       		 $accResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
   	}
   	else {
       		 flash("There was problem to view your account.");
   	}
}
?>
    <form method="POST">
        <label>Transactions:</label>
        <select name="type">
		<option value="saving">saving</option>
	</select>
        <label>Account</label>
        <select name="account">
		<?php foreach ($accResults as $ar): ?>
		<option value="<?php flash($ar["account_number"]); ?>"><?php flash($ar["account_number"]); ?></option>
		<?php endforeach; ?>
	</select>
	<label>Total Amount</label>
        <input type="number" min="0.01" step="0.01" name="amount"/>
	<label>Memo</label>
	<input type="text" name="memo" placeholder=" Sending a Message"/>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    $src = $_POST["account"];
    $dest = "000000000000"; 
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $created = date('Y-m-d H:i:s');
    $db = getDB();
    $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acct");
    $r = $stmt->execute([":acct" => $src]);
    $resultSrc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultSrc){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a1total = $resultSrc["balance"];
    $src = $resultSrc["id"]; 

    $r = $stmt->execute([":acct" => $dest]);
    $resultDest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultDest){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a2total = $resultDest["balance"];
    $dest = $resultDest["id"];

    switch($type){
	case "Deposit":
		$a1total += $amount;
		$a2total -= $amount;
		break;
	case "Withdraw":
		if($amount > $a1total){
			flash("You cannot withdraw more than your current balance");
			die(header("Location: depositwithdraw.php"));
		}
		$a1total -= $amount;
		$a2total += $amount;
		$amount = $amount * -1;
		break;
	case "Transfer":
		$a1total -= $amount;
		$a2total += $amount;
		$amount = $amount * -1;
		break;
    }

    $stmt = $db->prepare("INSERT INTO Transactions (act_src_id, act_dest_id, amount, action_type, memo, expected_total, created) VALUES(:p1a1, :p1a2, :p1amount, :type, :memo, :a1total, :created), (:p2a1, :p2a2, :p2amount, :type, :memo, :a2total, :created)"); 
    $r = $stmt->execute([
        ":p1a1" => $src,
        ":p1a2" => $dest,
        ":p1amount" => $amount,
        ":type" => $type,
        ":memo" => $memo,
	":a1total" => $a1total,
	":created" => $created,

	":p2a1" => $dest, 
        ":p2a2" => $src,
        ":p2amount" => ($amount*-1),
        ":type" => $type,
        ":memo" => $memo,
	":a2total" => $a2total,
	":created" => $created,
    ]);
    if ($r) {
        
    }
    else {
        $e = $stmt->errorInfo();
        flash("Creating error: " . var_export($e, true));
    }
    $stmt = $db->prepare("UPDATE Accounts set balance=:balance WHERE id=:id");
    $r = $stmt->execute([
	":balance" => $a1total,
	":id" => $src
    ]);
    $r2 = $stmt->execute([
	":balance" => $a2total, 
	"id" => $dest
    ]);
    if($r && $r2) {
	flash("succesfull $type!");
    }
    else{
	 flash("Account balance is updatind error");
    }
}
require_once(__DIR__ . "/../../partials/flash.php");
?>