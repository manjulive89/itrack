<?php
error_reporting(-1);
ini_set('display_errors', 'On');
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
include_once ('PHPExcel/IOFactory.php');

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '1028MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

/*$sheet = array(
    array(
      'a1 data',
      'b1 data',
      'c1 data',
      'd1 data',
    )
  );*/
for($i=1;$i<6;$i++)
{
    $aa[$i]='abc';
    $aa[$i]='cde';
    $aa[$i]='efr';
    $aa[$i]='sfdf';
}

$sheet[]=$aa;
//print_r($sheet);

  $doc = new PHPExcel();
  $doc->setActiveSheetIndex(0);

  $doc->getActiveSheet()->fromArray($sheet, null, 'A1');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="your_name.xls"');
header('Cache-Control: max-age=0');

  // Do your stuff here
  $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

$writer->save('/mnt/itrack/beta/src/php/download/your_name.xls');
//echo "tst";
//exit();*/
?>
		
			
