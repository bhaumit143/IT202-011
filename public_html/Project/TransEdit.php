<?php
require(__DIR__ . "/../../partials/nav.php");

?>
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
if (isset($_POST["save"])) {
    if($transaction <= 0){
        $transaction = null; 
    }
    $amount = $_POST("amount");
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) { 
        $stmt = $db->prepare("UPDATE Transactions set amount where id=:id");
        $r = $stmt->execute([
            ":amount" => $amount,
        ]);
        if ($r) {
            flash("Modified: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID is not set yet, we need ID in order to update");
    }
}
?>
<?php
$result = [];
if (isset($id)) {
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM TRANSACTIONS where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
    <h3>Edit Transaction</h3>
    <form method="POST">
        
        <label> Amount Change </label> 
        <input type="number" min="10.00" name="amount" value="<?php echo $result["amount"];?>" />
        <input type="submit" name="save" value="Update"/>

    </form>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
