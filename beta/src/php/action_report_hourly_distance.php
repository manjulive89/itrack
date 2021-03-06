<?php
include_once("main_vehicle_information_1.php");
 include_once('Hierarchy.php');
include_once('util_session_variable.php');
include_once('util_php_mysql_connectivity.php');
date_default_timezone_set("Asia/Kolkata");

echo'<link rel="StyleSheet" href="src/css/menu.css">';
include_once('xmlParameters.php');
include_once('parameterizeData.php');
include_once('lastRecordData.php');
include_once('getXmlData.php');	
if($personOption=="singlePerson")
{
	//echo "in if";
	//echo"vehicleserialRadio=".$vehicleserialRadio."<br>";
	
	//var_dump($root);
	$vehicle_info=get_vehicle_info($root,$vehicleserialRadio);
	//echo "vehicleInfo=".$vehicle_info."<br>";	
	$vehicle_detail_local=explode(",",$vehicle_info);
	//print_r($vehicle_detail_local);

	$parameterizeData=new parameterizeData();
	$parameterizeData->version='b';
	$LastRecordObject=getLastRecord($vehicleserialRadio,$sortBy,$parameterizeData);
	$sortBy="h";
	$versionString=$LastRecordObject->versionLR[0];
	$vehicleDetailArr[$vehicleserialRadio]=$vehicle_detail_local[0]."@".$vehicle_detail_local[8]."@".$versionString;
	//print_r($vehicleDetailArr);
	preg_match('#\((.*?)\)#', $versionString, $match);
	$versionOnly=$match[1];
	$version=substr($versionOnly,0,-2);

	$versionArr=explode('-',$version);
	if(count($versionArr)==1)
	{
		$durationFrom=0;
		$durationTo=$versionArr[0];
	}
	else
	{
		$durationFrom=$versionArr[0];
		$durationTo=$versionArr[1];
	}
	//echo "durationFron=".$durationFrom."DurationTo=".$durationTo."<br>";

	//exit();
}
else if($personOption=="multiplePerson")
{
	$durationFrom=8;
	$durationTo=21;	
}

$mysqlTableColumns="";
$mysqlDistTableColumns="";
$switchFlag=0;
$sheetData = array(
    array(
      'a1 data',
      'b1 data',
      'c1 data',
      'd1 data',
    )
  );
for($i=$durationFrom;$i<=$durationTo;$i++) // for making dynamic column duration half hour fetching record from mysql
{
	$hr=($i<10)?'0'.$i:$i;
	if($timeInterval==0) /////// this is only for 30 minute interval
	{
            if("HR_".$hr."_00_LOC"=="HR_00_00_LOC") //for setting 00_30 colume only
            {
                $mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_30_LOC,";
                 $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_30_DIST,";
           
            }
            else
            {
		$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_00_LOC,";
                $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_00_DIST,";
		if($i!=$durationTo) // for skiping last column because it exceed duration time
		{
			$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_30_LOC,";
                        $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_30_DIST,";
		}
            }
	}
	else ///// this for except interval
	{	
		if($switchFlag==0)
		{
			$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_00_LOC,";
			$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_30_LOC,";
                        $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_00_DIST,";
                        $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_30_DIST,";
		}
		else
		{
			$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_00_LOC,";
                        $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_00_DIST,";
			//echo "i=".$i."durationTo=".$durationTo."<br>";
			if($i!=$durationTo) // for skiping last column becuase it exceed duration time
			{
				$mysqlTableColumns=$mysqlTableColumns."HR_".$hr."_30_LOC,";
                                $mysqlDistTableColumns=$mysqlDistTableColumns."HR_".$hr."_30_DIST,";
			}			
		}
	}
	$switchFlag=1;
}

