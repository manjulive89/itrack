<?php
	include_once('Hierarchy.php');
	include_once('util_session_variable.php');
	include_once('util_php_mysql_connectivity.php');
	//include_once('manage_hierarchy_header1.php');
	$root=$_SESSION['root'];
	$common_id1=$_POST['common_id'];
	//echo "common_id=".$common_id1;
	echo'<input type="hidden" id="account_id_hidden" value='.$common_id1.'>';
	echo '<center>
			<strong>
				 Vehicle Trip Assignment
			</strong>
			<br>
		</center>'; 
	echo"<br>			
			<form name='manage1' method='post'>
				<center>
				<div style='overflow:auto;height:300px'> 
        <table border=0 cellspacing=0 cellpadding=0 class='module_left_menu'>";
					get_user_vehicle($root,$common_id1);
			 echo"</table></div>

				<br>
			
        <div style='overflow:auto;height:300px'> 
        <table border=0 cellspacing=0 cellpadding=0 class='module_left_menu' align='center'>					
					<tr>
						<td colspan='3'>
							&nbsp;&nbsp;<INPUT TYPE='checkbox' name='all_station' onclick='javascript:select_all_stations(this.form);'>
							<font size='2'>Select All</font>"."												
						</td>																														
					</tr>";	          
												
					$query="SELECT * from landmark where account_id='$common_id1' AND status='1'";
					//echo "query=".$query."<br>";
					$result=mysql_query($query,$DbConnection);
					$row_result=mysql_num_rows($result);		
					
          if($row_result!=null)
					{  
            $k=0;
						while($row=mysql_fetch_object($result))
						{									
							$geo_id=$row->landmark_id;
							$geo_name=$row->landmark_name;
						  
              if($k==0)
              {
                echo"<tr>";
              }           

              echo"
							<td>
								&nbsp<INPUT TYPE='checkbox' name='station_id[]' VALUE='$geo_id'>
								<font color='blue' size='2'>".$geo_name."&nbsp;&nbsp;&nbsp;</font>"."												
							</td>";				
              $k++;																																	

							if($k==8)
							{
                echo "</tr>";
                $k=0;
						  }
            }
					}
					else
					{
						echo"<font color='blue' size='2'>NO LANDMARK/STATION FOUND IN THIS ACCOUNT</font>";
					}
						echo"</td>";
					echo"</tr>";
			echo'</table>
			   </div>
				<br>
					<input type="button" id="enter_button" name="enter_button" Onclick="javascript:return action_manage_vtrip(\'assign\')" value="Assign">&nbsp;<input type="reset" value="Cancel">
				<br><a href="javascript:show_option(\'manage\',\'vtrip\');" class="back_css">&nbsp;<b>Back</b></a>
				</center>
			</form>';	

			function common_function_for_vehicle($vehicle_imei,$vehicle_id,$vehicle_name,$option_name)
			{	
				//$td_cnt++;
				global $td_cnt;
				if($td_cnt==1)
				{
					echo'<tr>';
				}
				
				//date_default_timezone_set('Asia/Calcutta');
				$current_date = date('Y-m-d');

				$xml_file = "../../../xml_vts/xml_data/".$current_date."/".$vehicle_imei.".xml";
			
				if(file_exists($xml_file))
				{
				echo'<td align="left"><INPUT TYPE="radio"  name="vehicle_id" VALUE="'.$vehicle_id.'"></td>
					   <td class=\'text\'>
					     <font color="darkgreen">'.$vehicle_name.'</font>
                <!--<A HREF="#" style="text-decoration:none;" onclick="main_vehicle_information('.$vehicle_id.')">
						        <font color="darkgreen">'.$vehicle_name.'</font>&nbsp;('.$option_name.')
               </A>-->
					   </td>';
				}
				else
				{
					echo'<td align="left">
							<INPUT TYPE="radio"  name="vehicle_id" VALUE="'.$vehicle_id.'">
						</td>
						<td class=\'text\'>
						  <font color="grey">'.$vehicle_name.'</font>
							<!--&nbsp;<A HREF="#" style="text-decoration:none;" onclick="main_vehicle_information('.$vehicle_id.')">
              '.$vehicle_name.'&nbsp;('.$option_name.')</A>-->
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
								$query="SELECT vehicle_id FROM station_assignment WHERE vehicle_id='$vehicle_id' AND status='1'";
								//echo "query=".$query;
								$result=mysql_query($query,$DbConnection);
								$num_rows=mysql_num_rows($result);
								//if($num_rows==0)
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
