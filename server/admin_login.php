<?php
session_start();
require_once '../server/DBManager_sqlserver.php';

function adminLogin($username, $password){
	try{
	    $db = new DBManager();
	    $sql = "SELECT COUNT(1) AS loginStatus from ADMIN_USERS a 
	        INNER JOIN STUDENT b ON a.user_id = b.student_id
	        WHERE a.user_id = '".$username."' AND b.password = '".$password."'";
	    $result = $db->getData($sql);
	    if ($result){
	        while($row = sqlsrv_fetch_array($result)){
	            $loginStatus = $row['loginStatus'];
	        }
	    }

	    if ($loginStatus == 1){
	        return true;
	    }

	} catch(PDOException $e) {
	    echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
	}
}

$username = $_POST["txtUsername"];
$password = $_POST["txtPassword"];

if(isset($username) && isset($password) && adminLogin($username, $password)){
	$_SESSION['loginUsername'] = $username;
	$_SESSION['loginStatus'] = 1;
	header('Location: ../pages/admin_dashboard.html');
}else{
	header('Location: ../admin_login.html');
}


?>