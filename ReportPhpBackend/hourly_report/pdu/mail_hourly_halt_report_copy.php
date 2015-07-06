<?php
set_time_limit(18000);
//include_once('util_session_variable.php');
//include_once('util_php_mysql_connectivity.php');

//$HOST = "111.118.181.156";
include_once("../database_ip.php");
$DBASE = "iespl_vts_beta";
//$HOST = "localhost";
$USER = "root";
$PASSWD = "mysql";
$account_id = "231";
//if($account_id == "231") $user_name = "delhi";
if($account_id == "231") $user_name = "delhi@";
echo "\nDBASE=".$DBASE." ,USER=".$USER." ,PASS=".$PASSWD;
$DbConnection = mysql_connect($HOST,$USER,$PASSWD) or die("Connection to server is down. Please try after few minutes.");
mysql_select_db ($DBASE, $DbConnection) or die("could not find DB");

//$abspath = "D:\\test_app";
$abspath = "/var/www/html/vts/beta/src/php";
include_once($abspath."/common_xml_element.php");
include_once($abspath."/get_all_dates_between.php");
include_once($abspath."/sort_xml.php");
include_once($abspath."/calculate_distance.php");
include_once($abspath."/report_title.php");
include_once($abspath."/read_filtered_xml.php");
include_once($abspath."/user_type_setting.php");
//include_once($abspath."/select_landmark_report.php");
//include_once($abspath."/area_violation/check_with_range.php");
//include_once($abspath."/area_violation/pointLocation.php");
require_once $abspath."/excel_lib/class.writeexcel_workbook.inc.php";
require_once $abspath."/excel_lib/class.writeexcel_worksheet.inc.php";
include_once($abspath."/util.hr_min_sec.php");
//include_once($abspath."/hourly_report/".$user_name."/get_master_detail.php");

//### IMPORT XLSX LIBRARY
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once $abspath.'/PHPExcel/IOFactory.php';

$objPHPExcel_1 = null;

echo "TEST1";
include_once("read_master_file.php"); 
include_once("read_sent_file.php");
//include_once("update_sent_file.php");
include_once("create_hrly_excel_file.php");
include_once("action_hourly_report_halt.php");

include_once("create_last_halt_time.php");
include_once("read_last_halt_time.php");
include_once("read_last_processed_time.php");
include_once("update_last_halt_time.php");

include_once("update_last_processed_time.php");
include_once("delete_file.php");

$sent_root_path = $abspath."/hourly_report/".$user_name."/sent_file";
echo "\nSent_RootPath=".$sent_root_path;

$evening_sent_file_path1 = $sent_root_path."/HOURLY_MAIL_VTS_HALT_REPORT_EVENING_MOTHER_DELHI_CASH_ROUTE.xlsx";
$evening_sent_file_path2 = $sent_root_path."/HOURLY_MAIL_VTS_HALT_REPORT_EVENING_MOTHER_DELHI_FOCAL_ROUTE.xlsx";
$morning_sent_file_path = $sent_root_path."/HOURLY_MAIL_VTS_HALT_REPORT_MORNING_MOTHER_DELHI.xlsx";

$evening_last_processed_time_path1 = $sent_root_path."/evening_last_processed_time_1.xlsx";
$evening_last_processed_time_path2 = $sent_root_path."/evening_last_processed_time_2.xlsx";
$morning_last_processed_time_path = $sent_root_path."/morning_last_processed_time.xlsx";

$evening_last_halt_time_path1 = $sent_root_path."/evening_last_halt_time_1.xlsx";
$evening_last_halt_time_path2 = $sent_root_path."/evening_last_halt_time_2.xlsx";
$morning_last_halt_time_path = $sent_root_path."/morning_last_halt_time.xlsx";

//echo "TEST2";
include_once("get_customer_db_detail.php");
include_once("get_route_db_detail.php");
//include_once("process_data.php");

