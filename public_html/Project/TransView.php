<?php
require(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
$accounts = getDropDown();
?>
<?php
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT`Transactions`.`act_src_id` AS `id`, `Transactions`.`act_dest_id` as `did`, `amount`, `action_type` FROM `Transactions` WHERE `id` = id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
    $stmt2 = $db->prepare("SELECT account_number FROM Accounts WHERE Accounts.id = id");
    $r2 = $stmt2->execute([":id" => $id]);
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if (!$result2) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>