$switchFlag=0;
$durationThis=0;
for($i=$durationFrom;$i<=$durationTo;$i++) ///// this is for column headings of table
{
	if($switchFlag==0) // for storing value of i one time in durationThis variable
	{
		$durationThis =$i;
	}
	
	$durationThis=$durationThis+$timeInterval;		
	if($durationThis>$durationTo) // when durationThis exceed from date end range than break the loop
	{
		break;
	}
	
	$hr=($i<10)?'0'.$i:$i;
	if($timeInterval==0) /////// this is only for 30 minute interval
	{
              
            if($hr."_00"=="00_00") //for setting 00_30 colume only
            {
                $durationArr[]=$hr.":30";
            }
            else
            {
		$durationArr[]=$hr.":00";
		if($i!=$durationTo) // for skiping last column becuase it exceed duration time
		{
			$durationArr[]=$hr.":30";
		}
            }
	}
	else ///// this for except interval
	{	
		if($switchFlag==0)
		{				
			$durationThis =$hr;
			$durationArr[]=$hr.":00";
		}
		else
		{
			$durationArr[]=$durationThis.":00";		
		}
	}
	$switchFlag=1;
}

//print_r($durationArr);
//echo"<br><br>";
//echo" personOption=".$personOption."<br>";

$mysqlTableColumns=substr($mysqlTableColumns,0,-1);
$mysqlDistTableColumns=substr($mysqlDistTableColumns,0,-1);

//echo" mysqlTableColumns=".$mysqlTableColumns."<br>";
if($personOption=="singlePerson")
{
$Query="SELECT imei,date,".$mysqlTableColumns.",".$mysqlDistTableColumns." FROM hourly_distance_log USE INDEX(date_imei) WHERE imei='$vehicleserialRadio'".
	   " AND date BETWEEN '$start_date' AND '$end_date'";
//echo "Query1=".$Query."<br>";
//exit();
$Result=mysql_query($Query,$DbConnection);
}
else if($personOption=="multiplePerson")
{
    
$imeiCondition="";
for($i=0;$i<sizeof($vehicleserial);$i++)
{
    $vehicle_info=get_vehicle_info($root,$vehicleserial[$i]);
    //echo "vehicleInfo=".$vehicle_info."<br>";	
    $vehicle_detail_local=explode(",",$vehicle_info);	
    $parameterizeData=new parameterizeData();
    $parameterizeData->version='b';
    $LastRecordObject=getLastRecord($vehicleserial[$i],$sortBy,$parameterizeData);
    $sortBy="h";
    $versionString=$LastRecordObject->versionLR[0];
    $vehicleDetailArr[$vehicleserial[$i]]=$vehicle_detail_local[0]."@".$vehicle_detail_local[8]."@".$versionString;
    $vSerialMultiple=explode(',',$vSerial[$i]);	
    $imeiCondition=$imeiCondition."imei='".$vehicleserial[$i]."' OR ";
}
$imeiCondition=substr($imeiCondition,0,-3);
$Query="SELECT imei,date,".$mysqlTableColumns.",".$mysqlDistTableColumns." FROM hourly_distance_log USE INDEX(date_imei) WHERE  date='$single_date' AND".
		" ($imeiCondition)";
//echo "Query2=".$Query."<br>";
$Result=mysql_query($Query,$DbConnection);
//print_r($vSerial);
}
if($timeInterval==0)
{
	$dataInterval=1;
}
else
{
	$dataInterval=($timeInterval*60)/30;
}

echo'<center><br>
		<font color="black"><b>Hourly Distance Report</font>
	</center>';
