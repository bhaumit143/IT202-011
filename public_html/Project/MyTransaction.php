<?php
require(__DIR__ . "/../../partials/nav.php");
$query = "";
$results = [];
$results2 = [];
if(isset($_GET["id"])){ 
  $user = $_GET["id"];
}
else{
    flash("The id is not pull");
}
?>
<?php
if (isset($user) && !empty($user)) {
    $db = getDB();
    $stmt=$db->prepare("SELECT amount, action_type, created, act_src_id, act_dest_id, Transactions.id as tranID FROM Transactions as Transactions JOIN Accounts ON Transactions.act_src_id = Accounts.id WHERE Accounts.id = :q LIMIT 10");
    $r = $stmt->execute([ ":q" => $user]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        flash("The results are successfull");
    }
    else {
        flash("There was a problem listing your transactions");
        echo var_export($stmt->errorInfo(), true);
    }
}
?>
<h3>List Transcation</h3>
<div class="results">
        <?php if (count($results) > 0): ?>
            <div class="list-group">
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item">
                        <div>
                            <div><strong>Action Type:</strong></div>
                            <div><?php flash($r["action_type"]); ?></div>
                        </div>
                        <div>
                            <div><strong>Sources:</strong></div>
                            <div><?php flash($r["act_src_id"]); ?></div>
                        </div>
                        <div>
                            <div><strong>Destinations:</strong></div>
                            <div><?php flash($r["act_dest_id"]); ?></div>
                        </div>
                        <div>
                            <div><strong>Total Amount:</strong></div>
                            <div><?php flash($r["amount"]); ?></div>
                        </div>
                        <div>
                            <a type="button" href="<?php url("AccView.php?id=" . $r["tranID"]); ?>">Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>