$date = date('Y-m-d');
//$pdate = date('Y-m-d', strtotime($date .' -1 day'));		//CHECK 12 JUNE EVENING REPORT PENDING
$pdate = "2014-01-06";
$unchanged = true;
//######## MAKE TWO SHIFTS
$shift_ev_date1 = $date." 16:00:00";
$shift_ev_date2 = $date." 23:59:59";
$shift_ev_date3 = $date." 00:00:00";
$shift_ev_date4 = $date." 09:00:00";
//$shift_ev_date4 = $date." 12:00:00";

$shift_mor_date1 = $pdate." 04:00:00";
$shift_mor_date2 = $pdate." 19:00:00";
//$shift_mor_date2 = $date." 22:00:00";

//$current_time = date('Y-m-d H:i:s');
$current_time = $pdate." 18:58:00";

$ev_run_start_time1 = $date." 20:00:00";
$ev_run_start_time2 = $date." 00:00:00";
$mor_run_start_time = $date." 10:00:00";

$shift_ev1 = false;
$shift_ev2 = false;
$shift_mor = false;

$shift_mor = true;
//$shift_ev2 = true;
//$route_type="CASH SMALL";
//$route_type="FOCAL MEDIUM";
//echo "SHIFT_EV1";
//############## CHECK VALID SHIFT #############################
//echo "\ncurrent_time=".$current_time.",shift_ev_date1=".$shift_ev_date1.", shift_ev_date2=".$shift_ev_date2;
//######## CHECK EVENING SHIFT1 ###########
//if( (($current_time >= $shift_ev_date1) && ($current_time <= $shift_ev_date2) && ($current_time >= $ev_run_start_time1)) || (($current_time >= $shift_ev_date3) && ($current_time <= $shift_ev_date4)) )
/*
if( (($current_time >= $shift_ev_date1) && ($current_time <= $shift_ev_date2) && ($current_time >= $ev_run_start_time1) ) || (($current_time >= $shift_ev_date3) && ($current_time <= $shift_ev_date4)) )
{	
	$shift_ev1 = true;
	echo "\nEv-Shift";
}
else
{
	//## DELETE EVENING FILE -IF SHIFT IS OVER
	echo "\nDEL-EV:SHIFT1 FILES";
	$shift = "ev";		
	if(file_exists($evening_sent_file_path1)) delete_file($evening_sent_file_path1);
	if(file_exists($evening_last_processed_time_path1)) delete_file($evening_last_processed_time_path1);
	if(file_exists($evening_last_halt_time_path1)) delete_file($evening_last_halt_time_path1);
}

//######## CHECK EVENING SHIFT2 ###########
if( ($current_time >= $shift_ev_date3) && ($current_time <= $shift_ev_date4) )
{	
	$shift_ev2 = true;
	echo "\nEv-Shift";
}
else
{
	//## DELETE EVENING FILE -IF SHIFT IS OVER
	echo "\nDEL-EV:SHIFT2 FILES";
	$shift = "ev";		
	if(file_exists($evening_sent_file_path2)) delete_file($evening_sent_file_path2);
	if(file_exists($evening_last_processed_time_path2)) delete_file($evening_last_processed_time_path2);
	if(file_exists($evening_last_halt_time_path2)) delete_file($evening_last_halt_time_path2);
}

//echo "\ncurrent_time=".$current_time.",shift_mor_date1=".$shift_mor_date1.", shift_mor_date2=".$shift_mor_date2;
if( ($current_time >= $shift_mor_date1) && ($current_time <= $shift_mor_date2) )
{
	if($current_time >= $mor_run_start_time)
	{
		$shift_mor = true;		
		echo "\nMor-Shift";
	}
}
else
{
	//## DELETE MORNING FILE -IF SHIFT IS OVER		 
	$shift = "mor";			
	if(file_exists($morning_sent_file_path)) delete_file($morning_sent_file_path);
	if(file_exists($morning_last_processed_time_path)) delete_file($morning_last_processed_time_path);
	if(file_exists($morning_last_halt_time_path)) delete_file($morning_last_halt_time_path);
}
//############ VALID SHIFT CLOSED ################################
*/
//$shift_mor = false; //###################TEMPPPPPPPPPPPPPPPP

echo "\nSTART";

