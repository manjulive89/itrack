<?php
set_time_limit(6000);
//include_once('util_session_variable.php');
//include_once('util_php_mysql_connectivity.php'); 
$HOST = "localhost";
$DBASE = "iespl_vts_beta";
$USER = "root";
$PASSWD = "mysql";
//echo "\nDBASE=".$DBASE." ,User=".$USER." ,PASS=".$PASSWD;
$DbConnection = mysql_connect($HOST,$USER,$PASSWD) or die("Connection to server is down. Please try after few minutes.");
mysql_select_db ($DBASE, $DbConnection) or die("could not find DB");

$abspath = "/var/www/html/vts/beta/src/php";

include_once($abspath."/get_all_dates_between.php");
include_once($abspath."/sort_xml.php");
include_once($abspath."/calculate_distance.php");
include_once($abspath."/report_title.php");
include_once($abspath."/read_filtered_xml.php");
//include_once($abspath."/get_location.php");
include_once($abspath."/user_type_setting.php");
include_once($abspath."/select_landmark_report.php");
include_once($abspath."/area_violation/check_with_range.php");
include_once($abspath."/area_violation/pointLocation.php");
require_once $abspath."/excel_lib/class.writeexcel_workbook.inc.php";
require_once $abspath."/excel_lib/class.writeexcel_worksheet.inc.php";
include_once($abspath."/util.hr_min_sec.php");

//include_once($abspath."/mail_action_report_distance_1.php");
//include_once("mail_action_report_halt2.php");
//include_once($abspath."/mail_action_report_travel_1.php");

function tempnam_sfx($path, $suffix)
{
	do
	{
		$file = $path.$suffix;
		$fp = @fopen($file, 'x');
	}
	while(!$fp);
	fclose($fp);
	return $file;
}

//echo "report4\n";
$csv_string_dist = "";                //INITIALISE  DISTANCE VARIABLES
$csv_string_dist_arr = array();
$sno_dist = 0;
$overall_dist = 0.0;

$csv_string_halt = "";                //INITIALISE  HALT VARIABLES
$csv_string_halt_arr = array();
$total_halt_dur = 0;
$sno_halt = 0;

$csv_string_travel = "";                //INITIALISE  TRAVEL VARIABLES
$csv_string_travel_arr = array();
$total_travel_dist = 0;
$total_travel_time = 0;
$sno_travel = 0;
                        
$query_assignment = "SELECT DISTINCT vehicle.vehicle_id,vehicle.vehicle_name FROM vehicle,vehicle_assignment,vehicle_grouping WHERE ".
                    "vehicle.vehicle_id = vehicle_assignment.vehicle_id AND vehicle_assignment.vehicle_id = vehicle_grouping.vehicle_id AND ".
                    "vehicle_grouping.account_id=231 AND vehicle.status=1 AND vehicle_assignment.status=1 AND vehicle_grouping.status=1 limit 50";              

//echo "\nquery=".$query_assignment."\n";
$result_assignment = mysql_query($query_assignment,$DbConnection);

//$count=0;

while($row_assignment = mysql_fetch_object($result_assignment))
{
  //$vname = $row_assignment->vehicle_name;  
  //if($vname == "DL1M6175" || $count<7)
  //{    
     $vehicle_id_a = $row_assignment->vehicle_id;
     $vehicle_name[] = $row_assignment->vehicle_name;
     $vehicle_name[] = $vname;
     
     $query_imei = "SELECT device_imei_no FROM vehicle_assignment WHERE vehicle_id ='$vehicle_id_a' AND status=1";
     $result_imei = mysql_query($query_imei, $DbConnection);
     $row_imei = mysql_fetch_object($result_imei);
     $device_imei_no[] = $row_imei->device_imei_no;
     $vid[] = $vehicle_id_a; 
     //$count++;
  //} 
}

/*$device_str = $_POST['vehicleserial'];
//echo "<br>devicestr=".$device_str;
$vserial = explode(':',$device_str);   */
$vsize=count($device_imei_no);

$current_date = date('Y-m-d');
//$previous_date = date('Y-m-d', strtotime($date .' -1 day'));
//$current_date = date('Y-m-d', strtotime($date .' -2 day')); // LAST 4 DAYS : -2, -3, -4, -5

///////////////////////////////////////////
$startdate = $current_date." 03:00:00";      // MORNING TIME: 3AM TO 6PM
$enddate = $current_date." 18:00:00"; 
///////////////////////////////////////////

