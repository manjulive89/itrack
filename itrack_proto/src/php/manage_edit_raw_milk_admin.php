
<?php
echo "edit##";
?>
<style>
   /* html, body{
margin:0;
padding:0;
height:100%;
}*/
section {
position: relative;
border: 1px solid #000;
padding-top: 37px;
/*background: #500;*/
}
section.positioned {
position: absolute;
top:100px;
left:100px;
width:800px;
box-shadow: 0 0 15px #333;
}
.container {
overflow-y: auto;
height: 350px;
}
/*table {
border-spacing: 0;
width:100%;
}*/
.tablescroll {
border-spacing: 0;
width:100%;
}
.tablescroll td + td {
border-left:1px solid #eee;
}
.tablescroll td, th {
border-bottom:1px solid #eee;
/*background: #ddd;*/
color: #000;
padding: 5px 15px;
}
.tablescroll th {
height: 0;
line-height: 0;
padding-top: 0;
padding-bottom: 0;
color: transparent;
border: none;
white-space: nowrap;
}
.tablescroll th div{
position: absolute;

background: transparent;
/*color: #fff;*/
color: black;
padding: 9px 25px;
top: 0;
margin-left: -25px;
line-height: normal;
border-left: 1px solid #800;
}
.tablescroll th:first-child div{
border: none;
}
</style>   
    <?php
	include_once('Hierarchy.php');
	include_once('util_session_variable.php');
	include_once('util_php_mysql_connectivity.php'); 	
	$root=$_SESSION['root'];		
	//echo "edit##"; 
	include_once("util_account_detail.php");
	$account_id_local=$_POST['common_id'];	
	$startdate = str_replace('/','-',$startdate);
	$enddate = str_replace('/','-',$enddate);
	$all_data=0;$all_data_without_date=0;$targetdatewise=0;
	if($order=="3")
	{
		$all_data = 1;
	}
	if($order=="6")
	{
		$all_data_without_date = 1;
	}
	if($order=="7")
	{
		$targetdatewise = 1;
	}
        if($order=="8")
	{
		$unloadaccepttime = 1;
	}
	$targetplant=$_POST['targetplant'];
	//echo $targetplant;
        //echo $order."<br>";
        //echo $targetdatewise;
	if($user_type=="raw_milk"){
		
                $row_account_admin_id=getAcccountAdminIdAdminId($account_id,$DbConnection);
                $account_admin_id=$row_account_admin_id[0];		
                $parent_admin_id=getAccountIdByAdminId($account_admin_id,$DbConnection);		
		///new code assign till root////
		$final_plant_list = array();
		$final_plant_name_list=array();
		$raw_milk_account = assign_to_till_root($account_id);
		//echo "<br>Admin=".$parent_admin_id;
		if($raw_milk_account)
		{
			$conditionStr="";
			foreach($raw_milk_account as $rma)
			{
				$conditionStr=$conditionStr." user_account_id='$rma' OR ";
			}
			$conditionStr=substr($conditionStr,0,-3);		  
			
                        $dataCNS=getCustomerNoStationConditionStr($conditionStr,$DbConnection);
                        foreach($dataCNS as $row)
                        {
                            $final_plant_list[]=$row['$final_plant_list'];
			    $final_plant_name_list[]=$row['$final_plant_list'];
                        }
		}
		//print_r($final_plant_list);
		
		//-----------XXXXXX----------///
	}
	function assign_to_till_root($account_id_local1)
	{	
		global $DbConnection;	
		global $parent_account_ids;	 
		global $acc_size;		
		
                $row_account_admin_id=getAcccountAdminIdAdminId($account_id,$DbConnection);
                $admin_id=$row_account_admin_id[0];
		
                $function_account_id=getAccountIdByAdminId($admin_id,$DbConnection);
		
                $function_account_type=getUserTypeAccount($function_account_id,$DbConnection);
		
		if($function_account_type!='raw_milk')
		{
			$parent_account_ids[]=$function_account_id;
			//print_r($parent_account_ids);
			return $parent_account_ids;
		}		
		
		else
		{			
			$final_account_id=assign_to_till_root($function_account_id);
			//query to check non transporter from account table  usertype='raw_milk'
			////////
			$parent_account_ids[]=$function_account_id;
			//echo"acc1=".$function_account_id."<br>"."acc1=".$function_account_id."<br>"."acc1=".$parent_account_ids."<br>";				
			return $parent_account_ids;					
		}
		//return $account_id_local1;
	}
