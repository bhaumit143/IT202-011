<?php
require(__DIR__ . "/../../partials/nav.php");
$accounts = getDropDown();
?>
   <h3 class="text-center"><strong>Bank Transaction</strong></h3> 
    <hr>
    <form method="POST">     
        <label>From</label placeholder="0"><br/>
            <select name="s_id"><br/>
            <?php foreach($accounts as $row):?><br/>
                <option value="<?php echo $row["id"];?>"> 
                <?php echo $row["account_number"];?>
                </option>
            <?php endforeach;?><br/>
            </select>
        <script>
            function showTransferForm(){
                if(document.getElementById('type').value == "transfer"){
                    document.getElementById('transfer').style.display='block';
                    document.getElementById('transfer').disabled = false; 
                }else{
                    document.getElementById('transfer').style.display='none';
                    document.getElementById('transfer').disabled = true; 
                }
            }
        </script><br/> 
        <div id="transfer" disabled>
            <label>Account </label><br/>
            <select name="d_id">
                <?php foreach($accounts as $row):?>
                    <option value="<?php echo $row["id"];?>">
                    <?php echo $row["account_number"];?>
                    </option>
                <?php endforeach;?>
            </select>
        </div>
        <label>Amount</label><br/> 
        <input type="number" min="1.00" name="amount">
        <br/><label>Action</label><br/> 
        <select name="action" id="type" placeholder="transfer" onclick="showTransferForm()"><br/>
            <option value ="transfer">transfer</option>
            <option value ="deposit">desposit</option>
            <option value ="withdrawl">withdraw</option>
        </select><br/>
        <label>Memo</label><br/>
        <input type="text" name="memo">
        <br/><input class="btn btn-primary" type ="submit" name="save" value="create"/><br/>
    <hr> 
    </form> 
<?php
    if(isset($_POST["save"])){
        $source = $_POST["s_id"]; 
        $destination = $_POST["d_id"]; 
        $amount = $_POST["amount"];
        $action  = $_POST["action"];
        $memo = $_POST["memo"];
        $user = get_user_id();
        $db = getDB();

        $stmt=$db->prepare("SELECT id FROM Accounts WHERE account_number = '000000000000'");
        $results = $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $world_id = $r["id"];
        
        $stmt2=$db->prepare("SELECT balance FROM Accounts WHERE Accounts.id = :q");
        $results2 = $stmt2->execute(["q"=> $source]);
        $r2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $balance = $r2["balance"];

        if(!isset($memo) && empty($memo)){
            $memo = "empty";
        }
        switch($action){
            case "deposit":
                bank($world_id, $source, ($amount * -1), $action, $memo);
            break;
            case "withdrawl":
                if($amount <= $balance){
                bank($source, $world_id, ($amount * -1), $action, $memo);
                }
                elseif($amount > $balance){
                    flash("Balance is Low");
                }
            break;
            case "transfer":
                bank($source,$destination,($amount *-1), $action, $memo);
            break;
        }
          
    }
require_once(__DIR__ . "/../../partials/flash.php");
?>