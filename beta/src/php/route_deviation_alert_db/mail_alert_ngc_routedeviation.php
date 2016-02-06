<?php
set_time_limit(300);
$DEBUG_OFFLINE = true;

$vehicle_name = array();
$device_imei_no = array();
$polyline_data_bin = array();
$polyline_name_bin = array();

$final_halt_datetime = array();
$final_halt_location = array();
$final_halt_remark = array();

$final_route_deviation_datetime = array();
$final_route_deviation_location = array();
$final_route_deviation_remark = array();
//#################
if($DEBUG_OFFLINE)
{
	include_once("setup/database_conn.php");
	include_once("setup/class_polyline_edge.php");
}
else
{
	include_once("setup/database_conn.php");
	include_once("setup/class_polyline_edge.php");
}
$account_id = "1991";
$user_name = "NGC";
$group_id = "0068";

if($DEBUG_OFFLINE)
{
    $abspath = "..";
}
else
{
    $abspath = "/var/www/html/vts/beta/src/php";
}
//echo "<br>AbsPath=".$abspath;
$isReport2=1;
include_once($abspath."/common_xml_element.php");
include_once($abspath."/calculate_distance.php");
include_once($abspath."/xmlParameters.php");
include_once($abspath."/parameterizeData.php");
include_once($abspath."/lastRecordData.php");
//include_once($abspath."/getXmlDatareport.php");	
include_once($abspath."/getDeviceData.php");		
include_once("action_ngc_routedeviation_alert.php");

$parameterizeData=new parameterizeData();
$parameterizeData->messageType='a';
$parameterizeData->version='b';
$parameterizeData->fix='c';
$parameterizeData->latitude='d';
$parameterizeData->longitude='e';
$parameterizeData->speed='f';	
$parameterizeData->io1='i';
$parameterizeData->io2='j';
$parameterizeData->io3='k';
$parameterizeData->io4='l';
$parameterizeData->io5='m';
$parameterizeData->io6='n';
$parameterizeData->io7='o';
$parameterizeData->io8='p';	
$parameterizeData->sigStr='q';
$parameterizeData->supVoltage='r';
$parameterizeData->dayMaxSpeed='s';
$parameterizeData->dayMaxSpeedTime='t';
$parameterizeData->lastHaltTime='u';
$parameterizeData->cellName='ab';	
$sortBy="h";
//date_default_timezone_set('Asia/Calcutta');
$current_date=date("Y-m-d");


//---------------step 1: Getting vehicle information of account-----------//
$polyline_id_bin=array();
$data_vehicle=array();
$final_result=array();
$data_vehicle=getVIDINVnameAr($account_id,$DbConnection);
if(sizeof($data_vehicle)>0)
{
   // $data[]=array('vid'=>$row->vehicle_id,'device'=>$row->device_imei_no,'vname'=>$row->vehicle_name);
    
    $o_cassandra=openCassandraConnection();
    //print_r($data_vehicle);
    foreach($data_vehicle as $row_data)
    {
        $vid=$row_data['vid'];
        $imei=$row_data['device'];
        $vname=$row_data['vname'];
        //echo $imei;
        $polyline_id=PolylineAssignVehilce($vid,$DbConnection);         
        //echo" Polid=". $polyline_id;
        $query_polyline = "SELECT polyline_coord,polyline_name FROM polyline WHERE polyline_id ='$polyline_id' AND ".
		"status=1";  
        $res_polyline = mysql_query($query_polyline,$DbConnection);
        if($row_polyline = mysql_fetch_object($res_polyline))
        {
                $polyline_coord_tmp = $row_polyline->polyline_coord;
                //echo"t".$polyline_coord_tmp;
                $polyline_coord = base64_decode($polyline_coord_tmp);//polyline in base64 must be decode before use
                $polyline_coord = str_replace('),(',' ',$polyline_coord);
                $polyline_coord = str_replace('(','',$polyline_coord);
                $polyline_coord = str_replace(')','',$polyline_coord);
                $polyline_coord = str_replace(', ',',',$polyline_coord);

                $polyline_data=explode(" ",$polyline_coord);
                $polyline_name=$row_polyline->polyline_name;
                //echo "PN=".$row_polyline->polyline_name;
                //echo "Polyline_choord=". $polyline_coord."<br>";
                //$flag_route=1;
                //----------------Step 2: Getting lat,lng of  Vehicle from xml/casandra--------//
                $chk_latlng_array=array();$data_date_array=array();
                //getting lat lng from casandra--------//
                
                $data_val= getLastSeenRecord($imei,$sortBy,$o_cassandra,$parameterizeData);               
                //print_r($data_val);
                $chk_latlng_array[]= $data_val->latitudeLR[0].','.$data_val->longitudeLR[0];
                $data_date_array[]= $data_val->deviceDatetimeLR[0];
               
                //===============For Testing Purpose======================//
                //$chk_latlng_array[]='25.319348555457204,86.44562244415283';
                //$data_date_array[]='2015-29-09 16:02:01';
                //=======[Class/Object] Class called for checking point on edge=============//
                $get_data=new class_polyline_edge();	
                $data_result = $get_data->get_polyline($polyline_data,$chk_latlng_array,$data_date_array,$polyline_name); //both parameters in array
                //print_r($data_result);
                foreach($data_result as $dtres)
                {
                        $dres=explode(':',$dtres);
                        $final_result[]=array('vehicle_no'=>$vname,'message'=>$dres[0],'current_loc'=>$dres[6],'datetime'=>$dres[5]);
                        //print_r($final_result);
                        
                }
        }
        
    }
    closeCassandraConnection($o_cassandra);
    //exit();
}
else
{
    echo "no vehicle assigned to ngc";
}

//Save into Database
$header="";
//print_r($final_result);
if(sizeof($final_result)>0)
{
	//save it into database
	
	//email
	$header="<b>Route Deviation Report Greater Than 1 Km From its Route<b><br><table border=1><tr><th>Vehicle</th><th>Message</th><th>Location</th><th>DateTime</th></tr>";
	foreach($final_result as $fre)
	{
		$header.='<tr>
		<td>'.$fre['vehicle_no'].'</td>				
		<td>'.$fre['message'].'</td>
		<td>'.$fre['current_loc'].'</td>
		<td>'.$fre['datetime'].'</td></tr>';
	}
	$header.="</table>";
}
//echo $header;
if($header!="")
{
	///===saving into database
	date_default_timezone_set('Asia/Calcutta');
	$current_time1 = date('Y-m-d H:i:s');
	$email_to='taseen@iembsys.com';
        //$email_to='prasad@charterhouse.in,rahul@charterhouse.in,Charterhouse GPS Team <gps.trakingitc@gmail.com>,logalert14@gmail.com';
	$queryInsert="Insert into email_log (account_id,subject,email,message,message_type,status,create_date,create_id) Values('$account_id','NGC Vehicle RouteDeviation','$email_to','$header','NGC_RD',1,'$current_time1','$account_id')";
	//echo $queryInsert;
	$Result=mysql_query($queryInsert,$DbConnection);
	if($Result)
	{
		echo"Saved into database";
	}
}

?>


