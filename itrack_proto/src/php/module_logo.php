<?php
  $query_logo1="select company_id from account_detail WHERE account_id='$account_id'";
  $result_logo1=mysql_query($query_logo1,$DbConnection);
  $row_logo1=mysql_fetch_object($result_logo1);
  $company_id1=$row_logo1->company_id;
  
  $query_logo2="SELECT DISTINCT company_info.company_name,company_logo.logo_file FROM company_info,company_logo WHERE company_info.logo_id=company_logo.logo_id AND company_info.company_id='$company_id1'";
  //echo "query=".$query_logo2;
  $result_logo2=mysql_query($query_logo2,$DbConnection);
  $row_logo2=mysql_fetch_object($result_logo2);
  $logo_file1=$row_logo2->logo_file;                          
  $company_name1=$row_logo2->company_name;
  $img='<img type="IMAGE" src="'.$logo_file1.'" height="35">';
  $host = $_SERVER['HTTP_HOST'];
  include('gethostnameurl.php');  
?> 
 <?php echo' <table border="0" width="100%" valing="top" class="module_logo" bgcolor="'.$bgcolor.'">';?>
    <tr>
      <td><?php echo $img; ?></td>
      <td align="center"><?php echo "<font color='#333333'><b>".$company_name1."</b></font>"; ?></td>
    </tr>
  </table>
 