?>
	<fieldset class="manage_fieldset_invoice">
		<legend><strong>RAW MILK INVOICE REPORT</strong></legend>
		
		<?php
			//$root_create_id = 829;
			
			if($user_type=="plant_raw_milk")
			{
				//echo "<br>Plant";
				//*** Getting plant from plant_user_assignment*******//
				$result_plant=getDetailAllPUA($account_id,$DbConnection);
				$plant_in="";
				
                                foreach($result_plant as $row_plant)
                                {
                                    $plant_in.=" invoice_mdrm.plant=".$row_plant['plant_customer_no']." OR ";
                                }
				if($plant_in!=""){
					$plant_in = substr($plant_in, 0, -3);
				}
                                
                                if($all_data)
				{
					$condition="datebetweenonly_alldata";
					$orderA="";
					$user_type="plant_raw_milk";
					$result=getInvoiceMDRM($condition,$startdate,$enddate,$plant_in,$orderA,$user_type,$DbConnection);
				}
                                else
				{
					if($all_data_without_date)
					{
						$condition="invoicestatus_alldataNoDate";
						$orderA="1";
						$user_type="plant_raw_milk";
						$result=getInvoiceMDRM($condition,$startdate,$enddate,$plant_in,$orderA,$user_type,$DbConnection);
					}
					else if($targetdatewise)
					{
						if($targetplant=="0")//for all plant
						{						
							 $condition="invoicestatus_allplant_targetdate";
                                                        //$targetplant=$targetplant;
                                                        $user_type="plant_raw_milk";
                                                        $conditionStr=""; 
                                                        $result=getInvoiceMDRMTargetDate($condition,$startdate,$enddate,$conditionStr,$targetplant,$user_type,$DbConnection);
                                                        
						}
						else
						{	
                                                         $condition="invoicestatus_plant_targetdate";
                                                        //$targetplant=$targetplant;
                                                        $user_type="plant_raw_milk";
                                                        $conditionStr=""; 
                                                        $result=getInvoiceMDRMTargetDate($condition,$startdate,$enddate,$conditionStr,$targetplant,$user_type,$DbConnection);
                                                       
							//echo$query1;
						}
					}
					else
					{
						$condition="datebetween_invoicestatus";
						$orderA=$order;
						$user_type="plant_raw_milk";
						$result=getInvoiceMDRM($condition,$startdate,$enddate,$plant_in,$orderA,$user_type,$DbConnection); 
					}
                }
			}
			else if($user_type=="raw_milk")
			{
				//echo "<br>Admin";
				$Child_account="";
				include_once('tree_hierarchy_information.php');
				radio_group_account_hierarchy_transporter_child($root);
				//echo $Child_account;
				$child_account = $account_id.",".$Child_account;
				$Child_account_arr=explode(",",$Child_account);
				$chaSize=sizeof($Child_account_arr);
				$conditionStr="";
                                $self_child_transporter_id="";
				for($i=0;$i<$chaSize;$i++)			
				{
					$conditionStr=$conditionStr." invoice_mdrm.transporter_account_id='$Child_account_arr[$i]' OR ";
                                        $self_child_transporter_id.="'$Child_account_arr[$i]'".',';                                        
                                }
				//$conditionStr=$conditionStr." transporter_account_id='$account_id' OR ";
				$conditionStr = substr($conditionStr,0,-3);
				$self_child_transporter_id = substr($self_child_transporter_id,0,-1);
                                if($all_data)
				{
                                    $condition="datebetweenonly_alldata";
                                    $orderA="";
                                    $user_type="raw_milk";
                                    $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                                }
                                else
				{
                                    if($all_data_without_date)
                                    {
                                        $condition="invoicestatus_alldataNoDate";
                                        $orderA="1";
                                        $user_type="raw_milk";
                                        $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                                    }
                                    else
                                    {
                                        $condition="datebetween_invoicestatus";
                                        $orderA=$order;
                                        $user_type="raw_milk";
                                        $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                                    }
                                }
			}
                       
			else //admin
			{
				//echo "<br>User";
				
                            if($all_data)
                            {
                                    $condition="datebetweenonly_alldata";
                                    $orderA="";
                                    $user_type="admin";
                                    $conditionStr="";
                                    $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                            }
                            else
                            {
                               if($all_data_without_date)
                               {
                                       $condition="invoicestatus_alldataNoDate";
                                       $orderA="1";
                                       $user_type="admin";
                                       $conditionStr="";
                                       $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                               } 
                               else if($targetdatewise)
                                    {
                                            if($targetplant=="0")//for all plant
                                            {
                                               $condition="invoicestatus_allplant_targetdate";
                                               //$targetplant=$targetplant;
                                               $user_type="admin";
                                               $conditionStr=""; 
                                               $result=getInvoiceMDRMTargetDate($condition,$startdate,$enddate,$conditionStr,$targetplant,$user_type,$DbConnection);
                                          
                                            }
                                            else
                                            {
                                                $condition="invoicestatus_plant_targetdate";
                                               //$targetplant=$targetplant;
                                               $user_type="admin";
                                               $conditionStr=""; 
                                               $result=getInvoiceMDRMTargetDate($condition,$startdate,$enddate,$conditionStr,$targetplant,$user_type,$DbConnection);
                                            
                                            }
                                    }
                               else if($unloadaccepttime)
                                {
                                       $condition="unloadaccepttime";
                                       $orderA="2";
                                       $user_type="admin";
                                       $conditionStr="";
                                       $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                                }
                               else
                               {
                                       $condition="datebetween_invoicestatus";
                                       $orderA=$order;
                                       $user_type="admin";
                                       $conditionStr="";
                                       $result=getInvoiceMDRM($condition,$startdate,$enddate,$conditionStr,$orderA,$user_type,$DbConnection);
                               }
                            }
			}
			//echo $query;
			//$result = mysql_query($query,$DbConnection);			
		?>		
		<!--<div style="height:430px;overflow:auto;font-size:11px;" align="center">-->
                <div style="font-size:11px;">
                <form name="invoice_form" method = "post" target="_blank">
                <section class="">
                <div class="container">
		<!--<table style="border:thin;" class="manage_interface" cellspacing="2" cellpadding="2" rules="all" align="center" width=100%>-->
                    <table class="tablescroll">
			<thead>
			<?php
			if( $user_type=="plant_raw_milk"){
				/*echo "<tr style='background-color:silver;' rules='all'>
			<td>SNO</td><td>FIRST CREATE DATE</td><td>LORRY NO</td><td>VEHICLE NO</td><td>TANKER TYPE</td><td>DOCKET NO</td><td>EMAIL</td><td>TRANSPORTER MOBILE</td><td>QTY(KG)</td>
				<td>FAT(%)</td><td>SNF(%)</td><td>FAT(KG)</td><td>SNF(KG)</td><td>MANUAL MILK AGE(Hrs)</td><td>DISPATCH TIME</td><td>TARGET TIME</td><td>DRIVER NAME</td><td>DRIVER MOBILE</td><td>USERNAME(USERID)</td><td>STATUS</td><td>CLOSE</td><td><font color=blue>PLANT</font></td><td><font color=blue>CHILLING PLANT</font></td><td>UNLOAD EST.In MINS(GateEntry)</td><td>UNLOAD ACCEPT TIME</td><td>FAT%(FT)</td><td>SNF%(FT)</td><td>Qty(FT)</td><td>Temp.(FT)</td><td>Acidity(FT)</td><td>MBRT-min(FT)</td><td>RM(FT)</td><td>BR(FT)</td><td>Protien%(FT)</td><td>Sodium(FT)</td><td>Testing Status</td><td>FAT%(RT)</td><td>SNF%(RT)</td><td>ADULTRATION</td><td>OtherADULTRATION</td>
			</tr>";*/
			echo "<tr class='header' style='background-color:silver;' rules='all'>
			<th>SNO<div>SNO</div></th><th>FIRST CREATED (General)<div>FIRST CREATED (General)</div></th><th>TRACK EDITED (Transp.Last/PlantFirstClose)<div>TRACK EDITED (Transp.Last/PlantFirstClose)</div></th><th>LORRY NO<div>LORRY NO</div></th><th>VEHICLE NO<div>VEHICLE NO</div></th><th>TANKER TYPE<div>TANKER TYPE</div></th><th>DOCKET NO<div>DOCKET NO</div></th><th>EMAIL<div>EMAIL</div></th><th>TRANSPORTER MOBILE<div>TRANSPORTER MOBILE</div></th><th>QTY(KG)<div>QTY(KG)</div></th>
				<th>FAT(%)<div>FAT(%)</div></th><th>SNF(%)<div>SNF(%)</div></th><th>FAT(KG)<div>FAT(KG)</div></th><th>SNF(KG)<div>SNF(KG)</div></th><th>MANUAL MILK AGE(Hrs)<div>MANUAL MILK AGE(Hrs)</div></th><th>DISPATCH TIME<div>DISPATCH TIME</div></th><th>TARGET TIME<div>TARGET TIME</div></th><th>DRIVER NAME<div>DRIVER NAME</div></th><th>DRIVER MOBILE<div>DRIVER MOBILE</div></th><th>USERNAME(USERID)<div>USERNAME(USERID)</div></th><th>STATUS<div>STATUS</div></th><th>CLOSE<div>CLOSE</div></th><th>PLANT<div>PLANT</div></th><th>CHILLING PLANT<div>CHILLING PLANT</div></th><th>LECI No<div>LECI No</div></th><th>MATERIAL<div>MATERIAL</div></th><th>UNLOAD EST.In MINS(GateEntry)<div>UNLOAD EST.In MINS(GateEntry)</div></th><th>POSTING DATE/TIME<div>POSTING DATE/TIME</div></th><th>FAT%(FT)<div>FAT%(FT)</div></th><th>SNF%(FT)<div>SNF%(FT)</div></th><th>Qty(FT)<div>Qty(FT)</div></th><th>Temp.(FT)<div>Temp.(FT)</div></th><th>Acidity(FT)<div>Acidity(FT)</div></th><th>MBRT-min(FT)<div>MBRT-min(FT)</div></th><th>RM(FT)<div>RM(FT)</div></th><th>BR(FT)<div>BR(FT)</div></th><th>Protien%(FT)<div>Protien%(FT)</div></th><th>Sodium(FT)<div>Sodium(FT)</div></th><th>Testing Status<div>Testing Status</div></th><th>FAT%(RT)<div>FAT%(RT)</div></th><th>SNF%(RT)<div>SNF%(RT)</div></th><th>ADULTRATION<div>ADULTRATION</div></th><th>OtherADULTRATION<div>OtherADULTRATION</div></th>
			</tr>";
			}
			else if($user_type=="raw_milk"){
				/*			
				echo "<tr style='background-color:silver;' rules='all'>
			<td>SNO</td><td>FIRST CREATE DATE</td><td>LORRY NO</td><td>VEHICLE NO</td><td>TANKER TYPE</td><td>DOCKET NO</td><td>EMAIL</td><td>TRANSPORTER MOBILE</td><td>QTY(KG)</td>
				<td>FAT(%)</td><td>SNF(%)</td><td>FAT(KG)</td><td>SNF(KG)</td><td>MANUAL MILK AGE(Hrs)</td><td>DISPATCH TIME</td><td>TARGET TIME</td><td>DRIVER NAME</td><td>DRIVER MOBILE</td><td>USERNAME(USERID)</td><td>STATUS</td><td><font color=blue>PLANT</font></td><td><font color=blue>CHILLING PLANT</font></td><td>UNLOAD EST.In MINS(GateEntry)</td><td>UNLOAD ACCEPT TIME</td><td>FAT%(FT)</td><td>SNF%(FT)</td><td>Qty(FT)</td><td>Temp.(FT)</td><td>Acidity(FT)</td><td>MBRT-min(FT)</td><td>RM(FT)</td><td>BR(FT)</td><td>Protien%(FT)</td><td>Sodium(FT)</td><td>Testing Status</td><td>FAT%(RT)</td><td>SNF%(RT)</td><td>ADULTRATION</td><td>OtherADULTRATION</td><td>APPROVED TIME</td>
			</tr>";*/
			echo "<tr class='header' style='background-color:silver;' rules='all'>
			<th>SNO<div>SNO</div></th><th>FIRST CREATED (General)<div>FIRST CREATED (General)</div></th><th>TRACK EDITED (Transp.Last/PlantFirstClose)<div>TRACK EDITED (Transp.Last/PlantFirstClose)</div></th><th>LORRY NO<div>LORRY NO</div></th><th>VEHICLE NO<div>VEHICLE NO</div></th><th>TANKER TYPE<div>TANKER TYPE</div></th><th>DOCKET NO<div>DOCKET NO</div></th><th>EMAIL<div>EMAIL</div></th><th>TRANSPORTER MOBILE<div>TRANSPORTER MOBILE</div></th><th>QTY(KG)<div>QTY(KG)</div></th>
				<th>FAT(%)<div>FAT(%)</div></th><th>SNF(%)<div>SNF(%)</div></th><th>FAT(KG)<div>FAT(KG)</div></th><th>SNF(KG)<div>SNF(KG)</div></th><th>MANUAL MILK AGE(Hrs)<div>MANUAL MILK AGE(Hrs)</div></th><th>DISPATCH TIME<div>DISPATCH TIME</div></th><th>TARGET TIME<div>TARGET TIME</div></th><th>DRIVER NAME<div>DRIVER NAME</div></th><th>DRIVER MOBILE<div>DRIVER MOBILE</div></th><th>USERNAME(USERID)<div>USERNAME(USERID)</div></th><th>STATUS<div>STATUS</div></th><th>PLANT<div>PLANT</div></th><th>CHILLING PLANT<div>CHILLING PLANT</div></th><th>LECI No<div>LECI No</div></th><th>MATERIAL<div>MATERIAL</div></th><th>UNLOAD EST.In MINS(GateEntry)<div>UNLOAD EST.In MINS(GateEntry)</div></th><th>POSTING DATE/TIME<div>POSTING DATE/TIME</div></th><th>FAT%(FT)<div>FAT%(FT)</div></th><th>SNF%(FT)<div>SNF%(FT)</div></th><th>Qty(FT)<div>Qty(FT)</div></th><th>Temp.(FT)<div>Temp.(FT)</div></th><th>Acidity(FT)<div>Acidity(FT)</div></th><th>MBRT-min(FT)<div>MBRT-min(FT)</div></th><th>RM(FT)<div>RM(FT)</div></th><th>BR(FT)<div>BR(FT)</div></th><th>Protien%(FT)<div>Protien%(FT)</div></th><th>Sodium(FT)<div>Sodium(FT)</div></th><th>Testing Status<div>Testing Status</div></th><th>FAT%(RT)<div>FAT%(RT)</div></th><th>SNF%(RT)<div>SNF%(RT)</div></th><th>ADULTRATION<div>ADULTRATION</div></th><th>OtherADULTRATION<div>OtherADULTRATION</div></th><th>APPROVED TIME<div>APPROVED TIME</div></th>
			</tr>";
			}
			else{ //admin
			/*echo "<tr style='background-color:silver;' rules='all'>
			<td>SNO</td><td>FIRST CREATE DATE</td><td>LORRY NO</td><td>VEHICLE NO</td><td>TANKER TYPE</td><td>DOCKET NO</td><td>EMAIL</td><td>TRANSPORTER MOBILE</td><td>QTY(KG)</td>
				<td>FAT(%)</td><td>SNF(%)</td><td>FAT(KG)</td><td>SNF(KG)</td><td>MANUAL MILK AGE(Hrs)</td><td>DISPATCH TIME</td><td>TARGET TIME</td><td>DRIVER NAME</td><td>DRIVER MOBILE</td><td>USERNAME(USERID)</td><td>STATUS</td><td>CLOSE</td><td><font color=red>CANCEL</font></td><td><font color=blue>PLANT</font></td><td><font color=blue>CHILLING PLANT</font></td><td>UNLOAD EST.In MINS(GateEntry)</td><td>UNLOAD ACCEPT TIME</td><td>FAT%(FT)</td><td>SNF%(FT)</td><td>Qty(FT)</td><td>Temp.(FT)</td><td>Acidity(FT)</td><td>MBRT-min(FT)</td><td>RM(FT)</td><td>BR(FT)</td><td>Protien%(FT)</td><td>Sodium(FT)</td><td>Testing Status</td><td>FAT%(RT)</td><td>SNF%(RT)</td><td>ADULTRATION</td><td>OtherADULTRATION</td><td>APPROVED</td><td>APPROVED TIME</td>
			</tr>";*/
			echo "<tr class='header' style='background-color:silver;' rules='all'>
			<th>SNO<div>SNO</div></th><th>FIRST CREATED (General)<div>FIRST CREATED (General)</div></th><th>TRACK EDITED (Transp.Last/PlantFirstClose)<div>TRACK EDITED (Transp.Last/PlantFirstClose)</div></th><th>LORRY NO<div>LORRY NO</div></th><th>VEHICLE NO<div>VEHICLE NO</div></th><th>TANKER TYPE<div>TANKER TYPE</div></th><th>DOCKET NO<div>DOCKET NO</div></th><th>EMAIL<div>EMAIL</div></th><th>TRANSPORTER MOBILE<div>TRANSPORTER MOBILE</div></th><th>QTY(KG)<div>QTY(KG)</div></th>
				<th>FAT(%)<div>FAT(%)</div></th><th>SNF(%)<div>SNF(%)</div></th><th>FAT(KG)<div>FAT(KG)</div></th><th>SNF(KG)<div>SNF(KG)</div></th><th>MANUAL MILK AGE(Hrs)<div>MANUAL MILK AGE(Hrs)</div></th><th>DISPATCH TIME<div>DISPATCH TIME</div></th><th>TARGET TIME<div>TARGET TIME</div></th><th>DRIVER NAME<div>DRIVER NAME</div></th><th>DRIVER MOBILE<div>DRIVER MOBILE</div></th><th>USERNAME(USERID)<div>USERNAME(USERID)</div></th><th>STATUS<div>STATUS</div></th><th>CLOSE<div>CLOSE</div></th><th>CANCEL<div>CANCEL</div></th><th>PLANT<div>PLANT</div></th><th>CHILLING PLANT<div>CHILLING PLANT</div></th><th>LECI No<div>LECI No</div></th><th>MATERIAL<div>MATERIAL</div></th><th>UNLOAD EST.In MINS(GateEntry)<div>UNLOAD EST.In MINS(GateEntry)</div></th><th>POSTING DATE/TIME<div>POSTING DATE/TIME</div></th><th>FAT%(FT)<div>FAT%(FT)</div></th><th>SNF%(FT)<div>SNF%(FT)</div></th><th>Qty(FT)<div>Qty(FT)</div></th><th>Temp.(FT)<div>Temp.(FT)</div></th><th>Acidity(FT)<div>Acidity(FT)</div></th><th>MBRT-min(FT)<div>MBRT-min(FT)</div></th><th>RM(FT)<div>RM(FT)</div></th><th>BR(FT)<div>BR(FT)</div></th><th>Protien%(FT)<div>Protien%(FT)</div></th><th>Sodium(FT)<div>Sodium(FT)</div></th><th>Testing Status<div>Testing Status</div></th><th>FAT%(RT)<div>FAT%(RT)</div></th><th>SNF%(RT)<div>SNF%(RT)</div></th><th>ADULTRATION<div>ADULTRATION</div></th><th>OtherADULTRATION<div>OtherADULTRATION</div></th><th>APPROVED<div>APPROVED</div></th><th>APPROVED TIME<div>APPROVED TIME</div></th>
			</tr>"; 
			}
			
			echo"</thead><tbody class='scrollContent'>";
			
			$sno_local =1;
			$color=1;
			
			$csv_string = "";
			$title= "RAW MILK INVOICE REPORT";
			$csv_string = $csv_string.$title."\n";
			if( $user_type=="plant_raw_milk"){
				$csv_string = $csv_string."SNO,FIRST CREATE DATE,LORRY NO,VEHICLE NO,TANKER TYPE,DOCKET NO,EMAIL,TRANSPORTER MOBILE,QTY,FAT(%),SNF(%),FAT(KG),SNF(KG),MANUAL MILK AGE(Hrs),DISPATCH TIME,TARGET TIME,DRIVER NAME,DRIVER MOBILE,USERNAME(USERID),STATUS, PLANT,LECINO,CHILLING PLANT,MATERIAL,UNLOAD ESTIMATED TIME,GATE ENTRY,POSTING DATE/TIME,FAT%(FT),SNF%(FT),Qty(FT),Temp.(FT),Acidity(FT),MBRT-min(FT),RM(FT),BR(FT),Protien%(FT),Sodium(FT),Testing Status,FAT%(RT),SNF%(RT),ADULTRATION,OtherADULTRATION,CLOSE TIME\n";
			}
			else if( $user_type=="raw_milk"){
				$csv_string = $csv_string."SNO,FIRST CREATE DATE,LORRY NO,VEHICLE NO,TANKER TYPE,DOCKET NO,EMAIL,TRANSPORTER MOBILE,QTY,FAT(%),SNF(%),FAT(KG),SNF(KG),MANUAL MILK AGE(Hrs),DISPATCH TIME,TARGET TIME,DRIVER NAME,DRIVER MOBILE,USERNAME(USERID),STATUS, PLANT,LECINO,CHILLING PLANT,MATERIAL,APPROVED TIME,UNLOAD ESTIMATED TIME,GATE ENTRY,POSTING DATE/TIME,FAT%(FT),SNF%(FT),Qty(FT),Temp.(FT),Acidity(FT),MBRT-min(FT),RM(FT),BR(FT),Protien%(FT),Sodium(FT),Testing Status,FAT%(RT),SNF%(RT),ADULTRATION,OtherADULTRATION \n";
			}
			else{
				$csv_string = $csv_string."SNO,FIRST CREATE DATE,LORRY NO,VEHICLE NO,TANKER TYPE,DOCKET NO,EMAIL,TRANSPORTER MOBILE,QTY,FAT(%),SNF(%),FAT(KG),SNF(KG),MANUAL MILK AGE(Hrs),DISPATCH TIME,TARGET TIME,DRIVER NAME,DRIVER MOBILE,USERNAME(USERID),STATUS, PLANT,LECINO,CHILLING PLANT,MATERIAL,APPROVED TIME,UNLOAD ESTIMATED TIME,GATE ENTRY,POSTING DATE/TIME,FAT%(FT),SNF%(FT),Qty(FT),Temp.(FT),Acidity(FT),MBRT-min(FT),RM(FT),BR(FT),Protien%(FT),Sodium(FT),Testing Status,FAT%(RT),SNF%(RT),ADULTRATION,OtherADULTRATION,CLOSE TIME\n";
			
			}
			
			echo"<input TYPE=\"hidden\" VALUE=\"$title\" NAME=\"title\">";
			$i=0;
			//while($row_select = mysql_fetch_object($result))
                      
                        //print_r($result);
                        
            foreach($result as $row_select)
			{
				
				$user_id = $row_select['uid'];
				$user_name = $row_select['nme'];
				
				$sno = $row_select['sno'];				
				$status="";
				$status = $row_select['invoice_status'];
				//echo "<br>status=".$status;
                         
				if($status==1)
				{
					$status = "Open";
					$status_download = "Open";
				}
				
				
				else if($status==2)
				{
					//$status = "<font color=brown>Closed</font>";
					if($user_type=="raw_milk")
					{
						$status = "<font color=brown> Closed</font>";
					}
					else
					{
						//echo $date;
						$current_date2=strtotime(date('Y-m-d H:i:s'));
						//$validity_edit_date=($current_date2 - (24*60));
						//echo strtotime($row_select->unload_accept_time)  ."=". $validity_edit_date."<br>";
						if($user_type=="plant_raw_milk")
						{
							$validity_edit_date=($current_date2 - (24*60*60));
							if((strtotime($row_select['unload_accept_time'])<= $current_date2) && (strtotime($row_select['unload_accept_time']) >= $validity_edit_date) )
							{
								$status = "<font color=brown><a href='#' onclick=setclosetimeedit($sno) > Closed</a></font>";
							}
							else
							{
								$status = "<font color=brown> Closed</font>";
							}
						}
						else
						{
							$validity_edit_date=($current_date2 - (48*60*60));
							//echo strtotime($row_select->unload_accept_time)  ."=". $validity_edit_date."=". $current_date2."<br>";
							if((strtotime($row_select['unload_accept_time'])<= $current_date2) && (strtotime($row_select['unload_accept_time']) >= $validity_edit_date) )
							{
								$status = "<font color=brown><a href='#' onclick=setclosetimeedit($sno) > Closed</a></font>";
							}
							else
							{
								$status = "<font color=brown> Closed</font>";
							}
						}
						
					}
					
					$status_download = "Closed";
				}
				else if($status==0)
				{
					$status = "<font color=red>Cancelled</font><br>CR:".$row_select['create_date']." ";
					$status_download = "Cancelled";
				}
                      	else if($status==5)
				{
                                    $user_Tid = getAccountAndDetail($row_select['transporter_account_id'],$DbConnection);
                                    //echo $user_Tid;
                                    if( ($user_type!="raw_milk") && ($user_type!="plant_raw_milk" ))
                                    {
                                            $status = "<a href='src/php/manage_invoice_milk_add_upload.php?pending=1&tid_p=".$row_select['transporter_account_id']."&sno_p=".$row_select['sno']." ;' class='hs2' target='_blank'><font color=red>Pending to<br> $user_Tid </font></a><br>CR:".$row_select['create_date']." ";
                                    }
                                    else
                                    {
                                            $status = "<font color=red>Pending to<br> $user_Tid </font>";
                                    }

                                    $status_download = "Pending to $user_Tid  ";
				}
                               				
                                $row_createuser= GetAccountInfo($row_select['create_id'],"1",$DbConnection);
                                $create_user_type = $row_createuser[0];
                                //echo $create_user_type;					
				if((float)$row_select['qty_kg']==0.0)
				{
						echo "<tr style='background-color:#FA5858'>";
				}	
				else if($create_user_type=='raw_milk')
				{
						echo "<tr style='background-color:#F5D0A9'>";
				}
				else if($create_user_type=='plant_raw_milk')
				{
						echo "<tr style='background-color:#81DAF5'>";
				}
				else//admin
				{
					echo "<tr>";
				}
				
				$plant_acceptance_time=$row_select['plant_acceptance_time'];
				//$closetime=$row_select->close_time;
				$closetime=$row_select['system_time'];
				$closetype1=$row_select['close_type'];
				$closetype="";
				if($row_select['invoice_status']==2)//set close type status 
				{
					if($closetype1=='a'){
					$closetype="<font color=brown>(AutoClose)</font>";
						$status_download=$status_download;
					}
					if($closetype1=='g'){
						$closetype="<font color=brown>(GPRSClose)</font>";
						$status_download=$status_download;
					}
					if($closetype1=='m'){
						$closetype="<font color=brown>(ManualClose)</font>";
						$status_download=$status_download;
					}
					//for old version
					if($closetype==""){
						if($row_select['unload_accept_time']!="")
						{
							$closetype="<font color=brown>(ManualClose)</font>";
							$status_download=$status_download;
						}
						else
						{
							$closetype="<font color=brown>(AutoClose)</font>";
							$status_download=$status_download;
						}
					}
				}
				
				
				//milk age calculation //
				$cdatetime1 = strtotime(date('00:00:00'));
				$cdatetime2 = strtotime(date('H:i:s'));
				$current_difftime = $cdatetime2 - $cdatetime1;
				$current_time_hrs=$current_difftime/3600;
				
				$current_date1=strtotime(date('Y-m-d H:i:s'));
			
				//case 1 when not close:
				$manual_milk_hr=0;
				if( $row_select['invoice_status'] == 1)
				{
					$milk_hr1=$row_select['milk_age']; //in hrs
					
					$disp_time1=$row_select['dispatch_time']; //in date time
					$disp_time2 = strtotime($disp_time1);
					//$disp_time_hrs= ($current_date1-$disp_time2)/3600;
					if($current_date1<$disp_time2)
					{
						$manual_milk_hr="N/A(current < dispatch)";
					}
					else
					{
						$manual_milk_hr= $milk_hr1 + ($current_date1-$disp_time2)/3600;
						//$manual_milk_hr= ($milk_hr1 + $current_time_hrs)-$disp_time_hrs;
						$manual_milk_hr=round($manual_milk_hr,2);
					}
					
				}
				//case 2 when close:
				if( $row_select['invoice_status'] == 2 && $row_select['unload_estimated_time']!="" && $row_select['unload_accept_time']!="")
				{
					$milk_hr1=$row_select['milk_age']; //in hrs
					
					$disp_time1=$row_select['dispatch_time']; // in date time
					$disp_time2 = strtotime($disp_time1);
					//$disp_time_hrs= ($current_date1-$disp_time2)/3600;
					
					$unload_estimate_min=$row_select['unload_estimated_time']; //in minutes
					$unload_estimate_hrs=$unload_estimate_min/60;
					
					$unload_accept_datetime1=$row_select['unload_accept_time']; //in date time
					$unload_accept_datetime2 = strtotime($unload_accept_datetime1);
					//$unload_accept_hrs= ($current_date1-$unload_accept_datetime2)/3600;
					
					if($unload_accept_datetime2<$disp_time2)
					{
					
						$manual_milk_hr="N/A(accept < dispatch)";
					}
					else
					{
						$manual_milk_hr= ($milk_hr1 + ($unload_accept_datetime2  - $disp_time2)/3600) - ($unload_estimate_hrs);
						//$manual_milk_hr= (($milk_hr1 + $unload_accept_hrs ) - ($disp_time_hrs)) - ($unload_estimate_hrs);
						//echo $milk_hr1 ."+". $unload_accept_hrs ." - ".$disp_time_hrs." - ".$unload_estimate_hrs."<br>";
						$manual_milk_hr=round($manual_milk_hr,2);
					}
					
				}
				// pending 
				if( $row_select['invoice_status'] == 5)
				{
					$milk_hr1=$row_select['milk_age']; //in hrs
					
					$disp_time1=$row_select['dispatch_time']; //in date time
					$disp_time2 = strtotime($disp_time1);
					//$disp_time_hrs= ($current_date1-$disp_time2)/3600;
					if($current_date1<$disp_time2)
					{
						$manual_milk_hr="N/A(current < dispatch)";
					}
					else
					{
						$manual_milk_hr= $milk_hr1 + ($current_date1-$disp_time2)/3600;
						//$manual_milk_hr= ($milk_hr1 + $current_time_hrs)-$disp_time_hrs;
						$manual_milk_hr=round($manual_milk_hr,2);
					}
					
				}
				
				//--------end milk age calculation-------------//
				
				echo "<td>".$sno_local."</td>";
				//echo"<td>".$row_select['create_date']."<br>LastEdited:".$row_select['edit_date']."</td>";
				 if($row_select['invoice_status']!= 5)
                                {
                                   echo"<td>".$row_select['create_date']."<br>LastEdited:".$row_select['edit_date']."</td>";
                                }
                                else
                                {
                                    echo"<td>".$row_select['create_date']."</td>";
                                }
                                echo "<td>".$row_select['transporter_editdate']."<br>/&nbsp;".$row_select['plant_editdate']."</td>";
                                 //for lorry
                                //echo"<td>".$row_select['lorry_no']."</td>";
                                echo '<input type="hidden" id="lorry_cnt_'.$sno_local.'" name="lorry_cnt_'.$sno_local.'" value="'.$row_select['lorry_no'].'">';
                                echo '<input type="hidden" id="lorry_'.$sno.'" name="lorry_'.$sno.'" value="'.$row_select['lorry_no'].'">';
				echo '<input type="hidden" id="lorry_pre_'.$sno.'" name="lorry_pre_'.$sno.'" value="'.$row_select['lorry_no'].'">'	;				
					
                                 if($row_select['invoice_status']== 1 && $user_type!="plant_raw_milk" && $row_select['unload_estimated_datetime']=="")
                                 {
                                     echo '<td><a href="javascript:show_lorry_pre('.$sno.')"><div id="label_lorry_'.$sno.'">'.$row_select['lorry_no'].'</div></a></td>';
                                 }
                                 else
                                 {
                                     echo"<td>".$row_select['lorry_no']."</td>";
                                 }
				
                                echo '<input type="hidden" id="vehicle_no_cnt_'.$sno_local.'" name="vehicle_no_cnt_'.$sno_local.'" value="'.$row_select['vehicle_no'].'">';
				echo '<input type="hidden" id="vehicle_no_'.$sno.'" name="vehicle_no_'.$sno.'" value="'.$row_select['vehicle_no'].'">';
				echo '<input type="hidden" id="vehicle_no_pre_'.$sno.'" name="vehicle_no_pre_'.$sno.'" value="'.$row_select['vehicle_no'].'">'	;				
				
                                //if( $user_type=="plant_raw_milk" && $row_select['invoice_status']==1)
                                if( $user_type!="raw_milk" && $row_select['invoice_status']==1)
                                {
                                        $dispatch_time_tmp= str_replace(":", "-", $row_select['dispatch_time']);
                                        $target_time_tmp= str_replace(":", "-", $row_select['target_time']);
                                        $vehicle_live_info_set=$row_select['vehicle_no']."/".$dispatch_time_tmp."/".$target_time_tmp."/".$row_select['plant'];
                                        //echo'<td><input type="checkbox" id="vehicle_status_'.$sno.'" name="vehicle_status_serial[]" value='.$row_select->vehicle_no.'>';
                                        echo'<td><input type="checkbox" id="vehicle_status_'.$sno.'" name="vehicle_status_serial[]" value="'.$vehicle_live_info_set.'">';
                                }
                               else
                                {
                                        echo'<td><div>';
                                }
                                //if($row_select['invoice_status']== 1 && $user_type!="plant_raw_milk" && $user_type!="raw_milk" && $row_select['unload_estimated_datetime']=="")
                                if($row_select['invoice_status']== 1 && $user_type!="plant_raw_milk" && $row_select['unload_estimated_datetime']=="")
                                {                                    
                                    echo '<a href="javascript:show_vehicle_no_pre('.$sno.')"><div id="label_vehicle_no_'.$sno.'">'.$row_select['vehicle_no'].'</div></a></div></td>';
                                }
                                else
                                {
                                  echo
                                  $row_select['vehicle_no']."</div></td>";  
                                }
				
                                echo"  
				<!--<td>".$row_select->vehicle_no."</td>-->
				<td>".$row_select['tanker_type']."</td>
				<td><font color=red>".$row_select['docket_no']."</font></td>
				<td>".$row_select['email']."</td>				
                                <td>".$row_select['mobile']."</td>
                                   ";
                                
                               
				echo '<input type="hidden" id="qty_kg_'.$sno.'" name="qty_kg_'.$sno.'" value="'.$row_select['qty_kg'].'">';
				echo '<input type="hidden" id="qty_kg_pre_'.$sno.'" name="qty_kg_pre_'.$sno.'" value="'.$row_select['qty_kg'].'">'	;				
				
                                if($row_select['invoice_status']== 1 && $user_type!="plant_raw_milk" && $row_select['unload_estimated_datetime']=="")
                                 {
                                     echo '<td><a href="javascript:show_qtykg_pre('.$sno.','.$row_select['fat_percentage'].','.$row_select['snf_percentage'].','.$row_select['fat_kg'].','.$row_select['snf_kg'].')"><div id="label_qtykg_'.$sno.'">'.$row_select['qty_kg'].'</div></a></td>';
                                 }
                                 else
                                 {
                                     echo"<td>".$row_select['qty_kg']."</td>";
                                 }
                                 
                                echo '<input type="hidden" id="fat_per_'.$sno.'" name="fat_per_'.$sno.'" value="'.$row_select['fat_percentage'].'">';
				echo '<input type="hidden" id="fat_per_pre_'.$sno.'" name="fat_per_pre_'.$sno.'" value="'.$row_select['fat_percentage'].'">'	;				
				
                                echo '<input type="hidden" id="snf_per_'.$sno.'" name="snf_per_'.$sno.'" value="'.$row_select['snf_percentage'].'">';
				echo '<input type="hidden" id="snf_per_pre_'.$sno.'" name="snf_per_pre_'.$sno.'" value="'.$row_select['snf_percentage'].'">'	;				
				
                                echo '<input type="hidden" id="fat_kg_'.$sno.'" name="fat_kg_'.$sno.'" value="'.$row_select['fat_kg'].'">';
				echo '<input type="hidden" id="fat_kg_pre_'.$sno.'" name="fat_kg_pre_'.$sno.'" value="'.$row_select['fat_kg'].'">'	;				
				
                                echo '<input type="hidden" id="snf_kg_'.$sno.'" name="snf_kg_'.$sno.'" value="'.$row_select['snf_kg'].'">';
				echo '<input type="hidden" id="snf_kg_pre_'.$sno.'" name="snf_kg_pre_'.$sno.'" value="'.$row_select['snf_kg'].'">'	;				
				
				//<td>".$row_select['qty_kg']."</td>
                                 echo"
				<td><div id=label_fat_per_$sno>".$row_select['fat_percentage']."</div></td>
				<td><div id=label_snf_per_$sno>".$row_select['snf_percentage']."</div></td>
				<td><div id=label_fat_kg_$sno>".$row_select['fat_kg']."</div></td>
				<td><div id=label_snf_kg_$sno>".$row_select['snf_kg']."</div></td>
				<!--<td>".$milk_hr1."-".$manual_milk_hr."</td>-->
				<td>".$manual_milk_hr."</td>
				<!--<td>".$row_select['milk_age']."</td>-->
				<td>".$row_select['dispatch_time']."</td>
				<td>".$row_select['target_time']."</td>
				<td>".$row_select['driver_name']."</td>
				<td>".$row_select['driver_mobile']."</td>";					
				//echo "<td>".$user_id."</td>	
				
				echo "<td>".$user_name."(".$user_id.")"."</td>	
				
				";
					//1 => OPEN
					//2 => CLOSED
					//0 => CANCELLED
					$close_invoice_flag = 0;
					$validity_time_tmp = $row_select['validity_time'];
					
					echo"<td><font color=green>".$status."</font></td>";
							
					
                                        if($row_select['invoice_status'] == 1)
					{		
						
						if( $user_type!="raw_milk"){
							echo '<td align=right><input type="checkbox" name="invoice_serial_close[]" value="'.$sno.'" id="close_chk_'.$sno.'" onclick=setclosetime('.$sno.') > <input type="hidden" id="closetime_'.$sno.'" name="closetime'.$sno.'" ></td>
							<input type="hidden" name="edit_close_chk[]" value="0" id="edit_close_chk_'.$sno.'"  >
							';	
						}
						else{
							echo'<input type="hidden" name="invoice_serial_close[]" value="'.$sno.'" id="close_chk_'.$sno.'"  >';
							echo'<input type="hidden" name="edit_close_chk[]" value="0" id="edit_close_chk_'.$sno.'"  >';
						}
						
						//if( $user_type=="plant_raw_milk"){
						if( $user_type=="plant_raw_milk" || $user_type=="raw_milk"){
							echo '<input type="hidden" name="invoice_serial_cancel[]" value="'.$sno.'">';
						}
						else{
							echo '<td align=right><input type="checkbox" name="invoice_serial_cancel[]" value="'.$sno.'"></td>';
						}
						
					}
					else{
						if($row_select['invoice_status'] == 2)
						{
							if( $user_type!="raw_milk"){
								echo '<td align=right>'.$closetime.'</td>';	
							}
							//if( $user_type=="plant_raw_milk"){
							if( $user_type=="plant_raw_milk" || $user_type=="raw_milk"){
								echo '<input type="hidden" name="invoice_serial_cancel[]" value="'.$sno.'">';
							}
							else{
								echo '<td align=right><input type="checkbox" name="invoice_serial_cancel[]" value="'.$sno.'"></td>';
							}
							
							echo '<input type="hidden" name="invoice_serial_close[]" value="'.$sno.'"/>
									<input type="hidden" name="edit_close_chk[]" value="0" id="edit_close_chk_'.$sno.'"  >
								<input type="hidden" id="closetime_'.$sno.'" name="closetime'.$sno.'" >
							';
						}
						if($row_select['invoice_status'] == 0)
						{
							if( $user_type!="raw_milk" && $user_type!="plant_raw_milk"){
								echo '<td align=right>'.$closetime.'</td>';
							}
							echo '<td align=right>&nbsp;</td>';	
							echo '<input type="hidden" name="invoice_serial_close[]" value="'.$sno.'"/>
								<input type="hidden" name="edit_close_chk[]" value="0" id="edit_close_chk_'.$sno.'"  >
								<input type="hidden" id="closetime_'.$sno.'" name="closetime'.$sno.'" >
							';
							echo '<input type="hidden" name="invoice_serial_cancel[]" value="'.$sno.'"/>';						

						}
						if($row_select['invoice_status'] == 5)
						{
							if( $user_type!="raw_milk" && $user_type!="plant_raw_milk"){
								echo '<td align=right>'.$closetime.'</td>';
							}
							else
							{
								echo '<td align=right>&nbsp;</td>';
							}
							
							
                                                        if( $user_type!="raw_milk" && $user_type!="plant_raw_milk")
                                                        {
                                                                echo '<td><input type="checkbox" name="invoice_serial_cancel[]" value="'.$sno.'"></td>';
                                                        }
                                                        else{
                                                                echo '<input type="hidden" name="invoice_serial_cancel[]" value="'.$sno.'"/>';	
                                                        }
							
							
							echo '<input type="hidden" name="invoice_serial_close[]" value="'.$sno.'"/>
							<input type="hidden" name="edit_close_chk[]" value="0" id="edit_close_chk_'.$sno.'"  >
								<input type="hidden" id="closetime_'.$sno.'" name="closetime'.$sno.'" >
							';
							//echo '<input type="hidden" name="invoice_serial_cancel[]" value="'.$sno.'"/>';						

						}
					}										
					
					echo '<td>
					<input type="hidden" id="plant_'.$sno.'" name="plant_'.$sno.'" value="'.$row_select['plant'].'">
					<input type="hidden" id="plant_pre_'.$sno.'" name="plant_pre_'.$sno.'" value="'.$row_select['plant'].'">'					
					;
					
					//echo $row_select->invoice_status."<br>";
					if($row_select['plant']!="") //plant exist
					{
						//if($row_select['invoice_status'] == 1 || $user_type=="raw_milk")
                                                if($row_select['invoice_status'] == 1 )
						{
							/*if($user_type=="plant_raw_milk")
							{
								if($plant_acceptance_time==""){
									echo '<div id="label_'.$sno.'"><font color=red>'.$row_select['plant'].'</div></font>';
								}
								else{
									echo '<div id="label_'.$sno.'"><font color=green>'.$row_select['plant'].'</div></font>';
								}
								
							}*/
                                                        
                                                        if($user_type=="plant_raw_milk" || $user_type=="raw_milk")
							{
								if($plant_acceptance_time==""){
									echo '<div id="label_'.$sno.'"><font color=red>'.$row_select['plant'].'</div></font>';
								}
								else{
									echo '<div id="label_'.$sno.'"><font color=green>'.$row_select['plant'].'</div></font>';
								}
								
							}
							
							else
							{
								if($plant_acceptance_time==""){
									echo '<a href="javascript:show_plant_list_pre('.$sno.','.$row_select['plant'].')"><div id="label_'.$sno.'"><font color=red>'.$row_select['plant'].'</font></div></a>';
								}
								else{
									echo '<a href="javascript:show_plant_list_pre('.$sno.','.$row_select['plant'].')"><div id="label_'.$sno.'"><font color=green>'.$row_select['plant'].'</font></div></a>';
								}
								
							}
						}
						else
						{
							if($plant_acceptance_time==""){
								echo '<div><font color=red>'.$row_select['plant'].'</font></div>';
							}
							else{
								echo '<div><font color=green>'.$row_select['plant'].'</font></div>';
							}
							
						}						
					}
					else //does not exist
					{
						if($row_select['invoice_status'] == 0 || $row_select['invoice_status'] == 2)
						{
							echo '<div id="label_'.$sno.'"></div>';
						}
						else
						{																		
							if($user_type=="raw_milk" || $user_type=="plant_raw_milk")
							{
								echo '<div id="label_'.$sno.'"></div>';
							}
							else
							{
								echo '<a href="javascript:show_plant_list('.$sno.')"><div id="label_'.$sno.'">Assign</div></a>';
							}
						}
					}				
					echo '</td>'
					;
					echo "<td>".$row_select['chilling_plant']."</td>";
                                        echo "<td>".$row_select['lecino']."</td>";
					///*********************************** invoice_material *************************************////
					if($row_select['invoice_material']=="") //	invoice_material (empty)
					{
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{	
							echo '<td align=right>
								 <input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" value="'.$row_select['invoice_material'].'"   readonly />
									
								</td>';	
						}
						
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{							
							echo'<td align=right><font color=red>X</font>
								<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" />								
							</td>';
						}
						else{
							echo'<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'"  />';
							
						}
					}
					else
					{
						
						if($row_select['invoice_status'] == 1)
						{
							
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
										<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" value="'.$row_select['invoice_material'].'"  readonly />										
										<font color=green>'.$row_select['invoice_material'].'</font>
								</td>';	
									
							}
							
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font>
								<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" value="'.$row_select['invoice_material'].'" />
								
								</td>';
							}
							else{
								echo'<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" value="'.$row_select['invoice_material'].'" />';
								
							}
						}
						else
						{
							//if($user_type!="raw_milk") //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['invoice_material'].'</font></td>';		
							}
							echo '<input type="hidden" name="invoice_material[]" id="invoice_material_'.$sno.'" value="'.$row_select['invoice_material'].'"  />';		
							
						}
						
					}
					///*********************************** unload estimated time time *************************************////
					if($row_select['unload_estimated_time']=="") //	unload_estimated_time (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['unload_estimated_datetime']!="")
						{
							$uedt='('.$row_select['unload_estimated_datetime'].')';
						}
						else
						{
							$uedt="";
						}
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{	
							echo '<td align=right>
									<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" value="'.$row_select['unload_estimated_time'].'"   readonly />
									<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'" value="'.$row_select['unload_estimated_datetime'].'"  readonly />
									<font color=green>-'.$uedt.'</font>
								</td>';	
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{							
							echo'<td align=right><font color=red>X</font>
								<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" />
								<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'"  />
							</td>';
						}
						else{
							echo'<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'"  />';
							echo'<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'"  />';
						}
					}
					else
					{
						if($row_select['unload_estimated_datetime']=="")
						{
							$unload_estimated_datetime="-";
						}
						else
						{
							$unload_estimated_datetime=$row_select['unload_estimated_datetime'];
						}
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
										<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" value="'.$row_select['unload_estimated_time'].'"  readonly />
										<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'" value="'.$row_select['unload_estimated_datetime'].'"  readonly />
										<font color=green>'.$row_select['unload_estimated_time'].'('.$unload_estimated_datetime.')</font>
								</td>';	
									
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font>
								<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" value="'.$row_select['unload_estimated_time'].'" />
								<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'" value="'.$row_select['unload_estimated_datetime'].'" />
								</td>';
							}
							else{
								echo'<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" value="'.$row_select['unload_estimated_time'].'" />';
								echo'<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'" value="'.$row_select['unload_estimated_datetime'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk") //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['unload_estimated_time'].'('.$unload_estimated_datetime.')</font></td>';		
							}
							echo '<input type="hidden" name="unload_estimated_time[]" id="unload_estimated_time_'.$sno.'" value="'.$row_select['unload_estimated_time'].'"  placeholder="In Minutes" />';		
							echo '<input type="hidden" name="unload_estimated_datetime[]" id="unload_estimated_datetime_'.$sno.'" value="'.$row_select['unload_estimated_datetime'].'"  placeholder="In Minutes" />';														
						}
						
					}
					///*********************************** unload_accept_time time *************************************////
					if($row_select['unload_accept_time']=="") //	unload_accept_time (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" value=""  placeholder="Date Time" readonly  />
							<font color=green>'.$row_select['unload_accept_time'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" value="'.$row_select['unload_accept_time'].'"  placeholder="Date Time" readonly  />
								<font color=green>'.$row_select['unload_accept_time'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" value="'.$row_select['unload_accept_time'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" value="'.$row_select['unload_accept_time'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin							
							{
								echo '<td align=right><font color=green>'.$row_select['unload_accept_time'].'</font></td>';	
							}
							echo '<input type="hidden" name="unload_accept_time[]" id="unload_accept_time_'.$sno.'" value="'.$row_select['unload_accept_time'].'"  />';										
				
						}
					}
					///*********************************** fat percentage First testing *************************************////
					if($row_select['fat_per_ft']=="") //	fat_per_ft (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" value=""  placeholder="FAT % FT" readonly  />
							<font color=green>'.$row_select['fat_per_ft'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" value="'.$row_select['fat_per_ft'].'"  placeholder="FAT % FT" readonly  />
								<font color=green>'.$row_select['fat_per_ft'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" value="'.$row_select['fat_per_ft'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" value="'.$row_select['fat_per_ft'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['fat_per_ft'].'</font></td>';	
							}
							echo '<input type="hidden" name="fat_per_ft[]" id="fat_per_ft_'.$sno.'" value="'.$row_select['fat_per_ft'].'"  />';										
				
						}
					}
					///*********************************** snf percentage First testing *************************************////
					if($row_select['snf_per_ft']=="") //	snf_per_ft (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" value=""  placeholder="SNF % FT" readonly  />
							<font color=green>'.$row_select['snf_per_ft'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="snf_per_ft[]" id="fat_per_ft_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" value="'.$row_select['snf_per_ft'].'"  placeholder="SNF % FT" readonly  />
								<font color=green>'.$row_select['snf_per_ft'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if($row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" value="'.$row_select['snf_per_ft'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" value="'.$row_select['snf_per_ft'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['snf_per_ft'].'</font></td>';	
							}
							echo '<input type="hidden" name="snf_per_ft[]" id="snf_per_ft_'.$sno.'" value="'.$row_select['snf_per_ft'].'"  />';										
				
						}
					}
					///*********************************** common testing *************************************////
					if($row_select['qty_ct']=="") //	qty_ct (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" value=""  placeholder="QTY" readonly  />
							<font color=green>'.$row_select['qty_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" value="'.$row_select['qty_ct'].'"  placeholder="QTY" readonly  />
								<font color=green>'.$row_select['qty_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" value="'.$row_select['qty_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" value="'.$row_select['qty_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['qty_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="qty_ct[]" id="qty_ct_'.$sno.'" value="'.$row_select['qty_ct'].'"  />';										
				
						}
					}
					
					if($row_select['temp_ct']=="") //	(temp_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" value=""  placeholder="Temp(Deg)" readonly  />
							<font color=green>'.$row_select['temp_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" value="'.$row_select['temp_ct'].'"  placeholder="Temp(deg)" readonly  />
								<font color=green>'.$row_select['temp_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" value="'.$row_select['temp_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" value="'.$row_select['temp_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['temp_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="temp_ct[]" id="temp_ct_'.$sno.'" value="'.$row_select['temp_ct'].'"  />';										
				
						}
					}
					if($row_select['acidity_ct']=="") //	(acidity_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" value=""  placeholder="Acidity" readonly  />
							<font color=green>'.$row_select['acidity_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" value="'.$row_select['acidity_ct'].'"  placeholder="Acidity" readonly  />
								<font color=green>'.$row_select['acidity_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" value="'.$row_select['acidity_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" value="'.$row_select['acidity_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['acidity_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="acidity_ct[]" id="acidity_ct_'.$sno.'" value="'.$row_select['acidity_ct'].'"  />';										
				
						}
					}
					if($row_select['mbrt_min_ct']=="") //	(mbrt_min_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" value=""  placeholder="mbrt min ct" readonly  />
							<font color=green>'.$row_select['mbrt_min_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" value="'.$row_select['mbrt_min_ct'].'"  placeholder="mbrt min ct" readonly  />
								<font color=green>'.$row_select['mbrt_min_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" value="'.$row_select['mbrt_min_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" value="'.$row_select['mbrt_min_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['mbrt_min_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="mbrt_min_ct[]" id="mbrt_min_ct_'.$sno.'" value="'.$row_select['mbrt_min_ct'].'"  />';										
				
						}
					}
					if($row_select['mbrt_rm_ct']=="") //	(mbrt_rm_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" value=""  placeholder="mbrt rm ct" readonly  />
							<font color=green>'.$row_select['mbrt_rm_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" value="'.$row_select['mbrt_rm_ct'].'"  placeholder="mbrt rm ct" readonly  />
								<font color=green>'.$row_select['mbrt_rm_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if($row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" value="'.$row_select['mbrt_rm_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" value="'.$row_select['mbrt_rm_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['mbrt_rm_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="mbrt_rm_ct[]" id="mbrt_rm_ct_'.$sno.'" value="'.$row_select['mbrt_rm_ct'].'"  />';										
				
						}
					}
					if($row_select['mbrt_br_ct']=="") //	(mbrt_br_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" value=""  placeholder="mbrt br ct" readonly  />
							<font color=green>'.$row_select['mbrt_br_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" value="'.$row_select['mbrt_br_ct'].'"  placeholder="mbrt br ct" readonly  />
								<font color=green>'.$row_select['mbrt_br_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" value="'.$row_select['mbrt_br_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" value="'.$row_select['mbrt_br_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['mbrt_br_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="mbrt_br_ct[]" id="mbrt_br_ct_'.$sno.'" value="'.$row_select['mbrt_br_ct'].'"  />';										
				
						}
					}
					if($row_select['protien_per_ct']=="") //	(protien_per_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" value=""  placeholder="Protien %" readonly  />
							<font color=green>'.$row_select['protien_per_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" value="'.$row_select['protien_per_ct'].'"  placeholder="Protien %" readonly  />
								<font color=green>'.$row_select['protien_per_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="protien_per_ct[]" id=protien_per_ct_'.$sno.'" value="'.$row_select['protien_per_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" value="'.$row_select['protien_per_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['protien_per_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="protien_per_ct[]" id="protien_per_ct_'.$sno.'" value="'.$row_select['protien_per_ct'].'"  />';										
				
						}
					}
					
					if($row_select['sodium_ct']=="") //	(sodium_ct)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" value=""  placeholder="Sodium" readonly  />
							<font color=green>'.$row_select['sodium_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if( $row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" value="'.$row_select['sodium_ct'].'"  placeholder="Sodium" readonly  />
								<font color=green>'.$row_select['sodium_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="sodium_ct[]" id=sodium_ct_'.$sno.'" value="'.$row_select['sodium_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" value="'.$row_select['sodium_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['sodium_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="sodium_ct[]" id="sodium_ct_'.$sno.'" value="'.$row_select['sodium_ct'].'"  />';										
				
						}
					}
					
					if($row_select['testing_status']=="") //	(testing_status)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if($row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" value=""  placeholder="Testing Status" readonly  />
							<font color=green>'.$row_select['testing_status'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" value="'.$row_select['testing_status'].'"  placeholder="Testing Status" readonly  />
								<font color=green>'.$row_select['testing_status'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="testing_status[]" id=testing_status_'.$sno.'" value="'.$row_select['testing_status'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" value="'.$row_select['testing_status'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['testing_status'].'</font></td>';	
							}
							echo '<input type="hidden" name="testing_status[]" id="testing_status_'.$sno.'" value="'.$row_select['testing_status'].'"  />';										
				
						}
					}
					//=======================RESAMPLING===================================//
					///*********************************** fat percentage resampling testing *************************************////
					if($row_select['fat_per_rt']=="") //	fat_per_rt (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" value=""  placeholder="FAT % RT" readonly  />
							<font color=green>'.$row_select['fat_per_rt'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if( $row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" value="'.$row_select['fat_per_rt'].'"  placeholder="FAT % RT" readonly  />
								<font color=green>'.$row_select['fat_per_rt'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" value="'.$row_select['fat_per_rt'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" value="'.$row_select['fat_per_rt'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['fat_per_rt'].'</font></td>';	
							}
							echo '<input type="hidden" name="fat_per_rt[]" id="fat_per_rt_'.$sno.'" value="'.$row_select['fat_per_rt'].'"  />';										
				
						}
					}
					///*********************************** snf percentage resampling testing *************************************////
					if($row_select['snf_per_rt']=="") //	snf_per_rt (empty)
					{
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" value=""  placeholder="SNF % RT" readonly  />
							<font color=green>'.$row_select['snf_per_rt'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" />';
						}
						
					}
					else
					{
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" value="'.$row_select['snf_per_rt'].'"  placeholder="SNF % RT" readonly  />
								<font color=green>'.$row_select['snf_per_rt'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" value="'.$row_select['snf_per_rt'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" value="'.$row_select['snf_per_rt'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['snf_per_rt'].'</font></td>';	
							}
							echo '<input type="hidden" name="snf_per_rt[]" id="snf_per_rt_'.$sno.'" value="'.$row_select['snf_per_rt'].'"  />';										
				
						}
					}
					///*********************************** common testing for Adultration *************************************////
					if($row_select['adultration_ct']=="") //	adultration_ct (empty)
					{
						$adultration_db_value2=explode(",",$row_select['adultration_ct']);
						$adultr_db_value="";
						foreach($adultration_db_value2 as $adval)
						{
							$adultr_db_value=$adultr_db_value.$adval."<br>";
						}
						$adultr_db_value=substr($adultr_db_value,0,-1);
						
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" value=""  placeholder="ADULTRATION" readonly  />
							<font color=green>'.$adultr_db_value.'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" />';
						}
						
					}
					else
					{
						$adultration_db_value2=explode(",",$row_select['adultration_ct']);
						$adultr_db_value="";
						foreach($adultration_db_value2 as $adval)
						{
							$adultr_db_value=$adultr_db_value.$adval."<br>";
						}
						$adultr_db_value=substr($adultr_db_value,0,-1);
						
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" value="'.$row_select['adultration_ct'].'"  placeholder="ADULTRATION" readonly  />
								<font color=green>'.$adultr_db_value.'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" value="'.$row_select['adultration_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" value="'.$row_select['adultration_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$adultr_db_value.'</font></td>';	
							}
							echo '<input type="hidden" name="adultration_ct[]" id="adultration_ct_'.$sno.'" value="'.$row_select['adultration_ct'].'"  />';										
				
						}
					}
					///////////////===============other adultration============================//
					if($row_select['otheradultration_ct']=="") //	otheradultration_ct (empty)
					{
						
						
						//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
						if( $row_select['invoice_status'] == 1) //for plant and admin
						{
							echo '<td align=right>
							<input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" value=""  placeholder="OTHERADULTRATION" readonly  />
							<font color=green>'.$row_select['otheradultration_ct'].'</font>
							</td>';										
						}
						//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
						else if($row_select['invoice_status'] != 1) //for plant and admin)
						{
							echo'<td align=right><font color=red>X</font><input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" /></td>';
						}
						else{
							echo'<input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" />';
						}
						
					}
					else
					{
											
						if($row_select['invoice_status'] == 1)
						{
							//if($user_type!="raw_milk" && $row_select->invoice_status == 1) //for plant and admin
							if($row_select['invoice_status'] == 1) //for plant and admin
							{
								echo '<td align=right>
								<input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" value="'.$row_select['otheradultration_ct'].'"  placeholder="OTHERADULTRATION" readonly  />
								<font color=green>'.$row_select['otheradultration_ct'].'</font>
								</td>';										
							}
							//else if($user_type!="raw_milk" && $row_select->invoice_status != 1) //for plant and admin)
							else if( $row_select['invoice_status'] != 1) //for plant and admin)
							{
								echo'<td align=right><font color=red>X</font><input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" value="'.$row_select['otheradultration_ct'].'" /></td>';
							}
							else{
								echo'<input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" value="'.$row_select['otheradultration_ct'].'" />';
							}
						}
						else
						{
							//if($user_type!="raw_milk" ) //for plant and admin
							{
								echo '<td align=right><font color=green>'.$row_select['otheradultration_ct'].'</font></td>';	
							}
							echo '<input type="hidden" name="otheradultration_ct[]" id="otheradultration_ct_'.$sno.'" value="'.$row_select['otheradultration_ct'].'"  />';										
				
						}
					}
					////////////////////////
					
					if( $user_type!="plant_raw_milk" && $user_type!="raw_milk" ){//admin
						if($plant_acceptance_time=="" && $row_select['invoice_status'] == 1 && $close_invoice_flag == 0 ){
							echo '<td align=right><input type="checkbox" name="approvalcheck_'.$sno.'" id="approvalcheck_'.$sno.'" onclick=setapproval('.$sno.') ></td>';
						}
						else{
							echo '<td align=right></td>';
						}						
					}
					if( $user_type!="plant_raw_milk"){
						echo '<td><input type="hidden" id="pre_plant_'.$sno.'" name="pre_plant_'.$sno.'" value='.$row_select['plant'].' ><input type="hidden" id="acceptancetime_'.$sno.'" name="acceptancetime_'.$sno.'" value='.$plant_acceptance_time.' >
						'.$plant_acceptance_time.'</td>';
					}
					echo '<input type="hidden" id="approval_'.$sno.'" name="approval_'.$sno.'" >';
				echo '</tr>';
				
				
				//######## PDF /CSV
				echo"<input TYPE=\"hidden\" VALUE=\"$sno_local\" NAME=\"temp[$i][SNo]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[create_date]\" NAME=\"temp[$i][FIRST CREATE DATE]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[lorry_no]\" NAME=\"temp[$i][LORRY NO]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[vehicle_no]\" NAME=\"temp[$i][VEHICLE NO]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[tanker_type]\" NAME=\"temp[$i][TANKER TYPE]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[docket_no]\" NAME=\"temp[$i][DOCKET NO]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[email]\" NAME=\"temp[$i][EMAIL]\">";
				
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[mobile]\" NAME=\"temp[$i][TRANSPORTER MOBILE]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[qty_kg]\" NAME=\"temp[$i][QTY(KG)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[fat_percentage]\" NAME=\"temp[$i][FAT(%)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[snf_percentage]\" NAME=\"temp[$i][SNF(%)]\">";
				
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[fat_kg]\" NAME=\"temp[$i][FAT(KG)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[snf_kg]\" NAME=\"temp[$i][SNF(KG)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$manual_milk_hr\" NAME=\"temp[$i][MANUAL MILK AGE(Hrs)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[dispatch_time]\" NAME=\"temp[$i][DISPATCH TIME]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[target_time]\" NAME=\"temp[$i][TARGET TIME]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[driver_name]\" NAME=\"temp[$i][DRIVER NAME]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[driver_mobile]\" NAME=\"temp[$i][DRIVER MOBILE]\">";
				
				echo"<input TYPE=\"hidden\" VALUE=\"$user_name($user_id)\" NAME=\"temp[$i][USERNAME(USERID)]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$status_download\" NAME=\"temp[$i][STATUS]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[plant]\" NAME=\"temp[$i][PLANT]\">";
				echo"<input TYPE=\"hidden\" VALUE=\"$row_select[chilling_plant]\" NAME=\"temp[$i][CHILLING PLANT]\">";
				//if( $user_type!="raw_milk")
				{
                                        echo"<input TYPE=\"hidden\" VALUE=\"$row_select[lecino]\" NAME=\"temp[$i][LECINO]\">";
                                        echo"<input TYPE=\"hidden\" VALUE=\"$row_select[invoice_material]\" NAME=\"temp[$i][MATERIAL]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[unload_estimated_time]\" NAME=\"temp[$i][UPLOAD EST. TIME]\">";					
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[unload_estimated_datetime]\" NAME=\"temp[$i][GATE ENTRY]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[unload_accept_time]\" NAME=\"temp[$i][POSTING DATE/TIME]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[fat_per_ft]\" NAME=\"temp[$i][FAT%(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[snf_per_ft]\" NAME=\"temp[$i][SNF%(FT]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[qty_ct]\" NAME=\"temp[$i][Qty(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[temp_ct]\" NAME=\"temp[$i][Temp.(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[acidity_ct]\" NAME=\"temp[$i][Acidity(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[mbrt_min_ct]\" NAME=\"temp[$i][MBRT-min(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[mbrt_rm_ct]\" NAME=\"temp[$i][RM(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[mbrt_br_ct]\" NAME=\"temp[$i][BR(FT)]\">";		
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[protien_per_ct]\" NAME=\"temp[$i][Protien%(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[sodium_ct]\" NAME=\"temp[$i][Sodium(FT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[testing_status]\" NAME=\"temp[$i][Testing Status]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[fat_per_rt]\" NAME=\"temp[$i][FAT%(RT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[snf_per_rt]\" NAME=\"temp[$i][SNF%(RT)]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[adultration_ct]\" NAME=\"temp[$i][ADULTRATION]\">";
					echo"<input TYPE=\"hidden\" VALUE=\"$row_select[otheradultration_ct]\" NAME=\"temp[$i][OTHER-ADULTRATION]\">";
				}
				if( $user_type!="plant_raw_milk"){
					echo"<input TYPE=\"hidden\" VALUE=\"$plant_acceptance_time\" NAME=\"temp[$i][APPROVED TIME]\">";
					
				}
				
				if( $user_type!="raw_milk"){
					echo"<input TYPE=\"hidden\" VALUE=\"$closetime\" NAME=\"temp[$i][CLOSE TIME]\">";
				}
				
				$adultration_db_value1=explode(",",$row_select['adultration_ct']);
				$adultration_db_value="";
				foreach($adultration_db_value1 as $adval)
				{
					$adultration_db_value=$adultration_db_value.$adval."/";
				}
				$adultration_db_value=substr($adultration_db_value,0,-1);
				
				$user_name=str_replace(',','',$user_name);
				
				if( $user_type=="plant_raw_milk"){
					$csv_string = $csv_string.$sno_local.','.$row_select['create_date'] .','. $row_select['lorry_no'].','.$row_select['vehicle_no'].','.$row_select['tanker_type'].','.$row_select['docket_no'].','.$row_select['email'].','.$row_select['mobile'].','.$row_select['qty_kg'].','.$row_select['fat_percentage'].','.$row_select['snf_percentage'].','.$row_select['fat_kg'].','.$row_select['snf_kg'].','.$manual_milk_hr.','.$row_select['dispatch_time'].','.$row_select['target_time'].','.$row_select['driver_name'].','.$row_select['driver_mobile'].','.$user_name.'('.$user_id.'),'.$status_download.','.$row_select['plant'].','.trim($row_select['lecino']).','.$row_select['chilling_plant'].','.$row_select['invoice_material'].','.$row_select['unload_estimated_time'].','.$row_select['unload_estimated_datetime'].','.$row_select['unload_accept_time'].','.$row_select['fat_per_ft'].','.$row_select['snf_per_ft'].','.$row_select['qty_ct'].','.$row_select['temp_ct'].','.$row_select['acidity_ct'].','.$row_select['mbrt_min_ct']
		.','.$row_select['mbrt_rm_ct'].','.$row_select['mbrt_br_ct'].','.$row_select['protien_per_ct'].','.$row_select['sodium_ct'].','.$row_select['testing_status'].','.$row_select['fat_per_rt']
		.','.$row_select['snf_per_rt'].','.$adultration_db_value.','.$otheradultration_db_value.','.$closetime."\n";
				}
				else if( $user_type=="raw_milk"){
					$csv_string = $csv_string.$sno_local.','.$row_select['create_date'] .','.$row_select['lorry_no'].','.$row_select['vehicle_no'].','.$row_select['tanker_type'].','.$row_select['docket_no'].','.$row_select['email'].','.$row_select['mobile'].','.$row_select['qty_kg'].','.$row_select['fat_percentage'].','.$row_select['snf_percentage'].','.$row_select['fat_kg'].','.$row_select['snf_kg'].','.$manual_milk_hr.','.$row_select['dispatch_time'].','.$row_select['target_time'].','.$row_select['driver_name'].','.$row_select['driver_mobile'].','.$user_name.'('.$user_id.'),'.$status_download.','.$row_select['plant'].','.trim($row_select['lecino']).','.$row_select['chilling_plant'].','.$row_select['invoice_material'].','.$plant_acceptance_time.','.$row_select['unload_estimated_time'].','.$row_select['unload_estimated_datetime'].','.$row_select['unload_accept_time'].','.$row_select['fat_per_ft'].','.$row_select['snf_per_ft'].','.$row_select['qty_ct'].','.$row_select['temp_ct'].','.$row_select['acidity_ct'].','.$row_select['mbrt_min_ct']
		.','.$row_select['mbrt_rm_ct'].','.$row_select['mbrt_br_ct'].','.$row_select['protien_per_ct'].','.$row_select['sodium_ct'].','.$row_select['testing_status'].','.$row_select['fat_per_rt']
		.','.$row_select['snf_per_rt'].','.$adultration_db_value.','.$otheradultration_db_value."\n";
				}
				
				else{
					$csv_string = $csv_string.$sno_local.','.$row_select['create_date'] .','.$row_select['lorry_no'].','.$row_select['vehicle_no'].','.$row_select['tanker_type'].','.$row_select['docket_no'].','.$row_select['email'].','.$row_select['mobile'].','.$row_select['qty_kg'].','.$row_select['fat_percentage'].','.$row_select['snf_percentage'].','.$row_select['fat_kg'].','.$row_select['snf_kg'].','.$manual_milk_hr.','.$row_select['dispatch_time'].','.$row_select['target_time'].','.$row_select['driver_name'].','.$row_select['driver_mobile'].','.$user_name.'('.$user_id.'),'.$status_download.','.$row_select['plant'].','.trim($row_select['lecino']).','.$row_select['chilling_plant'].','.$row_select['invoice_material'].','.$plant_acceptance_time.','.$row_select['unload_estimated_time'].','.$row_select['unload_estimated_datetime'].','.$row_select['unload_accept_time'].','.$row_select['fat_per_ft'].','.$row_select['snf_per_ft'].','.$row_select['qty_ct'].','.$row_select['temp_ct'].','.$row_select['acidity_ct'].','.$row_select['mbrt_min_ct']
		.','.$row_select['mbrt_rm_ct'].','.$row_select['mbrt_br_ct'].','.$row_select['protien_per_ct'].','.$row_select['sodium_ct'].','.$row_select['testing_status'].','.$row_select['fat_per_rt']
		.','.$row_select['snf_per_rt'].','.$adultration_db_value.','.$otheradultration_db_value.','.$closetime."\n";
				}
				
				//###########
				$i++;
				$sno_local++;
			}
			echo '<input type="hidden" value="'.$sno.'" id="counter"/>';
			echo '<input type="hidden" id="tmp_serial"/>';
                        echo '<input type="hidden" id="tmp_tot_list" value="'.$sno_local.'" />';
			
	echo '		
	 </tbody>
	</table>
	</div>
	<section>
	</form>';
	echo'
	<form name="invoice_form_csv" id="invoice_form_csv" method = "post" target="_blank">
		<center>
		<input TYPE="hidden" VALUE="Raw_Milk_Invoice" NAME="csv_type" id="csv_type">
		<input TYPE="hidden" VALUE="'.$csv_string.'" NAME="csv_string" id="csv_string">			
		<input type="button" onclick="javascript:manage_csv(\'src/php/report_getpdf_type3.php?size='.$sno_local.'\');" value="Get PDF" class="noprint">
		&nbsp;
		<input type="button" onclick="javascript:manage_csv_post(\'src/php/report_csv.php\');" value="Get CSV" class="noprint">
		&nbsp;
		</center>
	</form>
	';
	?>
	
                            </div>
	
	<table align="center">
		<tr>
			<td colspan="3">
				<?php		
					//if( $user_type=="plant_raw_milk")
                                        if(   $user_type!="raw_milk")
                                            {
						echo '
						<table><tr><td>
						<input type="button" value="Close\Update" id="enter_button" onclick="javascript:return action_manage_invoice_update(\'edit\')"/></td>';
						echo '
						<td><br><form  name="invoice_form_live" id="invoice_form_live" method = "post" target="_blank">
						<input type="button" onclick="javascript:action_manage_invoice_update(\'tracking\');" value="Get Live Tracking">	
						<input type=hidden id="tot_vehicle_live" name="tot_vehicle_live" >
						</form></td></tr></table>
						';
					}
					else if( $user_type=="raw_milk"){
						echo '<input type="button" value="Update/Cancel" id="enter_button" onclick="javascript:return action_manage_invoice_update(\'edit\')"/>&nbsp;';
					}
					else{
						echo '<input type="button" value="Update/Close/Cancel" id="enter_button" onclick="javascript:return action_manage_invoice_update(\'edit\')"/>&nbsp;';
					}
					
				
				?>
			</td>
		</tr>
	</table>
		
	<div id="blackout"> </div>
        <div id="divpopup_lorry">
	   
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return close_lorry_pre_cancel()" class="hs3">Close</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">EDIT LORRY NUMBER</td>
			</tr>							
		</table>
		<br>
                <?php
                    if($user_type=="raw_milk")
                    {
                        
                        $final_lorry_list=lorrylistTransporterAll($self_child_transporter_id,$DbConnection);
                    }
                    else
                    {
                        $final_lorry_list=lorrylistAll($DbConnection);
                    }
                    
                
                    
                    //print_r($final_lorry_list);
                    $final_lorry="";
                    if(count($final_lorry_list)>0)
                    {
                            foreach($final_lorry_list as $fl)
                            {
                                    $final_lorry .=$fl.",";
                            }
                            $final_lorry = substr($final_lorry, 0, -1);
                            $final_lorry=str_replace(' ','%20',$final_lorry);
                    }
                    echo"<input type='hidden' name='final_lrno' id='final_lrno' value=$final_lorry  />";                   
                ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" rules="all" style="background-color:ghostwhite;">							
			<tr>
				<td>Lorry No :</td>
                                <td>
                                    <input type="text" id="edit_lorryno" />
                                    <input type="hidden" id="temp_lorryno"  />
				</td>
			</tr>
			<tr><td colspan="2"><br></td></tr>
		</table>
		
		<br><center><input type="button" value="Edit" onclick="javascript:close_lorry_pre();"></center>
			
	</div>
        
        <div id="divpopup_vehicle_no">
	   
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return close_vehicle_no_pre_cancel()" class="hs3">Close</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">EDIT VEHICLE NUMBER</td>
			</tr>							
		</table>
		<br>
                <?php
                    $final_vehicle_list=vehiclelistAll($DbConnection);
                    //print_r($final_lorry_list);
                    $final_vehicle="";
                    if(count($final_vehicle_list)>0)
                    {
                            foreach($final_vehicle_list as $fl)
                            {
                                    $final_vehicle .=$fl.",";
                            }
                            $final_vehicle = substr($final_vehicle, 0, -1);
                            $final_vehicle=str_replace(' ','%20',$final_vehicle);
                    }
                    echo"<input type='hidden' name='final_vehicleno' id='final_vehicleno' value=$final_vehicle  />"; 
                    $vehicle_list_all=array();
                    global $vehicle_list_all;
                    get_user_vehicle($root,$account_id);
                    //print_r($vehicle_list_all);
                    $vehicle_list_all1 = array_unique($vehicle_list_all);
                    $all_vehicles_list = "";                    
                    foreach($vehicle_list_all1 as $vl){                          
                            $all_vehicles_list.= trim($vl).",";                           
                    }                   
                    $all_vehicles_list = substr($all_vehicles_list, 0, -1);                    
                    $all_vehicles_list = str_replace(' ','%20',$all_vehicles_list);
                    echo"<input type='hidden' name='vehicle_list_hidden' id='vehicle_list_hidden' value=$all_vehicles_list  />";  
                ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" rules="all" style="background-color:ghostwhite;">							
			<tr>
				
                                <td>
                                    <input type="hidden" id="edit_vehicle_no" />
                                    <input type="hidden" id="temp_vehicle_no"  />
				</td>
			</tr>
                        <tr>
				<td>Select Vehicle :</td><td>
				<input type="text" id="vehicle_list" name="vehicle_list"  size="30" onKeyUp="getScriptPage_raw_milk_for_edit(this.value,this.id,'box')">
				<div id="box2" class="input-div-route" style="display:none"></div>
				</td>
				
			</tr>
			<tr><td colspan="2"><br></td></tr>
		</table>
                
		
		<br><center><input type="button" value="Edit" onclick="javascript:close_vehicle_no_pre();"></center>
			
	</div>
        
        <div id="divpopup_invoice_qty">
	   
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return close_qtykg_pre_cancel()" class="hs3">Close</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">EDIT QUANTITIES PARAMETERS</td>
			</tr>							
		</table>
		<br>
              
		<table width="100%" border="0" cellpadding="0" cellspacing="0" rules="all" style="background-color:ghostwhite;">							
                    <tr>			
                       
                        <td>Qty:</td>
                        <td>
                                <input type="text" id="temp_qty_kg" placeholder="Qty" onblur=IsNumericA(this.value,this.id); onkeyup="javascript:put_fat_snf_kg_edit(this.value);">
                        </td>
                    </tr>
                    <tr>			
                       
                        <td>Fat %:</td>
                        <td>
                                <input type="text" id="temp_fat_per" placeholder="Fat %" onblur=IsNumericA(this.value,this.id); onkeyup="javascript:put_fat_kg_edit(this.value);"  >
                        </td>
                    </tr>
                        <tr>
                        <td>Snf %:</td>
                        <td>
                                <input type="text" id="temp_snf_per" placeholder="Snf %" onblur=IsNumericA(this.value,this.id); onkeyup="javascript:put_snf_kg_edit(this.value);"  >
                        </td>
                               
                    </tr>
                    <tr>			
                       
                        <td>Fat Kg:</td>
                        <td>
                                <input type="text" id="temp_fat_kg" readonly >
                        </td>
                    </tr>
                    <tr>			
                       
                        <td>Snf Kg:</td>
                        <td>
                                <input type="text" id="temp_snf_kg" readonly >
                        </td>
                    </tr>
                    <tr><td colspan="2"><br></td></tr>
		</table>
                
		
		<br><center><input type="button" value="Edit" onclick="javascript:close_qtykg_pre();"></center>
			
	</div>
        
	<div id="divpopup_plant">
	    <?php if($user_type=="raw_milk"){ ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return close_plant_list_transporter()" class="hs3">Close</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">ASSIGN PLANT</td>
			</tr>							
		</table>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" rules="all" style="background-color:ghostwhite;">							
			<tr>
				<td>Select Plant :</td><td>
				<select name="plant_list" id="plant_list">
					<option value="0">Select Customer</option>
					<?php
						/*$query_plant = "SELECT customer_no FROM station WHERE type=1 AND user_account_id='$parent_admin_id' AND status=1";
						$result_query = mysql_query($query_plant,$DbConnection);
						while($row=mysql_fetch_object($result_query))
						{
							echo '<option value="'.$row->customer_no.'">'.$row->customer_no.'</option>';
						}*/
						$i=0;
						foreach($final_plant_list as $raw_milk_plant){
						echo '<option value="'.$raw_milk_plant.'">'.$final_plant_name_list[$i].'('.$raw_milk_plant.')</option>';
							$i++;
						}
					?>
				</select></td>
			</tr>
			<tr><td colspan="2"><br></td></tr>
		</table>
		
		<br><center><input type="button" value="Assign" onclick="javascript:close_plant_list_transporter();"></center>
		<?php }
		else { ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return close_plant_list()" class="hs3">Close</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">ASSIGN PLANT</td>
			</tr>							
		</table>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" rules="all" style="background-color:ghostwhite;">							
			<tr>
				<td>Select Plant :</td><td>
				<select name="plant_list" id="plant_list">
					<option value="0">Select Customer</option>
					<?php
						$query_plant = "SELECT customer_no FROM station WHERE type=1 AND user_account_id='$account_id' AND status=1";
						$result_query = mysql_query($query_plant,$DbConnection);
						while($row=mysql_fetch_object($result_query))
						{
							echo '<option value="'.$row->customer_no.'">'.$row->customer_no.'</option>';
						}
					?>
				</select></td>
			</tr>
			<tr><td colspan="2"><br></td></tr>
		</table>
		<!--<br><center><div id='approve_pre_visible'><input type="checkbox" id="accept_pre" name="accept_pre">Approved</div></center>-->
		<br><center><input type="button" value="Assign" onclick="javascript:close_plant_list();"></center>
		<?php } ?>
		
	</div>
	<div id="divpopup_milkage">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="skyblue">							
			<tr>
				<td class="manage_interfarce" align="right"><a href="#" onclick="javascript:return cancel_milkage()" class="hs3">Cancel&nbsp;</a></td> 													
			</tr> 
			<tr>
				<td colspan="5" valign="top" align="justify">MILK AGE ACCEPT TIME AND RESAMPLING</td>
			</tr>							
		</table>
		<br>
		<form id="popupform" name="popupform">
		<table align="center" border="0" cellpadding="0" cellspacing="0" rules="" style="background-color:ghostwhite;">							
			
			<tr>
				<td style="height:8px;" colspan=3></td>
			</tr>
			<tr>
				<td colspan=3>
					<fieldset>
					<legend>
						First Plant Testing</legend>
							<table  align="center">
                                                                <tr>
									<td>Invoice Material:</td>
									<td>
                                                                            <select name="temp_inv_material" id="temp_inv_material">
                                                                                    <option value="0">Select</option>
                                                                                    <?php
                                                                                            getAllRawMilkInvoiceMaterial($account_id,$DbConnection);
                                                                                            $data=getAllRawMilkInvoiceMaterial($account_id,$DbConnection);				
                                                                                            foreach($data as $dt)
                                                                                            {					
                                                                                                    $code=$dt['code']; 
                                                                                                    $name=$dt['name'];
                                                                                                    $sno=$dt['sno']; 
                                                                                                    echo'<option value='.$code.'>'.$name.'('.$code.')</option>';
                                                                                            }
                                                                                    ?>
                                                                            </select>
                                                                        </td>
								</tr>
								<tr>
									<td>Gate Entry/Unload Estimate Time:</td><td>
									<input type="text" id="temp_unload_estimate_datetime"  onclick="javascript:NewCal(this.id,'yyyymmdd',true,24);"  onblur='close_milkage_cal_min();' readonly >
									<input type="label" id="temp_unload_estimate_datetime_label" style='display:none;' >
									<input type="text" id="temp_unload_estimate_time" size='5'  placeholder="In Minutes"  onblur='IsNumericA(this.value,this.id); ' readonly >&nbsp;mins</td>
								</tr>
								<tr>
									<td>Posting Date/Time: </td>
									<td>
										<input type="text" id="temp_unload_accept_time"  onclick="javascript:NewCal(this.id,'yyyymmdd',true,24);"  onblur='close_milkage_cal_min();' readonly >
										<input type="label" id="temp_unload_accept_time_label" style='display:none;' >
									</td>
								</tr>
								<tr>
									<td>Fat %:</td>
									<td>
										<input type="text" id="temp_fat_per_ft" placeholder="Fat %" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Snf %:</td>
									<td>
										<input type="text" id="temp_snf_per_ft" placeholder="Snf %" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
							</table>					
					</fieldset>
				</td>
			</tr>
			
			<tr>
				<td colspan=3>
					<fieldset>
					<legend>
						<input type="checkbox" name='resampling_chk' id='resampling_chk' onclick='javascript:show_resampling_block(checked);'>Resampling</legend>
							<table  align="center" >
								
								<tr style="display:none" id='resamplingTest'>
									<td>
										<table>								
											<tr>
												<td>Fat %:</td>
												<td>
													<input type="text" id="temp_fat_per_rt" placeholder="Fat %" onblur=IsNumericA(this.value,this.id); >
												</td>
											</tr>
											<tr>
												<td>Snf %:</td>
												<td>
													<input type="text" id="temp_snf_per_rt" placeholder="Snf %" onblur=IsNumericA(this.value,this.id); >
												</td>
											</tr>
										</table>
									</td>
								</tr>
								
							</table>					
					</fieldset>
				</td>
				
			</tr>
			<tr>
				<td colspan=3 >
					<fieldset>
						<legend>Common Values</legend>
						<table align="center">
							<tr>
									<td>Qty:</td>
									<td>
										<input type="text" id="temp_qty_ct" placeholder="Qty" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Temp(degC):</td>
									<td>
										<input type="text" id="temp_degree_ct" placeholder="Temp(degC)" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Acidity(%):</td>
									<td>
										<input type="text" id="temp_acidity_ct" placeholder="Acidity(%)" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								
								<tr>
									<td>MBRT(min):</td>
									<td>
										<input type="text" id="temp_mbrt_min_ct" placeholder="MBRT(min)" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>RM Value:</td>
									<td>
										<input type="text" id="temp_mbrt_rm_ct" placeholder="RM Value" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>BR Value:</td>
									<td>
										<input type="text" id="temp_mbrt_br_ct" placeholder="BR Value" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Protien(%):</td>
									<td>
										<input type="text" id="temp_protien_ct" placeholder="Protien(%)" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Sodium:</td>
									<td>
										<input type="text" id="temp_sodium_ct" placeholder="Sodium" onblur=IsNumericA(this.value,this.id); >
									</td>
								</tr>
								<tr>
									<td>Adultration:</td>
									<td>
										<select multiple name="temp_adultration_ct" id="temp_adultration_ct"  >
											<option value="Neutralizer">Neutralizer</option>
											<option value="Urea">Urea </option>
											<option value="AmmoniumCompound">Ammonium Compound</option>
											<option value="StarchAndCerealFlour">Starch & Cereal Flour</option>
											<option value="Salt">Salt</option>
											<option value="Sugar">Sugar</option>
											<option value="Glucose">Glucose</option>
											<option value="Maltodextrin">Maltodextrin</option>
											<option value="DetergentTest">Detergent Test</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Others:</td>
									<td><input type="text" id="temp_otheradultration_ct" placeholder="Oth.Adultration" maxlength='50' ></td>
								</tr>
								<tr>
									<td><input type=radio value="1" id="temp_accept_reject_sampling" name="temp_accept_reject_sampling" checked >Accept </td>
									<td>
										<input type=radio value="0" id="temp_accept_reject_sampling" name="temp_accept_reject_sampling" >Reject
									</td>
								</tr>
								<tr>
									<td colspan=2 align="center" >
										<input type="button" value="Ok" id="popupbutton" name="popupbutton" onclick="javascript:close_milkage();">
										<input type="hidden" value="" id="popup_action" name="popup_action" >
									</td>
								</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		</form>
		<!--<br><center><div id='approve_pre_visible'><input type="checkbox" id="accept_pre" name="accept_pre">Approved</div></center>-->
		<br>
	</div>
	
