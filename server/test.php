<?php

require_once 'DBManager_sqlserver.php';
require_once 'admin_class_activateschedule.php';

$db = new DBManager();
$a = new AdminActivateSchedule($db, 2, 4);


$startDate = "20150425";
$startTime = "100000";
$endDate = "20150426";
$endTime = "110000";

echo $db->dbname;

//$a->addActivateSchedule($startDate, $startTime, $endDate, $endTime);

?>