if(mysql_num_rows($Result)==0)
{
echo'<center><br>
		<font color="red"><b>No data found for selected option.</font>
	</center>';
	exit();
}
$titleArr=array();
echo'<form name="locationForm" action="commonDownloadScript.php" target="_blank"> <center><br>
<table class="menu" border=1 rules=all bordercolor="#e5ecf5" style="font-size: 10pt;margin: 0px;padding: 0px;font-weight: normal;" cellspacing=3 cellpadding=3>
		<tr bgcolor="darkgray">
			<td>
			<b>Serial
			</td>
			<td>
			<b>Date
			</td>
			<td>
			<b>User Name
			</td>
			<td>
			<b>Mobile Number
			</td>
			<td>
			<b>Apd Version
			</td>';
        $titleArr[]="Serial";
        $titleArr[]="Date";
        $titleArr[]="User Name";
        $titleArr[]="Mobile Number";
        $titleArr[]="Apd Version";
	for($i=0;$i<sizeof($durationArr);$i++)
	{
             $titleArr[]=$durationArr[$i];
		echo"<td><b>".$durationArr[$i]."</td>";
	}
		echo"</tr>";
                
	$serial=1;
	$mysqlTableColumnsArr=explode(",",$mysqlTableColumns);
        $mysqlDistTableColumns=explode(",",$mysqlDistTableColumns);
        
	//echo "mysqlTableColumns=".$mysqlTableColumns."<br>";
	$columnSize=sizeof($mysqlTableColumnsArr);
	
	$sheetFinalArr[]=$titleArr;
	while($row=mysql_fetch_object($Result))
	{
            $valueArr=array();
            if($serial%2==0)
            {
                    echo"<tr bgcolor='lightgray'>";
            }
            else
            {
                    echo"<tr>";
            }
	$imeiDetailArr=explode("@",$vehicleDetailArr[$row->imei]);
	echo"<td>".$serial."</td>
		<td>".$row->date."</td>
		<td>".$imeiDetailArr[0]."</td>
		<td>".$imeiDetailArr[1]."</td>
		<td>".$imeiDetailArr[2]."</td>";
		//echo"columnSize=".$columnSize."<br>";
		//echo"dataInterval=".$dataInterval."<br>";
        $valueArr[]=$serial;
        $valueArr[]=$row->date;
        $valueArr[]=$imeiDetailArr[0];
        $valueArr[]=$imeiDetailArr[1];
        $valueArr[]=$imeiDetailArr[2];
		$durationBreakCount=1;
		$culumnSum=0;
		
		for($ci=0;$ci<$columnSize;$ci++)
		{
                    if($row->$mysqlTableColumnsArr[$ci]=="NODATA")
                    {
                        $locationRecord='No Record Found';
                    }
                    else
                    {
                        $locationRecord=$row->$mysqlTableColumnsArr[$ci];
                    }
			if($ci==0)
			{
                            $valueArr[]=$locationRecord;
				echo"<td>".$locationRecord."</td>";
				continue;
			}
			
			if($durationBreakCount<=$dataInterval)
			{
				//$culumnSum+=$culumnSum+$locationRecord;
				//echo"durationBreakCount=".$durationBreakCount."dataInterval=".$dataInterval."mysqlTableColumnsArr=".$mysqlTableColumnsArr[$ci]."<br>";
				if($durationBreakCount==$dataInterval)
				{
                                    $valueArr[]=$locationRecord."[".$row->$mysqlDistTableColumns[$ci]."]";
					echo"<td>".$locationRecord."[".$row->$mysqlDistTableColumns[$ci]."]</td>";
					$culumnSum=0;
					$durationBreakCount=1;
					if($ci==$columnSize)
					{
						break;
					}
					continue;
				}			
				$durationBreakCount++;
			}
		}
echo"</tr>";
$sheetFinalArr[]=$valueArr;
$serial++;
	}		
echo"</table>
<input type='hidden' name='downloadFilePath' value='/mnt/itrack/beta/src/php/download/personHourlyReport.xls'>
<input type='hidden' name='downloadFileName' value='personHourlyReport.xls'>
<br>
<input type='submit' value='Download Excel'></center></form>";

//print_r($sheetFinalArr);
//error_reporting(-1);
//ini_set('display_errors', 'On');
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
include_once ('PHPExcel/IOFactory.php');

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '1028MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);



  $doc = new PHPExcel();
  $doc->setActiveSheetIndex(0);

  $doc->getActiveSheet()->fromArray($sheetFinalArr, null, 'A1');
/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="your_name.xls"');
header('Cache-Control: max-age=0');*/

  // Do your stuff here
  $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

$writer->save('/mnt/itrack/beta/src/php/download/personHourlyReport.xls');
//echo "tst";
//exit();*/
?>
		
			
