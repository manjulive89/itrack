<?php
//error_reporting(-1);
//ini_set('display_errors', 'On');
	include_once('util_android_php_mysql_connectivity.php');  	   //util_session_variable.php sets values in session
	include_once('util_android_session_variable.php');   //util_php_mysql_connectivity.php make set connection of user to database  
        require_once "lib/nusoap.php"; 
	include_once('active_vehicle_func.php');
        require_once "lib/nusoap.php";
      
        $pathInPieces = explode(DIRECTORY_SEPARATOR ,dirname(__FILE__));
        //print_r($pathInPieces);
        $pathToRoot=$pathInPieces[0]."/".$pathInPieces[1]."/".$pathInPieces[2];
        //$pathToRoot=$pathInPieces[0]."/".$pathInPieces[1]."/".$pathInPieces[2]."/".$pathInPieces[3];
        //echo "pathToRoot=".$pathToRoot."<br>";
        //====cassamdra //////////////
        include_once($pathToRoot."/phpApi/Cassandra/Cassandra.php");     //##### INCLUDE CASSANDRA API
        //echo "fileExists=".file_exists($pathToRoot."/phpApi/Cassandra/Cassandra.php");
        include_once($pathToRoot."/phpApi/libLogNew.php");     //##### INCLUDE CASSANDRA API*/

        $o_cassandra = new Cassandra();	
        $o_cassandra->connect($s_server_host, $s_server_username, $s_server_password, $s_server_keyspace, $i_server_port);


        /* $groupId="";
        $userId="KTC";
        $password="BEEKAY";
        $sync="vehicleList";    
        $result=primaryDeviceInfo($groupId,$userId,$password,$sync);
        echo  $result;*/
	
        function primaryDeviceInfo($groupId,$userId,$password,$sync)
        {
        global $DbConnection;
        $android_group_id = trim($groupId);
	$android_user_id =trim($userId);
	$android_password=md5(trim($password));
	$sync = $sync;
	//date_default_timezone_set('Asia/Calcutta');
	$current_time = date('Y/m/d H:i:s');
	$today_date=explode(" ",$current_time);
	$today_date1=$today_date[0];
	$today_date2 = str_replace("/","-",$today_date1);
	
	/*$android_group_id="";
	$android_user_id="Sales2";
	$android_password=md5("odisha");
	$sync="login";*/
	
	$query ="SELECT account_id FROM account WHERE (group_id='$android_group_id' OR group_id IS NULL)".
		"AND user_id='$android_user_id' AND password='$android_password' AND (status=1 OR status=4)";
		//echo "query111=".$query."<br>";
	$result=mysql_query($query,$DbConnection);
	$row=mysql_fetch_row($result);
	$account_id=$row[0];
	
	//echo "test";
	//$imei_1=trim($_POST['imei']);	
		
	$process=0;
	if($sync=="check_connection")
	{
		echo "Success\n";		
	}
	
	if($sync=="login")
	{
		$query ="SELECT account_id,user_type,group_id FROM account WHERE (group_id='$android_group_id' OR group_id IS NULL) ".
		"AND user_id='$android_user_id' AND password='$android_password' AND (status=1 OR status=4)";
		//echo "query=".$query."<br>";
		$result=mysql_query($query,$DbConnection);		
		$num_rows=mysql_num_rows($result);
		if($num_rows>0)
		{
			$row=mysql_fetch_row($result);
			//echo "account_id=".$row[0]."<br>";
			$query1="SELECT account_feature.user_type_id,account_detail.name FROM account_feature,account_detail WHERE ".
			"account_detail.account_id=account_feature.account_id AND account_feature.account_id='$row[0]'";
			//echo "query1=".$query1."<br>";
			$result1=mysql_query($query1,$DbConnection);
			$row1=mysql_fetch_object($result1);
			//echo "usrtyoe=".$row1->user_type_id;
			$user_type_id=substr($row1->user_type_id,-1);
			if($user_type_id=="6")
			{
				$type="P";
			}
			else
			{
				$type="V";
			}
                        $data[]=array("actionStatus"=>"success","deviceType"=>$type,"accountName"=>$row1->name,"accountType"=>$row[1]);
									
		}
		else
		{		
                    $data[]=array("actionStatus"=>"failure","deviceType"=>"","accountName"=>"","accountType"=>"");
		}
                return json_encode($data);
	}
	else if($sync=="vehicleList")
	{
	 
		//echo "vehiclelist=";
		global $o_cassandra;
		$logDate=date('Y-m-d');
		$query5 = "SELECT vehicle_id FROM vehicle_grouping USE INDEX (vg_accountid_status) WHERE account_id = '$account_id' AND status=1";
		$result5=mysql_query($query5,$DbConnection);
		$num_rows1=mysql_num_rows($result5);
		$j=0;
		if($num_rows1!=0)
		{
			while ($row5=mysql_fetch_object($result5))
			{
				$vehicle_id[$j]=$row5 ->vehicle_id;
				$j++;   
			}
		}	
		$data=array();
		$query_test1 = "SELECT DISTINCT vehicle.vehicle_id,vehicle.vehicle_name,vehicle.vehicle_type,vehicle.category,vehicle.vehicle_tag,vehicle.vehicle_number,".
		"vehicle.max_speed,vehicle.fuel_voltage,vehicle.tank_capacity,vehicle_assignment.device_imei_no FROM vehicle ".
		"USE INDEX (v_vehicleid_status),vehicle_assignment USE INDEX (va_vehicleid_status) WHERE".
		" vehicle.vehicle_id=vehicle_assignment.vehicle_id AND ( ";
		$join_query="";
		if($j!=0)
		{
			for($k=0;$k<$j;$k++)
			{
				if($k==($j-1))
				{        
				$join_query=$join_query." vehicle.vehicle_id='$vehicle_id[$k]'";
				}
				else
				{ 
				$join_query=$join_query." vehicle.vehicle_id='$vehicle_id[$k]' OR";
				}  
			}
			$query_test=$query_test1.$join_query.") AND vehicle.status=1 AND vehicle_assignment.status=1";
			// echo "<br>".$query_test;
			$result_test=mysql_query($query_test,$DbConnection);
			while ($row_1=mysql_fetch_object($result_test))
			{
				$query_io = "SELECT io FROM device_manufacturing_info USE INDEX (dmi_device_imei_status) WHERE device_imei_no='$row_1->device_imei_no' AND status=1";
				//echo "query_io=".$query_io."<br>";
				$result_io=mysql_query($query_io,$DbConnection);
				$row_io=mysql_fetch_row($result_io);
				//echo "row=".$row_io[0]."<br>";
				$query_fm = "SELECT feature_name FROM feature_mapping WHERE feature_id IN ($row_io[0]) AND status=1";
				//echo "query_fm=".$query_fm."<br>";
				$result_fm=mysql_query($query_fm,$DbConnection);
				$feature_names="";
				while($row_fm=mysql_fetch_object($result_fm))
				{
					$feature_names=$feature_names.$row_fm->feature_name.",";
				}
				$feature_names=substr($feature_names,0,-1);
				//echo "feature_name=".$feature_names."<br>";
				if($feature_names!="")
				{
					$query_iovalue="";
					$result_iovalue="";
					$row_iovalue="";
					$query_iovalue = "SELECT ".$feature_names." FROM io_assignment USE INDEX (ioa_vehicle_id_status),".
					"vehicle_assignment USE INDEX (va_vehicleid_imei_status) WHERE io_assignment.vehicle_id=vehicle_assignment".
					".vehicle_id AND vehicle_assignment.device_imei_no='$row_1->device_imei_no' AND vehicle_assignment.status=1".
					" AND io_assignment.status=1";
					//echo "query=".$query_iovalue."<br>";
					$result_iovalue=mysql_query($query_iovalue,$DbConnection);
					$feature_names1="";
					$tmp_arrrrr=array();
					reset($tmp_arrrrr);
					while($row_iovalue=mysql_fetch_object($result_iovalue))
					{
						//echo "feature_names=".$feature_names."<br>";
						$feature_names1=explode(",",$feature_names);
						$final_iotypevalue_str="";
						for($i=0;$i<sizeof($feature_names1);$i++)
						{
							if($row_iovalue->$feature_names1[$i]!="")
							{
								$final_iotypevalue_str=$final_iotypevalue_str.$row_iovalue->$feature_names1[$i]."^".$feature_names1[$i].":";								
							}						
						}
						$final_iotypevalue_str=substr($final_iotypevalue_str,0,-1);					
					}					
				}
			
				$vehicle_active_flag=0;
                                //echo "in insert and update flag<br>";
                                //$vehicle_imei="862170017134329";
                                $logResult=hasImeiLogged($o_cassandra, $row_1->device_imei_no, $logDate);
                                         //echo "logResult=".$logResult."<br>";
                            //$st_results = getCurrentDateTime($o_cassandra,$vehicle_imei,$sortFetchData);
                            //var_dump($st_results);
                            //$xml_current = "../../../xml_vts/xml_data/".$today_date2."/".$vehicle_imei.".xml";
                                if($logResult!='')
                                {
                                    $vehicle_active_flag=1;                                      
                                }
				
				if($vehicle_active_flag==1)
				{
					$data[]=array("deviceImeiNo"=>$row_1->device_imei_no,"vehicleName"=>$row_1->vehicle_name,"status"=>"green","vehicleIO"=>$final_iotypevalue_str);
				}
				else
				{
					$data[]=array("deviceImeiNo"=>$row_1->device_imei_no,"vehicleName"=>$row_1->vehicle_name,"status"=>"gray","vehicleIO"=>$final_iotypevalue_str);
				}
			}
		}
		return json_encode($data);
            }
        }
$server = new soap_server();
$server->register("primaryDeviceInfo");
$server->service($HTTP_RAW_POST_DATA);
	
?>