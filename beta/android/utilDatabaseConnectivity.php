<?php
$HOST = "localhost";
$DBASE = "iespl_vts_beta";
$USER = "root";
$PASSWD = "mysql";
    //Open a new connection to the MySQL server
$mysqli = new mysqli($HOST,$USER,$PASSWD,$DBASE);

//$mysqliP = new E_mysqli($HOST,$USER,$PASSWD,$DBASE);

//Output any connection error
if ($mysqli->connect_error) 
{
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}
	//include_once("PhpMysqlConnectivity.php");
	
//$DbConnection = mysql_connect($HOST,$USER,$PASSWD) or die("could not connect to DB test");
//mysql_select_db ($DBASE, $DbConnection) or die("could not find DB");
?>