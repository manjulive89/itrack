<?php
	include_once('Hierarchy.php');
	include_once('util_session_variable.php');
	include_once('util_php_mysql_connectivity.php');
	//include_once('manage_hierarchy_header1.php');
        
        include_once("../../../phpApi/Cassandra/Cassandra.php");     //##### INCLUDE CASSANDRA API
        include_once("../../../phpApi/libLog.php");     //##### INCLUDE CASSANDRA API*/
    
        $o_cassandra = new Cassandra();	
        $o_cassandra->connect($s_server_host, $s_server_username, $s_server_password, $s_server_keyspace, $i_server_port);

        $vehicle_color1=getColorFromAP($account_id,$DbConnection); /// A->Account P->Preference
	$root=$_SESSION['root'];
	$common_id1=$_POST['common_id'];
	//echo "common_id=".$common_id1;
        $logDate=date('Y-m-d');
	echo'<input type="hidden" id="account_id_hidden" value='.$common_id1.'>';
	echo"<br>			
			<form name='manage1' method='post'>
                            <center>
                              <fieldset>
                                <legend>Vehicle To Route Assignment</legend>
                                    <table border=0 cellspacing=0 cellpadding=0 class='module_left_menu'>
                                    <tr>
                                            <td colspan='3'>
                                                    <INPUT TYPE='checkbox' name='all_vehicle' onclick='javascript:select_all_assigned_vehicle(this.form);'>
                                                    <font size='2'>Select All</font>"."												
                                            </td>																														
                                    </tr>";
                                    get_user_vehicle($root,$common_id1);
                            echo"</table>
				<br>
				<table border=0 cellspacing=0 cellpadding=0 class='module_left_menu' align='center'>
					";
					/*							
					$query="SELECT * from polyline where user_account_id='$common_id1' AND status='1'";
					//echo "query=".$query."<br>";
					$result=mysql_query($query,$DbConnection);
					$row_result=mysql_num_rows($result);		
					if($row_result!=null)
					{
						while($row=mysql_fetch_object($result))
						{									
							$polyline_id=$row->polyline_id;
							$polyline_name=$row->polyline_name;
                                                        echo"<tr>
								<td>
									&nbsp<INPUT TYPE='radio' name='polyline_id' VALUE='$polyline_id'>
									<font color='blue' size='2'>".$polyline_name."&nbsp;&nbsp;&nbsp;</font>"."												
								</td>																														
							</tr>";
						}
					}
					else
					{
						echo"<font color='blue' size='2'>NO POLYLINE/ROUTE FOUND IN THIS ACCOUNT</font>";
					}*/
                                        $row_data=getDetailAllPolylineMerge($common_id1,$DbConnection);
                                        if(count($row_data)>0)
                                        {
                                            sort_array_of_array($row_data, 'polyline_name');
                                            /*foreach($row_data as $row)
                                            {
                                                $polyline_id=$row['polyline_id'];
                                                $polyline_name=$row['polyline_name'];
                                                 echo"<tr>
                                                        <td>
                                                                &nbsp<INPUT TYPE='radio' name='polyline_id' VALUE='$polyline_id'>
                                                                <font color='blue' size='2'>".$polyline_name."&nbsp;&nbsp;&nbsp;</font>"."												
                                                        </td>																														
                                                </tr>";
                                            }*/
                                            echo "<select name='polyline_id' id='polyline_id'>
                                            <option value=0>Select</option>";
                                            foreach($row_data as $row)
                                            {
                                                $polyline_id=$row['polyline_id'];
                                                $polyline_name=$row['polyline_name'];
                                                echo'<option value='.$polyline_id.'>'.$polyline_name.'</option>';
                                                 ;
                                            }
                                            echo "</select>";
                                             
                                        }
                                        else
                                        {
                                            echo"<font color='blue' size='2'>NO POLYLINE/ROUTE FOUND IN THIS ACCOUNT</font>";
                                        }
						echo"</td>";
					echo"</tr>";
			echo'</table>
				<br>
					<input type="button" id="enter_button" name="enter_button" Onclick="javascript:return action_manage_polyline(\'assign\')" value="Assign">&nbsp;<input type="reset" value="Cancel">
				<br><a href="javascript:show_option(\'manage\',\'polyline\');" class="back_css">&nbsp;<b>Back</b></a>
                                </fieldset>	
                            </center>
			</form>';	
                        function sort_array_of_array(&$array, $subfield)
                        {
                            $sortarray = array();
                            foreach ($array as $key => $row)
                            {
                                $sortarray[$key] = $row[$subfield];
                            }

                            array_multisort($sortarray, SORT_ASC, $array);
                        }
                        
			function common_function_for_vehicle($vehicle_imei,$vehicle_id,$vehicle_name,$option_name)
			{
                            global $o_cassandra;
                            //var_dump($o_cassandra);
                            global $logDate;
				//$td_cnt++;
				global $td_cnt;
				if($td_cnt==1)
				{
					echo'<tr>';
				}
				
				//date_default_timezone_set('Asia/Calcutta');
				$current_date = date('Y-m-d');

				$logResult=hasImeiLogged($o_cassandra, $vehicle_imei, $logDate);
                            //$st_results = getCurrentDateTime($o_cassandra,$vehicle_imei,$sortFetchData);
                            //var_dump($st_results);
                            //$xml_current = "../../../xml_vts/xml_data/".$today_date2."/".$vehicle_imei.".xml";
                            if($logResult!='')
                            {
				echo'<td align="left"><INPUT TYPE="checkbox"  name="vehicle_id[]" VALUE="'.$vehicle_id.'"></td>
					   <td class=\'text\'>
					     <font color="darkgreen">'.$vehicle_name.'</font>
              
					   </td>';
				}
				else
				{
					echo'<td align="left">
							<INPUT TYPE="checkbox"  name="vehicle_id[]" VALUE="'.$vehicle_id.'">
						</td>
						<td class=\'text\'>
						  <font color="grey">'.$vehicle_name.'</font>
							
						</td>';
				}
				if($td_cnt==3)
				{ 
					echo'</tr>';
				}

			}

			function get_user_vehicle($AccountNode,$account_id)
			{
				global $vehicleid;
				global $vehicle_cnt;
				global $td_cnt;
				global $DbConnection;
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
								/*$query="SELECT vehicle_id FROM polyline_assignment WHERE vehicle_id='$vehicle_id' AND status='1'";
								//echo "query=".$query;
								$result=mysql_query($query,$DbConnection);
								$num_rows=mysql_num_rows($result);*/
                                                                $num_rows=PolylineAssignVehilce($vehicle_id,$DbConnection);
								if($num_rows==0)
								{							
									common_function_for_vehicle($vehicle_imei,$vehicle_id,$vehicle_name,$AccountNode->data->AccountGroupName);
								}
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
