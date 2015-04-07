<?php
session_start();
require_once '../server/DBManager_sqlserver.php';

function adminLogin($username, $hashPassword){
	
	try{
	    $db = new DBManager();
	    $sql = "SELECT COUNT(1) AS loginStatus from ADMIN_USERS a 
	        INNER JOIN STUDENT b ON a.user_id = b.student_id
	        WHERE a.user_id = '".$username."' AND b.password = '".$hashPassword."'";
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
$hashPassword = $_POST["hashPassword"];
$hashPassword = sha1($username.$hashPassword);

if(isset($username) && isset($hashPassword) && adminLogin($username, $hashPassword)){
	$_SESSION['loginUsername'] = $username;
	$_SESSION['loginStatus'] = 1;
	$_SESSION['loginType'] = 'admin';
	header('Location: ../pages/admin_dashboard.html');
}else{
	header('Location: ../admin_login.html');
}


?>