$transporter_m = array();
$vehicle_m = array();	
//########################## MORNING SHIFT STARTS #########################
//#########################################################################
//#### INITIALIZE ARRAYS
$sheet1_row = 2;
$sheet2_row = 2;

$unchanged = true;

$shift = array();			//MASTER FILE
$expected_customer_csv = array();
$expected_time_csv = array();
$vehicle_t = array();
$transporter = array();
$all_routes = array();

$route_input = array();
$vehicle_input = array();
$customer_input = array();
$shift_input = array();
$transporter_input = array();

$min_date_ev = array();
$max_date_ev = array();
$min_date_mor = array();
$max_date_mor = array();

$relative_plant_input = array();
$relative_customer_input = array();
$relative_transporter_input = array();
$relative_route_input = array();

$customer_sel = array(array());
$plant_sel = array(array());
$transporter_sel = array(array());
$station_id = array(array());
$type = array(array());
$station_coord = array(array());
$distance_variable = array(array());

$expected_time_sel = array(array());	//FROM MASTER FILE

$vehicle_name_rdb = array();		//VEHICLE ROUTE DETAIL
$vehicle_imei_rdb = array();
$route_name_rdb = array();
$route_type_rdb = array();
//$remark_rdb = array();

$csv_string_dist = "";                //INITIALISE  DISTANCE VARIABLES
$csv_string_dist_arr = array();
$sno_dist = 0;
$overall_dist = 0.0;

$csv_string_halt = "";                //INITIALISE  HALT VARIABLES
$csv_string_halt_arr = array();
$total_halt_dur = 0;
$sno_halt = 0;

$user_interval = "1";   //1 MINUTES		

$Vehicle = array();			//SENT FILE
$SNo = array();
$StationNo = array();
$Type = array();
$RouteNo = array();
$ReportShift = array();
$ArrivalDate = array();
$ArrivalTime = array();
$DepartureDate = array();
$DepartureTime = array();
$ScheduleTime = array();
$Delay = array();
$HaltDuration = array();
$Remark = array();
$ReportDate1 = array();
$ReportTime1 = array();
$ReportDate2 = array();
$ReportTime2 = array();
$TransporterM = array();
$TransporterI = array();
$Plant = array();
$Km = array();
$Lat = array();
$Lng = array();
$DistVar = array();
$IMEI = array();
$RouteType = array();
//####################
$Vehicle_CI = array();
$StationNo_CI = array();
$RouteNo_CI = array();
$RouteType_CI = array();
$TransporterI_CI = array();
$ArrivalTime_CI = array();
//####################

$RedRoute = array();
$RedCustomer = array();

$last_vehicle_name = array();		//LAST PROCESSED FILE
$last_halt_time = array();
$last_halt_time_new = array();

//$last_time = $current_time;

$last_time_processed ="";
$csv_string_halt_final = "";

//$vehicle_arr = array();
//$customer_arr = array();
//$route_arr = array();

$total_route = array();
$total_customer = array();
//$type_arr = array();

