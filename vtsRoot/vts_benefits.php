<?php  
  include_once('src/php/Hierarchy.php');  
  include_once('src/php/util_session_variable.php');
  include_once("src/php/util_php_mysql_connectivity.php");  

  $currentdate=date("Y-m-d");
	list($currentyear,$currentmonth,$currentday)=split("-",$currentdate);  
	

	
?>
<html>
  <head>
  
  <style type="text/css">
      
      /* attributes of the container element of textbox */
      .loginboxdiv{
      margin:0px;
      height:21px;
      width:146px;
      background:url(images/flash_images/login_bg.gif) no-repeat bottom;
      }
      /* attributes of the input box */
      .loginbox
      {
      background:none;
      border:none;
      width:134px;
      height:15px;
      margin:0;
      padding: 2px 7px 0px 7px;
      font-family:Verdana, Arial, Helvetica, sans-serif;
      font-size:11px;
      }

      
  </style>
  
  
    <?php
      include('src/php/main_frame_part1.php')
    ?> 

    <script type="text/javascript">     
    if (document.addEventListener) 
    {
      document.addEventListener("DOMContentLoaded", init, false);
    }
    
   
   
    </script>
  </head>

<body onload="javascript:callpageheight();">
  <?php
    if($account_id)
    {
      echo"<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=home.php\">";
    }
    else
    {
  ?>
	<form name="myform" method = "post" action ="login.php" onSubmit="javascript:return index_validate_form(myform)">
	
	
    <input name="width" class="" value="" type="hidden">
    <input name="height" class="" value="" type="hidden">
    <input name="resolution" class="" value="" type="hidden">
  
    <div id="topmaindiv" class="main" align=center > <!-- for background color --> 
			<div id="topheaderdiv" class="header">
				<div id="myBoxA">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">								  
						<tr> 
							<td colspan="2" valign="top"> 
								<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" bgcolor="#F8F8F8">    
									<tr>
										<td>
											<table>
												<tr>							
													<td align="left">&nbsp;<img src="images/IES1.png" style="border: medium none;" ></td>						
													<td align="left"><img src="images/companyname3.png" style="border: medium none;">
												</tr>
											</table>
										</td>
										
										<td align="right" valign='top'>
											<table border="0" cellspacing="2" cellpadding="2" class="menu"> 
												<tr>													
													<td><font color="#2B3A94"><b>Group ID</b></font></td>
													<td>:</td>
													<td> 
                               <div class="loginboxdiv">
                                <input name="group_id" class="loginbox"  type="text" />
                                </div>
                          </td>
													<td colspan="4" valign='top' align='right'><img src="images/flash_images/itrack-track.png" /></td>
												</tr>
												<tr>
													<td><font color="#2B3A94"><b>User ID</b></font></td>
													<td >:</td>
													<td>
                             <div class="loginboxdiv">
                                <input name="user_id" class="loginbox"  type="text" />
                              </div>
                          </td>
													<td><font color="#2B3A94"><b>Password</b></font></td>
													<td>:</td>
													<td>
                            <div class="loginboxdiv">
                                <input name="password" class="loginbox"  type="password" />
                              </div>
                          </td>
													<td><input value="Sign In" type="submit" ></td>
												</tr>						
											</table>  
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<img src="images/flash_images/lineupper.png" />
							</td>
						</tr>
						
						
						<tr>
							<td>
								<table valign="top" align="center" border="0" cellpadding="0" cellspacing="1" bgcolor="" width="100%">


<tbody><tr valign="top">
    
      <td style="margin: 2px;" width=600px >
	         <div class="allpageheading2">Vehicle Tracking Systems (VTS) Benefits</div> 
           <ol class="allpagesb_menu3a">
           
                <li style="margin: 3px; text-align:justify;"> 
                  <div class="allpagearticle">
                                 
                                    <div align=center>
                                    <table class="allpagesb_menu3" >
                          				
                          						<tr>
                          							<td >
                                              <p>Some important benefits of VTS</p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                  1. Vehicle tracking systems can help to reduce running costs by specifically targeting those who speed and waste fuel. By focussing upon these drivers it is possible to not only reduce fuel and maintenance bills, but to also reduce insurance premiums. 

                                              </p>
                                              
                                              
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                              
                                                 2. Some insurance companies will offer around a thirty percent discount to companies who implement a GPS vehicle tracking system. This is not only because it encourages safer driving, but also helps recovery if thefts do occur. 

                                              </p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                3.  Productivity of workers can be increased by being able to keep track of lunch hours, exposing unauthorized stops and breaks and by evaluating the overtime requests of workers. By having detailed information on the whereabouts of vehicles at all times, it is far easier to keep an eye on employee activities. 
                                              </p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                4. GPS devices help businesses to become more customers friendly. For instance, a cab company that is using a vehicle tracking system can tell a customer exactly where the nearest cab is and give a realistic estimate on how long it will be
                                              </p>
                                              
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                               5. Business owners can find their most productive employees and use this information to implement further training or even a system of bonuses to enhance staff members' work ethic.      
                                              </p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                6. Vehicle tracking systems will vastly reduce your phone bills as it is no longer a necessity to constantly call employees to find their location 
                                              </p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                 7. By having all the relevant information on one screen, those running the software have easy access to answer enquiries rapidly and accurately.
                                              </p>
                                              <p style="text-align:justify;" class="allpagesb_menu3b">
                                                  8. GPS systems reduce the amount of paperwork that drivers must fill out. By doing this you not only soften the blow of introducing such a system, but also increase the accuracy of your records. 
                                              </p>
                                                <p style="text-align:justify;" class="allpagesb_menu3b">
                                                9. By having detailed information on the whereabouts of all employees, business owners are far more in touch with their business operations. Meaning they have greater levels of control over their company. 
                                                </p>
                                        </td>
                          						
                          						</tr>
                          									
                          				
                                  </table>           
                                   </div> 
                                  
                                  </div>
                </li> 
               
				    </ol> 
        </td>
     
        
        <td>
          <table  border=0  >
            <tr >
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td style="font-size:18px; font-family:Bodoni MT; font-smooth: always;font-weight:bold;font-style:italic;  color:#FF6347;text-align:center;">Vehicle Tracking System<br><br></td>
            </tr> 
            <tr>
              <td>
              
                <table class="allpageheadingItalic">
                  <tr>
                    <td> &#149;</td><td>GPS/GPRS Based Tracking System</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Alert Notifications</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Water Proof Enclosure</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Internal Memory for data storage</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Distance Calculation</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Track on Mobile</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Built in Antenna</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Over Voltage Protection (Optional)</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>Backup Battery (Optional)</td>
                  </tr>
                  <tr>
                    <td> &#149;</td><td>IO's(Optional)(Fuel,Ignition,..)</td>
                  </tr>
                </table>
              </td>
              
              
              <td>
             <table class="allpageheadingItalic1">
                  <tr>
                    <td>&nbsp;</td><td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td> <img src="images/flash_images/contactus.jpg" width="70px" /></td><td><a href="contactus.php" style="text-decoration:none">Contact us</a></td>
                  </tr>
                  <tr>
                     <td>  <img src="images/flash_images/distributors.jpg" width="50px"/></td><td><a href="distributors.php" style="text-decoration:none">Distributors</a></td>
                  </tr>
                  <tr>
                    <td><img src="images/flash_images/askquery.jpg"width="70px" /></td><td><a href="querycontact.php" style="text-decoration:none" >Ask Query</a></td>
                  </tr>
                  <tr>
                    <td><img src="images/flash_images/aboutus.jpg"width="60px" /></td><td><a href="index.php" style="text-decoration:none" >Home</a></td>
                  </tr>
              
              </table>
        </td>
        
              
            </tr>
            
            <tr>
            <td colspan=2 align=center>
            <br>
                <img src="images/general/benefits.jpg"  style="text-align=center">
                <br>
            </td>
            </tr>
          </table>
        </td>
          
        
        
      </tr>
 
</tbody></table>
							</td>
						</tr>	
            
            </tr>
						<tr>
            <td>	<img src="images/flash_images/linebottom.png" /> </td>
            </tr>				

	<tr>
		<td>
			<table width=100% class="menu" bgcolor="EAEAEA">
				<tr valign=top>
					<td align='center' class="allpageheadingfooter">				
					Innovative Embedded Systems provides full service for hardware and firmware design and prototyping for micro controller and embedded systems
					</td>
				</tr>
				<tr valign=top>
					<td align='right' class="allpageheadingfooter">				
						&copy;IESPL All Right Reserved (2005-2012)&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;Visit <a href="http://www.iembsys.co.in" style="text-decoration:none"><b>iembsys.com</b></a>&nbsp; &nbsp;&nbsp;&nbsp;
					</td>
				</tr>
			</table> 
		</td>
	</tr>
							
					</table>
				</div>
			</div>
		</div> 
	</form>
  <?php
  }
  ?>

<?php
mysql_close($DbConnection);
?>

</body></html>
