<?php 
require_once(__DIR__ ."/db.php");
function se($v, $k = null, $default = "", $isEcho = true) {
    
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
    
       
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
function sanitize_email($email= ""){ 
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);

}
function is_valid_email($email= ""){
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
//User Helper
function is_logged_in(){
    return isset($_SESSION["user"]); //se($_SESSION, "user", false, false);
}
function get_username(){
    if (is_logged_in()){ //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"],"username", "", false);

    }
    return "";
}
function get_user_email(){
    if (is_logged_in()){//we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);

    }
    return "";
}
function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}
function get_user_id(){
    if (is_logged_in()){//we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
// falsh message system
function flash($msg = "", $color="info") 
{
    $message = ["text"=>$msg, "color" => $color]; 
    if (isset($_SESSION['flash'])){
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}

function getMessage(){
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;     
    }
    return array();
}

function getDropDown(){
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT id, account_number FROM Accounts WHERE Accounts.user_id = :id");
    $r = $stmt->execute([
        ":id"=>$user
    ]);  

    if($r){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results; 
    }
    else{
     flash("There was a problem fetching the accounts");
    }

}
function bank($acc1, $acc2, $amount, $action, $memo)
{
    $db = getDB();
    $user = get_user_id();

    $stmt2 = $db ->prepare("SELECT IFNULL(SUM(Amount),0) AS Total FROM Transactions WHERE Transactions.act_src_id = :q");
    $results2 = $stmt2->execute([":q"=> $acc1]);
    $r2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $balanceAcc1 = $r2["Total"];

    $acc1NewBalance = $balanceAcc1 + $amount;

    $stmt3 = $db ->prepare("SELECT IFNULL(SUM(Amount),0) AS Total FROM Transactions WHERE Transactions.act_src_id = :q");
    $results3 = $stmt3->execute([":q"=> $acc2]);
    $r3 = $stmt3->fetch(PDO::FETCH_ASSOC);
    $balanceAcc2 = $r3["Total"];
    $acc2NewBalance = $balanceAcc2 + ($amount*-1);


    $stmt = $db ->prepare("INSERT INTO Transactions (act_src_id, act_dest_id, amount, action_type, memo, expected_total)
        VALUES (:s_id, :d_id, :amount, :action_type, :memo, :expected_total), (:s_id2, :d_id2, :amount2, :action_type2, :memo2, :expected_total2)" );
        //called in create then it doesn't need to be called here
            
                $r = $stmt->execute([
                    //first part
                    ":s_id" => $acc1,
                    ":d_id" => $acc2,
                    ":amount" => $amount,
                    ":action_type" => $action,
                    ":memo" => $memo,
                    ":expected_total" => $acc1NewBalance,
                    //second part
                    ":s_id2" => $acc2,
                    ":d_id2" => $acc1,
                    ":amount2" => ($amount*-1),
                    ":action_type2" => $action,
                    ":memo2" => $memo,
                    ":expected_total2" => $acc2NewBalance
                ]);
                if ($r) {
                    flash("Transaction Complete!");

                    $stmt = $db ->prepare("SELECT IFNULL(SUM(Amount),0) AS Total FROM Transactions WHERE Transactions.act_src_id = :id");
                    $r = $stmt->execute([
                            ":id" => $acc1
                    ]);
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    $source_total = $results["Total"]; 
                
                    if ($source_total) {
                        flash("Check 1 Successfull");
                    }
                    else {
                        $e = $stmt->errorInfo();
                        flash("Error getting source total: " . var_export($e, true));
                    }


                    $stmt = $db ->prepare("SELECT IFNULL(SUM(Amount),0) AS Total FROM Transactions WHERE Transactions.act_src_id = :id");
                    $r = $stmt->execute([
                        ":id" => $acc2
                    ]);
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    $destination_total = $results["Total"]; 

                    if ($destination_total) {
                        flash("Check 2 Successfull");
                    }
                    else {
                        $e = $stmt->errorInfo();
                        flash("Error getting destination total: " . var_export($e, true));
                    }

                            $stmt4=$db->prepare("UPDATE `Accounts` SET `balance` = :x WHERE id = :q");
                            $results4 = $stmt4->execute([":q"=> $acc1, ":x" => $source_total]);

                            $stmt4=$db->prepare("UPDATE `Accounts` SET `balance` = :x WHERE id = :q");
                            $results4 = $stmt4->execute([":q"=> $acc2, ":x" => $destination_total]);
                            
                        }
                        else {
                            $e = $stmt->errorInfo();
                            flash("Error creating: " . var_export($e, true));
                        }
        
}

function url($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
}

// end flash message system
?> 