//#################### IF SHIFT MORNING #########################
//$shift_mor = true; //comment
if($shift_mor)
{
	echo "\nMOR";
	$route_type = "ALL";
	//######## READ EVENING SENT FILE #############		
	if(file_exists($morning_last_processed_time_path))
	{
		echo "\nLast Processed";
		read_last_processed_time($morning_last_processed_time_path);
		echo "\nBefore Read LastHaltTime";
		read_last_halt_time($morning_last_halt_time_path);
		read_all_routes($account_id,"ZPMM");
		$Last_Time = $last_time_processed;
	}
	else
	{
		echo "\nElse:UpdateLastTime";
		$Last_Time = $shift_mor_date1;
		//$Last_Time = "2013-10-07 19:00:00";
	}
			
	if (!file_exists($morning_sent_file_path))
	{
		echo "\nCreateFile:Morning";
		$morning_last_processed_time = "";
		
		get_route_db_detail("ZPMM");		
		echo "\nSizeRoute=".sizeof($route_name_rdb);
		get_customer_db_detail($account_id, "ZPMM", $route_type);
		echo "\nSizeAllRoutes=".sizeof($all_routes);
		$objPHPExcel_1 = null;
		create_hrly_excel($morning_sent_file_path, "ZPMM", $route_type);
		echo "\nAfter CreateHrly";
		create_last_halt_time($morning_last_halt_time_path);
		echo "\nAfter LastHalt";
	}

	$objPHPExcel_1 = null;	
	read_sent_file($morning_sent_file_path);
	echo "\nAfter ReadSentFile";
	get_halt_xml_data($Last_Time,$current_time, $morning_sent_file_path);
	echo "\nAfter Data Process";
		
	//######### UPDATE LAST TIME PROCESSED -ALWAYS UPDATED #############	
	update_last_processed_time($morning_last_processed_time_path, $current_time);
	update_last_halt_time($morning_last_halt_time_path);
	
	echo "\nAfter Last ProcessedDetail:Morning";
	//#### LAST TIME PROCESSED CLOSED #############
		
	//############ SEND EMAIL ##############
	$to = 'rizwan@iembsys.com';
//	$to = 'gpsreporthourly@gmail.com';
	
	$time_1 = date('Y-m-d H:i:s');
	$time_2 = strtotime($time_1);
	$msg = "";
	if($unchanged)
	{
		$msg = "UNCHANGED";
	}
	else
	{
		$msg = "CHANGED";
	}	
	$subject = "V2_HOURLY_MAIL_VTS_HALT_REPORT_MORNING(MOTHER_DELHI)_".$msg."_".$time_1."_".$time_2;
	$message = "V2_HOURLY_MAIL_VTS_HALT_REPORT_MORNING(MOTHER_DELHI)_".$msg."_".$time_1."_".$time_2."<br><br><font color=red size=1>*** This is an automatically generated email by the system on specified time, please do not reply ***</font>";
	$random_hash = md5(date('r', time()));  
	$headers = "From: support@iembsys.co.in\r\n";
	$headers .= "Cc: hourlyreport4@gmail.com";
	//$headers .= "Cc: rizwan@iembsys.com";
	//pass:8090025844  
	//$headers .= "Cc: rizwan@iembsys.com,jyoti.jaiswal@iembsys.com,support1@iembsys.com,support2@iembsys.com";
	//$headers .= "Cc: rizwan@iembsys.com,jyoti.jaiswal@iembsys.com";
	$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
	$filename_title = "V2_HOURLY_MAIL_VTS_HALT_REPORT_MORNING_MOTHER_DELHI_".$msg."_".$time_1."_".$time_2.".xlsx";
	$file_path = $morning_sent_file_path;
	//echo "\nFILE PATH:Mor=".$file_path;
	include("send_mail_api.php");	
	//######################################	
}

//########################## EVENING SHIFT STARTS #########################
//#########################################################################
//#### INITIALIZE ARRAYS -EV-SHIFT1

$sheet1_row = 2;
$sheet2_row = 2;
$unchanged = true;

$shift = array();			//MASTER FILE
$expected_customer_csv = array();
$expected_time_csv = array();
$vehicle_t = array();
$transporter = array();
$all_routes = array();
$all_customers = array();

$route_input = array();
$vehicle_input = array();
$customer_input = array();
$shift_input = array();
$transporter_input = array();

$min_date_ev = array();
$max_date_ev = array();
$min_date_mor = array();
$max_date_mor = array();

$relative_plant_input = array();
$relative_customer_input = array();
$relative_transporter_input = array();
$relative_route_input = array();

$customer_sel = array(array());
$plant_sel = array(array());
$transporter_sel = array(array());
$station_id = array(array());
$type = array(array());
$station_coord = array(array());
$distance_variable = array(array());

$expected_time_sel = array(array());	//FROM MASTER FILE

$vehicle_name_rdb = array();		//VEHICLE ROUTE DETAIL
$vehicle_imei_rdb = array();
$route_name_rdb = array();
$route_type_rdb = array();
//$remark_rdb = array();

