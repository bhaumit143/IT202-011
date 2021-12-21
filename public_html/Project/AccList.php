<?php
require(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You cannot access this page");
    die(header("Location: " . url("login.php")));
}
?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
    
}
if(isset($_POST["search"]) && !empty($query)){
  $db = getDB();
  $stmt = $db->prepare("SELECT id, account_number, account_type, balance, user_id FROM Accounts WHERE account_number like :q LIMIT 10");
  $r = $stmt->execute([":q" => "%$query%"]);
  if($r){
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  else{
    flash("There was a problem fetching the results"); 
  }

}
?>
<form method="POST">
    <input name="query" placeholder="Search" value="<?php flash($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Account Number:</div>
                        <div><?php flash($r["account_number"]); ?></div>
                    </div>
                    <div>
                        <div>Account Type:</div>
                        <div><?php flash($r["account_type"]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php flash($r["balance"]); ?></div>
                    </div>
                    <div>
                        <div>User Id:</div>
                        <div><?php flash($r["id"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="test_edit_account.php?id=<?php flash($r['id']); ?>">Edit</a>
                        <a type="button" href="test_view_account.php?id=<?php flash($r['id']); ?>">View</a>
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