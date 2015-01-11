<?php
include("DBManager_sqlserver.php");

$DBManager = new DBManager();

$sql = "SELECT * from testtable";
$result = $DBManager->getData($sql);
while($row = sqlsrv_fetch_array($result))
{
	echo $row['test_id']." -> ".$row['test_name']."<br>";
}

$sql = "INSERT INTO testtable (test_id, test_name) VALUES (?, ?)";
$params = array(3, "test3");

$DBManager->beginSet();
//$DBManager->setData($sql, $params);
$DBManager->commitWork();

?>