$csv_string_dist = "";                //INITIALISE  DISTANCE VARIABLES
$csv_string_dist_arr = array();
$sno_dist = 0;
$overall_dist = 0.0;

$csv_string_halt = "";                //INITIALISE  HALT VARIABLES
$csv_string_halt_arr = array();
$total_halt_dur = 0;
$sno_halt = 0;

$user_interval = "1";   //1 MINUTES		

$Vehicle = array();			//SENT FILE
$SNo = array();
$StationNo = array();
$Type = array();
$RouteNo = array();
$ReportShift = array();
$ArrivalDate = array();
$ArrivalTime = array();
$DepartureDate = array();
$DepartureTime = array();
$ScheduleTime = array();
$Delay = array();
$HaltDuration = array();
$Remark = array();
$ReportDate1 = array();
$ReportTime1 = array();
$ReportDate2 = array();
$ReportTime2 = array();
$TransporterM = array();
$TransporterI = array();
$Plant = array();
$Km = array();
$Lat = array();
$Lng = array();
$DistVar = array();
$IMEI = array();
$RouteType = array();

//####################
$Vehicle_CI = array();
$StationNo_CI = array();
$RouteNo_CI = array();
$ArrivalTime_CI = array();
//####################

$RedRoute = array();
$RedCustomer = array();

$last_vehicle_name = array();		//LAST PROCESSED FILE
$last_halt_time = array();

//$last_time = $current_time;

$last_time_processed ="";
$csv_string_halt_final = "";

//$vehicle_arr = array();
//$customer_arr = array();
//$route_arr = array();

//$shift_ev = true;
//$Last_Time = "2013-10-07 15:00:00";
//$current_time = "2013-10-07 21:55:00";
//$current_time = "2013-10-07 15:00:00";
//echo "\nShiftEV2";
if($shift_ev1)
{
	echo "\nEV1-CASH ROUTE";
	$route_type = "CASH";
	//######## READ EVENING SENT FILE #############		
	echo "\nLastProcessedFile=".$evening_last_processed_time_path1;
	if(file_exists($evening_last_processed_time_path1))
	{		
		echo "\nFile Exists";
		read_last_processed_time($evening_last_processed_time_path1);
		echo "\nLast ProcessedTime";
		read_last_halt_time($evening_last_halt_time_path1);
		read_all_routes($account_id,"ZPME");
		echo "\nLast HaltTime";
		$Last_Time = $last_time_processed;
	}
	else
	{
		echo "\nFile DoesNot Exist";
		$Last_Time = $shift_ev_date1;
		//$Last_Time = "2013-10-07 15:00:00";
	}
			
	if (!file_exists($evening_sent_file_path1))
	{
		echo "\nCreateFile:Evening";
		$evening_last_processed_time = "";
		
		get_route_db_detail("ZPME");		
		echo "\nSizeRoute=".sizeof($route_name_rdb);
		get_customer_db_detail($account_id, "ZPME", $route_type);
		echo "\nSizeAllRoutes=".sizeof($all_routes);
		$objPHPExcel_1 = null;
		create_hrly_excel($evening_sent_file_path1, "ZPME", $route_type);
		create_last_halt_time($evening_last_halt_time_path1, $route_type);		
		//echo "\n3";
	}
	
	$objPHPExcel_1 = null;	
	echo "\nEvFile1=".$evening_sent_file_path1;
	read_sent_file($evening_sent_file_path1);
	echo "\nAfter ReadSentFile";
	get_halt_xml_data($Last_Time,$current_time, $evening_sent_file_path1);
	echo "\nAfter Data Process";	
		
	//######### UPDATE LAST TIME PROCESSED -ALWAYS UPDATED #############	
	update_last_processed_time($evening_last_processed_time_path1, $current_time);
	update_last_halt_time($evening_last_halt_time_path1);
	echo "\nAfter Last ProcessedDetail:Evening";
	//#### LAST TIME PROCESSED CLOSED #############
	
	//############ SEND EMAIL ##############
	//$to = 'rizwan@iembsys.com';
	$to = 'gpsreporthourly@gmail.com'; 	
	$time_1 = date('Y-m-d H:i:s');
	$time_2 = strtotime($time_1);
	$msg = "";
	if($unchanged)
	{
		$msg = "UNCHANGED";
	}
	else
	{
		$msg = "CHANGED";
	}
	$subject = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING(MOTHER_DELHI)_CASH_ROUTE_".$msg."_".$time_1."_".$time_2;
	$message = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING(MOTHER_DELHI)_CASH_ROUTE_".$msg."_".$time_1."_".$time_2."<br><br><font color=red size=1>*** This is an automatically generated email by the system on specified time, please do not reply ***</font>";
	$random_hash = md5(date('r', time()));  
	$headers = "From: support@iembsys.co.in\r\n";
	$headers .= "Cc: hourlyreport4@gmail.com"; 
	//$headers .= "Cc: rizwan@iembsys.com";	
	//pass:8090025844
	//$headers .= "Cc: rizwan@iembsys.com,jyoti.jaiswal@iembsys.com";
	$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 	
	$filename_title = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING_MOTHER_DELHI_CASH_ROUTE_".$msg."_".$time_1."_".$time_2.".xlsx";	
	$file_path = $evening_sent_file_path1;

	//echo "\nFILE PATH:Ev=".$file_path; 	
	include("send_mail_api.php");	
	//######################################
}