<?php
    function get_user_vehicle($AccountNode,$account_id)
    {
            //echo "hi".$account_id;
            global $vehicleid;
            global $vehicle_cnt;
            global $td_cnt;
            global $DbConnection;
            global $vehicle_list_all;
            if($AccountNode->data->AccountID==$account_id)
            {
                    $td_cnt =0;
                    for($j=0;$j<$AccountNode->data->VehicleCnt;$j++)
                    {			    
                            $vehicle_id = $AccountNode->data->VehicleID[$j];
                            $vehicle_name = $AccountNode->data->VehicleName[$j];
                            $vehicle_imei = $AccountNode->data->DeviceIMEINo[$j];
                            if($vehicle_id!=null)
                            {
                                    for($i=0;$i<$vehicle_cnt;$i++)
                                    {
                                            if($vehicleid[$i]==$vehicle_id)
                                            {
                                                    break;
                                            }
                                    }			
                                    if($i>=$vehicle_cnt)
                                    {
                                            $vehicleid[$vehicle_cnt]=$vehicle_id;
                                            $vehicle_cnt++;
                                            $td_cnt++;


                                                    $vehicle_list_all[]=$vehicle_name;

                                            if($td_cnt==3)
                                            {
                                                    $td_cnt=0;
                                            }
                                    }
                            }
                    }
            }
            $ChildCount=$AccountNode->ChildCnt;
            for($i=0;$i<$ChildCount;$i++)
            { 
                    get_user_vehicle($AccountNode->child[$i],$account_id);
            }
    }
?>