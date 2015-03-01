<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
สวัสดี
<?php

include("DBManager_sqlserver.php");

$DBManager = new DBManager();

$sql = "INSERT INTO testtable (test_id, test_name) VALUES (N'เทส', N'กี้')";

$DBManager->beginSet();
$DBManager->setData($sql);
$DBManager->commitWork();

$sql = "SELECT * from testtable";
$result = $DBManager->getData($sql);
//print_r($result);
while($row = sqlsrv_fetch_array($result))
{
	echo $row['test_id']." -> ".$row['test_name']."<br>";
}

$sql = "SELECT * from STUDENT";
$result = $DBManager->getData($sql);
//print_r($result);
while($row = sqlsrv_fetch_array($result))
{
	echo $row['name']." -> ".$row['lastname']."<br>";
}

?>