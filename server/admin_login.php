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
	    	//log audit
	    	$user_ip = get_client_ip();
	    	$sql = "INSERT INTO ADMIN_AUDITLOG (user_id, user_ip, activity, logdate) VALUES ('$username', '$user_ip', 'Admin Login', GETDATE())";
	    	$db->beginSet();
	        if($db->setData($sql))
	        {
	            $db->commitWork();
	        }
	        return true;
	    }

	} catch(PDOException $e) {
	    echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
	}
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
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