//#### INITIALIZE ARRAYS -EV-SHIFT1

$sheet1_row = 2;
$sheet2_row = 2;
$unchanged = true;

$shift = array();			//MASTER FILE
$expected_customer_csv = array();
$expected_time_csv = array();
$vehicle_t = array();
$transporter = array();
$all_routes = array();
$all_customers = array();

$route_input = array();
$vehicle_input = array();
$customer_input = array();
$shift_input = array();
$transporter_input = array();

$min_date_ev = array();
$max_date_ev = array();
$min_date_mor = array();
$max_date_mor = array();

$relative_plant_input = array();
$relative_customer_input = array();
$relative_transporter_input = array();
$relative_route_input = array();

$customer_sel = array(array());
$plant_sel = array(array());
$transporter_sel = array(array());
$station_id = array(array());
$type = array(array());
$station_coord = array(array());
$distance_variable = array(array());

$expected_time_sel = array(array());	//FROM MASTER FILE

$vehicle_name_rdb = array();		//VEHICLE ROUTE DETAIL
$vehicle_imei_rdb = array();
$route_name_rdb = array();
$route_type_rdb = array();
//$remark_rdb = array();

$csv_string_dist = "";                //INITIALISE  DISTANCE VARIABLES
$csv_string_dist_arr = array();
$sno_dist = 0;
$overall_dist = 0.0;

$csv_string_halt = "";                //INITIALISE  HALT VARIABLES
$csv_string_halt_arr = array();
$total_halt_dur = 0;
$sno_halt = 0;

$user_interval = "1";   //1 MINUTES		

$Vehicle = array();			//SENT FILE
$SNo = array();
$StationNo = array();
$Type = array();
$RouteNo = array();
$ReportShift = array();
$ArrivalDate = array();
$ArrivalTime = array();
$DepartureDate = array();
$DepartureTime = array();
$ScheduleTime = array();
$Delay = array();
$HaltDuration = array();
$Remark = array();
$ReportDate1 = array();
$ReportTime1 = array();
$ReportDate2 = array();
$ReportTime2 = array();
$TransporterM = array();
$TransporterI = array();
$Plant = array();
$Km = array();
$Lat = array();
$Lng = array();
$DistVar = array();
$IMEI = array();
$RouteType = array();

//####################
$Vehicle_CI = array();
$StationNo_CI = array();
$RouteNo_CI = array();
$ArrivalTime_CI = array();
//####################

$RedRoute = array();
$RedCustomer = array();

$last_vehicle_name = array();		//LAST PROCESSED FILE
$last_halt_time = array();

//$last_time = $current_time;

$last_time_processed ="";
$csv_string_halt_final = "";


