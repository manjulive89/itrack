<?php  
	include_once('util_session_variable.php');	
	include("user_type_setting.php");	
	$path = $_SERVER['SCRIPT_NAME'];	
	$url_substr = explode('/',$path);		
	$size = sizeof($url_substr);
	$interface = $url_substr[$size-1];				
	$div_height="<div style='height:2px;'></div>";		
	$set_nbsp="&nbsp;";		
	$img_size='width="15px" hight="14px"';
	$query="SELECT name from account_detail where account_id='$account_id'";	
	$result=mysql_query($query,$DbConnection);	
	$row=mysql_fetch_object($result);
	$user_name=$row->name;
	//echo"interface=".$interface;
	echo "<input type='hidden' id='vehicle_milstone'>";
	echo "<table border='0' width='100%' cellpadding='0' cellspacing='0' height='100%' class='frame_header_table'>  
			<tr>
				<td width='2%'>&nbsp;&nbsp;<img src='images/icon/welcome.png'".$img_size." style='border:none;'></td>
				<td align='left' width='17%'><font color='blue'>Welcome </font><font color='green'>&nbsp;:&nbsp;".$user_name."</font></td>";
			//if($interface=="home.php" || $interface=="live.php")
			if($interface=="home.php" || $interface=="live.php")
			{
				if($size_utype_session>1)
				{
				echo'<input type="hidden" id="default_category" value="'.$user_typeid_array[0].'">';
				echo"<td align='right'>Category&nbsp;:&nbsp;</td>
					<td>
						<select id='category' onchange='javascript:setDisplayOption(this.value);'>";													
							for($i=0;$i<$size_utype_session;$i++)
							{	echo'<option value="'.$user_typeid_array[$i].'">'.$user_type_name_session[$i].'</option>';	}
					echo"</select>    
					</td>";
				}
				if($size_utype_session==1)
				{	echo '<input type="hidden" id="category" value="'.$user_typeid_array[0].'">';	}
	
				echo"<td align='right'>&nbsp;&nbsp;Display Option&nbsp;:&nbsp;</td>
					<td>
						<select id='user_type_option' style='font-size:10px' onchange='javascript:show_main_home_vehicle(this.value);'>
							<option value='all'>All</option>					<option value='group'>By Group</option>		<option value='user'>By User</option>
							<option value='vehicle_tag'>By Vehicle Tag</option>	<option value='vehicle_type'>By Vehicle Type</option>
							<!--<option value='vehicle'>By Vehicle</option>-->	
						</select>    
					</td>";	
			}
				
        //if($_SERVER["HTTP_X_FORWARDED_FOR"] == "172.26.48.189")
        //{
          /*if( ($group_id == "0051") || ($group_id == "0004") )
          {
            echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<blink><font color=blue><strong>Welcome!</strong></font>&nbsp;&nbsp; <font color=red><strong>Mr Partha Sen Gupta</strong> &nbsp;(VP RM ,Tata Steel Ltd.)</font></blink></td>';
          } */
        //}
        
        if($interface == "live.php")
        {
           echo '
				<td>
					<select id="mode_selector" style="font-size:10px" onchange="javascript:select_mode_dropdown(this.form);">
						<option value="1">Map Mode</option>
						<option value="2">Text Mode</option>							
					</select>    
				</td>				
				<td align="right">
					<a href="javascript:show_live_vehicles_hide_div();" style="text-decoration:none;">
						Select vehicle
					</a>
				</td>
				<td>					
					&nbsp;&nbsp;
					<span id="ref_time" style="font-size:x-small;color:red;"></span>
						&nbsp;&nbsp;
						<input type="checkbox" checked id="trail_path">
							<span style="font-size:x-small;color:green;">
								Add Arrow
							</span>
							&nbsp;&nbsp;
						<input type="checkbox" id="trail_path_real"><span style="font-size:x-small;color:green;">
						Add Trail
					</span>
				</td>';
        }
        
        echo'<td align="right">
					<table class="frame_header_table" border="0" cellspacing=0 cellpadding=0>
						<tr>';	           
							if($user_id=='lab')
							{
							echo'<td>
									<table>
										<tr>
											<td height="3px"></td>
										</tr>
										<tr>											
												<a href="home.htm" style="text-decoration:none;" target="_blank">
													<img src="images/icon/lab.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
												</a>
											</td>
										</tr>
									</table>
								</td>							
								<td '.$v_align.'>';	
								if($interface == "lab/index.php")
								{
									echo '<b class="hs1">Lab</b>';
								} 
								else
								{ 
									echo '<a href="lab/index.htm" class="hs2" target="_blank">Lab</a> ';
								}
							echo'</td>';
							echo'<td>'.$set_nbsp.'|'.$set_nbsp.'</td>';
							}            						

							echo'<td>
									<table cellspacing=0 cellpadding=0>
										<tr>
											<td height="3px"></td>
										</tr>
										<tr>
											<td>
												<a href="home.htm" style="text-decoration:none;">
													<img src="images/icon/home1.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
												</a>
											</td>
										</tr>
									</table>									
								</td>							
								<td '.@$v_align.'>';	
								if($interface == "home.php")
								{
									echo '<b class="hs1">Home</b>';
								} 
								else
								{ 
									echo '<a href="home.htm" class="hs2">Home</a> ';
								}
								echo'</td>';

								echo'<td>
										'.$set_nbsp.'|'.$set_nbsp.'
									</td>
									<td>
										<table cellspacing=0 cellpadding=0>
											<tr>
												<td height="3px"></td>
											</tr>
											<tr>
												<td>
													<a href="live.htm" style="text-decoration:none;">
														<img src="images/icon/live.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
													</a>
												</td>
											</tr>
										</table>
									</td>
									<td '.@$v_align.'>';	
										if($interface == "live.php")
										{
											echo '<b class="hs1">Live</b>';
										} 
										else
										{ 
											echo '<a href="live.htm" class="hs2">Live</a>';
										}
								echo'</td>';
								if($session_user_permission==1)
								{
									echo'<td>
											'.$set_nbsp.'|'.$set_nbsp.'
										</td>
										<td>
											<table cellspacing=0 cellpadding=0>
												<tr>
													<td height="3px"></td>
												</tr>
												<tr>
													<td>												
														<a href="manage.htm" style="text-decoration:none;">
															<img src="images/icon/manage.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
														</a>
													</td>
												</tr>
											</table>
											</td>							
										<td '.@$v_align.'>';	
											if($interface == "manage.php")
												{
													echo '<b class="hs1">Manage</b>';
												}
												else
												{ 
													echo '<a href="manage.htm" class="hs2">Manage</a>';
												}
									echo'</td>';
								}
									echo'<td>
											'.$set_nbsp.'|'.$set_nbsp.'
										</td>
										<td>
											<table cellspacing=0 cellpadding=0>
												<tr>
													<td height="3px"></td>
												</tr>
												<tr>
													<td>
														<a href="report.htm" style="text-decoration:none;">
															<img src="images/icon/report2.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
														</a>
													</td>
												</tr>
											</table>
										</td>
										<td '.@$v_align.'>';	
											if($interface == "report.php")
											{
												echo '<b class="hs1">Report</b>';
											} 
											else
											{ 
												echo '<a href="report.htm" class="hs2">Report</a>';
											}
									echo'</td>';

									if($session_user_permission==1)
									{	
									echo'<td>
										'.$set_nbsp.'|'.$set_nbsp.'
										</td>';
										echo'<td '.@$v_align.'>
												<table cellspacing=0 cellpadding=0>
													<tr>
														<td height="3px"></td>
													</tr>
													<tr>
														<td>
															<a href="setting.htm" style="text-decoration:none;">
																<img src="images/icon/setting1.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
															</a>
														</td>
													</tr>
												</table>
											</t>
											<td>';  
                       if(@$school_user_type==1)
                       {
                       
                       }
                       else
                       {
                          if($interface == "setting.php")
      										{
      											echo '<b class="hs1">Setting</b>';
      										} 
      										else
      										{ 
      											echo '<a href="setting.htm" class="hs2">Setting</a>';
      										}
                       }                           
										
									echo'</td>';
									}
									echo'<td>
											'.$set_nbsp.'|'.$set_nbsp.'
										</td>
										<td>
											<table cellspacing=0 cellpadding=0>
												<tr>
													<td height="3px"></td>
												</tr>
												<tr>
													<td>
														<a href="help.htm" style="text-decoration:none;">
															<img src="images/icon/help1.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
														</a>
													</td>
												</tr>
											</table>
										</td>
										<td '.@$v_align.'>';	
											if($interface == "help.php")
											{
												echo '<b class="hs1">Help</b>';
											}
											else
											{ 
												echo '<a href="help.htm" class="hs2">Help</a>';
											}
									echo'</td>
											<td>
												'.$set_nbsp.'|'.$set_nbsp.'
											</td>';						
									echo'<td>
											<table cellspacing=0 cellpadding=0>
												<tr>
													<td height="3px"></td>
												</tr>
												<tr>
													<td>
														<a href="logout.htm" style="text-decoration:none;">
															<img src="images/icon/logout.png" '.$img_size.' style="border:none;">'.$set_nbsp.'
														</a>
													</td>
												</tr>
											</table>
										</td>
										<td '.@$v_align.'>';
										echo '<a href="logout.htm" class="hs2">
													Logout
												</a>&nbsp;
										</td>
									</tr>
								</table>
							</td>
						</tr>
				</table> '; 		
	?>  				  
    