$date1 = $startdate;
$date2 = $enddate;
$user_interval = "5";   //FIVE MINUTES

//$user_interval = 30*60;
//echo "user_interval=".$user_interval."<br>";
///////////////////////////////////////////////////////////////////////////////
//echo "<br>vsiz=".$vsize;

$station_id = array();
$type = array();
$customer_no = array();
$station_name = array();
$station_coord = array();
$distance_variable = array();
$google_location = array();


//##### GET STATION COORDINATES ###############
$query2 = "SELECT DISTINCT station_id,type,customer_no,station_name,station_coord,distance_variable,google_location FROM station WHERE ".
            "user_account_id=231 AND status=1";
//echo "\nQQQQQQQQQQ2=".$query2;
$result2 = mysql_query($query2,$DbConnection); 

while($row2 = mysql_fetch_object($result2))
{
  $station_id[] = $row2->station_id;
  $type[] = $row2->type;
  $customer_no[] = $row2->customer_no;
  $station_name[] = $row2->station_name;
  $station_coord[] = $row2->station_coord;
  $distance_variable[] = $row2->distance_variable;
  
  //$google_location[] = $row2->google_location;
  //$google_location[] = $placename1;
}   
//##############################################

if($vsize>0)
{
  echo "\nStations Size ::".sizeof($station_id); 
  include_once("mail_action_report_halt2.php"); 
  //echo "\nAfter"; 
  write_report($device_imei_no, $vid, $vehicle_name, $user_interval);
}
    

