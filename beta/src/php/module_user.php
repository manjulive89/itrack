<?
  include_once('util_session_variable.php');
  include_once("util_php_mysql_connectivity.php");
  
  $user_account=$_GET['user_account_1'];
  $data="";
  
  if($user_account!=null)
  {
    $query1="SELECT admin_id FROM account_detail WHERE account_id='$user_account'";
    $result1=mysql_query($query1,$DbConnection);
    $row1=mysql_fetch_object($result1);
    $admin_id=$row1->admin_id;
    // echo"user_acc=".$admin_id2."<br>";
    $query2="SELECT DISTINCT account_id,user,grp FROM account WHERE ".
    "account_id IN (SELECT DISTINCT account_id FROM account_detail WHERE ".
    "admin_id='$admin_id_1' OR account_admin_id='$admin_id' ORDER BY account_id)"; 
    //echo "query=".$query3;
    $result2=mysql_query($query2,$DbConnection);
    $i=0;
    while($row2=mysql_fetch_object($result2))
    {
      $account_id_local=$row2->account_id; 
      $user_local=$row2->user;
      $data=$data.":".$account_id_local.":".$user_local;   
    }
    //echo $query2;
    echo $data;
  }
?>