if($shift_ev2)
{
	echo "\nEV-2 FOCAL ROUTE";
	$route_type = "FOCAL";
	//######## READ EVENING SENT FILE #############		
	if(file_exists($evening_last_processed_time_path2))
	{		
		read_last_processed_time($evening_last_processed_time_path2);
		echo "\nLast ProcessedTime";
		read_last_halt_time($evening_last_halt_time_path2);
		read_all_routes($account_id,"ZPME");
		echo "\nLast HaltTime";
		$Last_Time = $last_time_processed;
	}
	else
	{
		echo "\nElse";
		$Last_Time = $shift_ev_date1;
		//$Last_Time = "2013-10-07 15:00:00";
	}
			
	if (!file_exists($evening_sent_file_path2))
	{
		echo "\nCreateFile:Evening";
		$evening_last_processed_time = "";
		
		get_route_db_detail("ZPME");		
		echo "\nSizeRoute=".sizeof($route_name_rdb);
		get_customer_db_detail($account_id, "ZPME", $route_type);
		echo "\nSizeAllRoutes=".sizeof($all_routes);
		$objPHPExcel_1 = null;
		create_hrly_excel($evening_sent_file_path2, "ZPME", $route_type);
		create_last_halt_time($evening_last_halt_time_path2, $route_type);		
		//echo "\n3";
	}
	
	$objPHPExcel_1 = null;	
	read_sent_file($evening_sent_file_path2);
	echo "\nAfter ReadSentFile";
	get_halt_xml_data($Last_Time,$current_time, $evening_sent_file_path2);
	echo "\nAfter Data Process";	
		
	//######### UPDATE LAST TIME PROCESSED -ALWAYS UPDATED #############	
	update_last_processed_time($evening_last_processed_time_path2, $current_time);
	update_last_halt_time($evening_last_halt_time_path2);
	echo "\nAfter Last ProcessedDetail:Evening";
	//#### LAST TIME PROCESSED CLOSED #############
	
	//############ SEND EMAIL ##############
	//$to = 'rizwan@iembsys.com';
	$to = 'gpsreporthourly@gmail.com'; 	
	$time_1 = date('Y-m-d H:i:s');
	$time_2 = strtotime($time_1);
	$msg = "";
	if($unchanged)
	{
		$msg = "UNCHANGED";
	}
	else
	{
		$msg = "CHANGED";
	}
	$subject = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING(MOTHER_DELHI)_FOCAL_ROUTE_".$msg."_".$time_1."_".$time_2;
	$message = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING(MOTHER_DELHI)_FOCAL_ROUTE_".$msg."_".$time_1."_".$time_2."<br><br><font color=red size=1>*** This is an automatically generated email by the system on specified time, please do not reply ***</font>";
	$random_hash = md5(date('r', time()));  
	$headers = "From: support@iembsys.co.in\r\n";
	$headers .= "Cc: hourlyreport4@gmail.com"; 
	//$headers .= "Cc: rizwan@iembsys.com";	
	//pass:8090025844
	//$headers .= "Cc: rizwan@iembsys.com,jyoti.jaiswal@iembsys.com";
	$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 	
	$filename_title = "HOURLY_MAIL_VTS_HALT_REPORT_EVENING_MOTHER_DELHI_FOCAL_ROUTE_".$msg."_".$time_1."_".$time_2.".xlsx";	
	$file_path = $evening_sent_file_path2;

	//echo "\nFILE PATH:Ev=".$file_path; 	
	include("send_mail_api.php");	
	//######################################
}
//######### SHIFT EVENING CLOSED 

$last_halt_sec_global = 0;

function binary_plant_search($elem, $array, $array1, $array2, $array3) 	//elem = station to search, array = customer, array1 = plant
{
   $top = sizeof($array) -1;
   $bot = 0;
   while($top >= $bot) 
   {
      $p = floor(($top + $bot) / 2);
      if ($array[$p] < $elem) $bot = $p + 1;
      elseif ($array[$p] > $elem) $top = $p - 1;
      else return $array1[$p].":".$array2[$p].":".$array3[$p];//return TRUE;
   }
   return "-";
}

function get_halt_information()
{
}
?>