function write_report($vserial, $vid, $vname, $user_interval)
{
  //echo "\nIn Report";
  global $DbConnection;
  global $startdate;
  global $enddate;
  global $sno;
  global $overall_dist;
    
  global $csv_string_dist;
  global $csv_string_dist_arr;
  global $sno_dist;
  global $overall_dist;
    
  global $csv_string_halt;
  global $csv_string_halt_arr;
  global $total_halt_dur;
  global $sno_halt;
    
  global $csv_string_travel;
  global $csv_string_travel_arr;
  global $total_travel_dist;
  global $total_travel_time;
  global $sno_travel; 
  
  global $total_halt_dur; 
  
  $maxPoints = 1000;
  $file_exist = 0;
  
  //global $csv_string;
  //global $csv_string_arr;  
  echo "\nTotal Vehicles=".sizeof($vserial);
  	    

	for($i=0;$i<sizeof($vserial);$i++)
	{  	            
		$query1 = "SELECT vehicle_name FROM vehicle WHERE ".
		"vehicle_id IN(SELECT vehicle_id FROM vehicle_assignment ".
		"WHERE device_imei_no='$vserial[$i]' AND status=1) AND status=1 limit 2";
		//echo "<br>".$query1;
		//echo "<br>DB=".$DbConnection;
		$result = mysql_query($query1,$DbConnection);
		$row = mysql_fetch_object($result);
		$vname[$i] = $row->vehicle_name;  
		//echo "\n vname=".$vname[$i];
		 
		//GET DISTANCE DATA
		$csv_string_dist = "";
		//$overall_dist = 0.0;
		$sno_dist = 1;
		
		/*get_distance_xml_data($vserial[$i], $vid[$i], $vname[$i], $startdate, $enddate, $user_interval);	
		$csv_string_dist = $csv_string_dist.'#Total,'.$startdate.','.$enddate.','.$overall_dist; 
		echo "\nDISTANCE::vserial->  ".$vid[$i]." vname->".$vname[$i]." ".$csv_string_dist;
		$csv_string_dist_arr[$i] = $csv_string_dist;
		$csv_string_dist="";    
		$sno_halt = 1; */
		  		
		$total_halt_dur = 0;
		
    //echo "\nSerial = ".$s." :Vehicle:".$vname[$i]." ::Before";
    
    get_halt_xml_data($vserial[$i], $vid[$i], $vname[$i], $startdate, $enddate, $user_interval);
		
		/*$hms_2 = secondsToTime($total_halt_dur);
		$hrs_min = $hms_2[h].":".$hms_2[m].":".$hms_2[s];	
		$csv_string_halt = $csv_string_halt.'#Total,-,-,-,-,'.$hrs_min; */
		//echo "\n\nHALT::vserial->  ".$vid[$i]." vname->".$vname[$i].", ".$csv_string_halt;
		$csv_string_halt_arr[$i] = $csv_string_halt;
		$csv_string_halt="";   
		
		$s = $i+1;
		echo "\nSerial = ".$s." :Vehicle:".$vname[$i]." ::Completed";
    /*$sno_travel = 1;
		
		get_travel_xml_data($vserial[$i], $vid[$i], $vname[$i], $startdate, $enddate, $user_interval);
		$hms_2 = secondsToTime($total_travel_time);
		$hrs_min = $hms_2[h].":".$hms_2[m].":".$hms_2[s];	
		
		$csv_string_travel = $csv_string_travel.'#Total,-,-,-,-,'.$total_travel_dist.','.$hrs_min;
		$total_travel_time = 0;
		$total_travel_dist = 0;
		echo "\nTRAVEL::vserial->  ".$vid[$i]." vname->".$vname[$i].", ".$csv_string_travel;
		$csv_string_travel_arr[$i] = $csv_string_travel; 
		$csv_string_travel="";  */  
	}  	
}

   
  $vehicle_id_final = "";
  $csv_string_dist_final ="";
  $csv_string_halt_final ="";
  $csv_string_travel_final =""; 
  $vname_str ="";
  
	for($j=0;$j<sizeof($vid);$j++)
	{    
		$vehicle_id_final = $vehicle_id_final.$vid[$j].",";
		$vehicle_name_final = $vehicle_name_final.$vehicle_name[$j].",";
		$device_imei_no_final = $device_imei_no_final.$device_imei_no[$j].",";
		//$alert_id_final = $alert_id_final.$alert_db;
		//$csv_string_dist_final = $csv_string_dist_final.$csv_string_dist_arr[$j]."@";
		$csv_string_halt_final = $csv_string_halt_final.$csv_string_halt_arr[$j]."@";
		//$csv_string_travel_final = $csv_string_travel_final.$csv_string_travel_arr[$j]."@"; 		       		
  }
  $vehicle_id_final = substr($vehicle_id_final, 0, -1);
  $vehicle_name_final = substr($vehicle_name_final, 0, -1);
  $device_imei_no_final = substr($device_imei_no_final, 0, -1);
  //$csv_string_dist_final = substr($csv_string_dist_final, 0, -1);
  $csv_string_halt_final = substr($csv_string_halt_final, 0, -1);
  //$csv_string_travel_final = substr($csv_string_travel_final, 0, -1); 
    
    
  //echo "\nOutVehicleID";
  if($vehicle_id_final!="")
  {
    //echo "\nInVehicleID";
		//$inc_serial=$i+1;
		$inc_serial = rand();
		$filename_title = 'VTS_HALT2_REPORT_MOTHER_DELHI_MORNING_'.$previous_date."_".$inc_serial;
		$fullPath = "/var/www/html/vts/beta/src/php/download/".$filename_title;
		$fname = tempnam_sfx($fullPath, ".xls");
		$workbook =& new writeexcel_workbook($fname);
		
		$border1 =& $workbook->addformat();
		$border1->set_color('white');
		$border1->set_bold();
		$border1->set_size(9);
		$border1->set_pattern(0x1);
		$border1->set_fg_color('green');
		$border1->set_border_color('yellow');
		
		$text_format =& $workbook->addformat(array(
			bold    => 1,
			//italic  => 1,
			//color   => 'blue',
			size    => 10,
			//font    => 'Comic Sans MS'
		));
												
    $blank_format = & $workbook->addformat();
    $blank_format->set_color('white');
    $blank_format->set_bold();
    $blank_format->set_size(12);
    $blank_format->set_merge();
    			
		$vname_label = explode(',',$vehicle_name_final);     //**TOTAL VEHICLES 		
	
		$worksheet2 =& $workbook->addworksheet('Halt Report');
		$sheet2 = explode('@',$csv_string_halt_final);
		$r=0;   //row 
                
		$worksheet2->write($r, 0, "Vehicle", $text_format);
    $worksheet2->write($r, 1, "SNo", $text_format);
		$worksheet2->write($r, 2, "Location", $text_format);
		$worksheet2->write($r, 3, "Station No", $text_format);
		$worksheet2->write($r, 4, "Type", $text_format);
		$worksheet2->write($r, 5, "Arrival Date", $text_format);
		$worksheet2->write($r, 6, "Arrival Time", $text_format);
		$worksheet2->write($r, 7, "Departure Date", $text_format);
		$worksheet2->write($r, 8, "Departure Time", $text_format);
		$worksheet2->write($r, 9, "Halt Duration (Hr:min:sec)", $text_format); 
		$worksheet2->write($r, 10, "Latitude", $text_format);
		$worksheet2->write($r, 11, "Longitude", $text_format);
		
    //echo "\nSheet=".sizeof($sheet2);
    $r++;
    for($p=0;$p<sizeof($sheet2)-1;$p++)
		{
			//echo "vehicle_name=".$vname_label[$p]."<br><br>escalation_id=".$escalation_id[$i]."<br><br>csv_string_dist_final".$sheet2[$p]."<br><br>";
			//$report_title_halt = "Halt Report -Vehicle:".$vname_label[$p]." -($date1 to $date2)";   //COMMENTED ON REQ
			//echo "report_title_halt=".$report_title_halt."<br>";			
			/*$worksheet2->write      ($r, 0, $report_title_halt, $border1);
			for($b=1;$b<=6;$b++)
			{
				$worksheet2->write_blank($r, $b, $border1);
			} */                  
			//$r++;
			/*$worksheet2->write($r, 0, "SNo", $text_format);
			$worksheet2->write($r, 1, "Location", $text_format);
			$worksheet2->write($r, 2, "Station No", $text_format);
			$worksheet2->write($r, 3, "Arrival Time", $text_format);
			$worksheet2->write($r, 4, "Departure Time", $text_format);
			$worksheet2->write($r, 5, "Halt Duration (Hr:min:sec)", $text_format); 
			$r++;	*/				
			$data_flag = false;
			
      $sheet2_row = explode('#',$sheet2[$p]);        
			//echo "\nSheetRowSize=".sizeof($sheet2_row);
      
      for($q=0;$q<sizeof($sheet2_row);$q++)
			{
				$data_flag = false;
        $sheet2_data_main_string="";
				$sheet2_data = explode(',',$sheet2_row[$q]);
				
				//echo "\nRow=".$q." ,SizeCol=".sizeof($sheet2_data);
				
				for($m=0;$m<sizeof($sheet2_data);$m++)
				{           
					/*if($sheet2_data[$m]==null)
					{
					 echo "\nNull found";
          }
					if($sheet2_data[$m]=="")
					{
					 echo "\nNone found";
          }
					if($sheet2_data[$m]==" ")
					{
					 echo "\nBlank Space found";
          } */
          
          $c=0;
          if( ($sheet2_data[$m]!="") && ($sheet2_data[$m]!=" ") && ($sheet2_data[$m]!=null) )
					{					 
            //$worksheet2->write($r,$m, $sheet2_data[$m]);
            if($m==5 || $m==6)
            {
              $datetime_tmp = explode(" ",$sheet2_data[$m]);
              $worksheet2->write($r,$c, $datetime_tmp[0]);
              $c++;
              $worksheet2->write($r,$c, $datetime_tmp[1]);
              $c++;
            }
            else
            {
              $worksheet2->write($r,$m, $sheet2_data[$m]);
              $c++;
            }            
					  $sheet2_data_main_string=$sheet2_data_main_string.$sheet2_data[$m].",";
					  $data_flag = true;
          }      
				}
				//echo "sheet2_data_main_string=".$sheet2_data_main_string."<br>";
				if($data_flag)
				{
          //echo "\nRow incremented";
          $r++;
        } 
			} 
		}		
   
  $workbook->close(); //CLOSE WORKBOOK
  //echo "\nWORKBOOK CLOSED"; 
  
  //########### SEND MAIL ##############//
  $to = 'rizwan@iembsys.com';
  //$to = 'Amit.Patel@motherdairy.com,Ravindra.Negi@motherdairy.com,Vijay.Singh@motherdairy.com,vivek.chahal@motherdairy.com';
  //$to = 'jyoti.jaiswal@iembsys.com';
  $subject = 'VTS_HALT2_REPORT_MOTHER_DELHI_MORNING_'.$current_date;
  $message = 'VTS_HALT2_REPORT_MOTHER_DELHI_MORNING_'.$current_date; 
  $random_hash = md5(date('r', time()));  
  $headers = "From: support@iembsys.co.in\r\n";
  //$headers .= "Cc: jyoti.jaiswal@iembsys.com,omvrat@iembsys.com,avanendra@iembsys.com"; 
  $headers .= "Cc: rizwan@iembsys.com";
  //$headers .= "Cc: jyoti.jaiswal@iembsys.com";
  $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
  $filename_title = $filename_title.".xls";
  $file_path = $fullPath.".xls";
  
  //echo "\nFILE PATH=".$file_path;  
  include_once("send_mail_api.php");
  //####################################//
  
  unlink($file_path); 
}
 
?>
