<?php
function getStudentById($id){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
		$sql = "SELECT student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status FROM STUDENT where student_id = '$id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($response_arr, $row);
			}
		}
		$db = null;
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function getStudentByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $student_id = json_decode($request->getBody())->student_id;
		$sql = "SELECT student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status FROM STUDENT where student_id = '$student_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($response_arr, $row);
			}
		}
		$db = null;
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function getStudentByClassOf(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
		$sql = "SELECT student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status FROM STUDENT where classof_id = '$classof_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($response_arr, $row);
			}
		}
		$db = null;
		$app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function addStudent(){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
		$request = $app->request();
	    $student_detail = json_decode($request->getBody());

	    $password = passwordEncryption($student_detail->password, $student_detail->student_id);

	    $sql = "INSERT INTO STUDENT (student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status) VALUES ('$student_detail->student_id', '$student_detail->classof_id', N'$student_detail->name', N'$student_detail->lastname', '$student_detail->email', '$student_detail->password', '$student_detail->profilepic', '$student_detail->GPA', GETDATE(), '$student_detail->student_status')";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
        $db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
        {
        	$db->commitWork();
            $app->response->setBody(json_encode(array("status"=>"success")));
        }
        else
		{
			$db->rollbackWork();
            $app->response->setBody(json_encode(array("status"=>"fail")));
            $app->response->write(json_encode($db->errmsg()));    	
		}
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateStudent(){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
		$request = $app->request();
	    $student_detail = json_decode($request->getBody());

	    $student_id = $student_detail->student_id;
	    $classof_id = $student_detail->classof_id;
	    $name = $student_detail->name;
	    $lastname = $student_detail->lastname;
	    $email = $student_detail->email;
	    $password = $student_detail->password;
	    $profilepic = $student_detail->profilepic;
	    $GPA = $student_detail->GPA;
	    $student_status = $student_detail->student_status;

	    $updateSql = "";
	    if ($classof_id != "" || $name != "" || $lastname != "" || $email != "" || $password != "" || $profilepic != "" || $GPA != "" || $student_status != ""){

	    	$updateSql = "UPDATE STUDENT SET updatedate = GETDATE()";
	    	if($classof_id != ""){
	    		$updateSql .= ", classof_id = '$classof_id'";
	    	}
	    	if($name != ""){
	    		$updateSql .= ", name = N'$name'";
	    	}
	    	if($lastname != ""){
	    		$updateSql .= ", lastname = N'$lastname'";
	    	}
	    	if($email != ""){
	    		$updateSql .= ", email = '$email'";
	    	}
	    	if($password != ""){
	    		$password = passwordEncryption($password, $student_id);
	    		$updateSql .= ", password = '$password'";
	    	}
	    	if($profilepic != ""){
	    		$updateSql .= ", profilepic = '$profilepic'";
	    	}
	    	if($GPA != ""){
	    		$updateSql .= ", GPA = '$GPA'";
	    	}
	    	if($student_status != ""){
	    		$updateSql .= ", student_status = '$student_status'";
	    	}

	    	$updateSql = " WHERE student_id = '$student_id'";
	    }

	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
    	if ($updateSql != ""){
    		$db = new DBManager();
	        $db->beginSet();
	        if($db->setData($sql))
	        {
	        	$db->commitWork();
	            $app->response->setBody(json_encode(array("status"=>"success")));
	        }
	        else
			{
				$db->rollbackWork();
	            $app->response->setBody(json_encode(array("status"=>"fail")));
	            $app->response->write(json_encode($db->errmsg()));    	
			}
	        $db = null;
    	} else {
    		$app->response->setBody(json_encode(array("status"=>"success")));
    	}
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function deleteStudent(){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $student_detail = json_decode($request->getBody());
	    $sql = "DELETE FROM STUDENT where student_id = '$student_detail->student_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
        $db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
        {
            $db->commitWork();
            $app->response->setBody(json_encode(array("status"=>"success")));
        }
        else
        {
            $db->rollbackWork();
            $app->response->setBody(json_encode(array("status"=>"fail")));
            $app->response->write(json_encode($db->errmsg()));   
        }
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function resetStudentPassword(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $student_id = json_decode($request->getBody())->student_id;
	    $newpassword = passwordEncryption($student_id, passwordEncryption($student_id, 'welcome1'));
		$sql = "UPDATE STUDENT set password = '$newpassword' where student_id = '$student_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
        {
            $db->commitWork();
            $app->response->setBody(json_encode(array("status"=>"success")));
        }
        else
        {
            $db->rollbackWork();
            $app->response->setBody(json_encode(array("status"=>"fail")));
            $app->response->write(json_encode($db->errmsg()));   
        }
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function passwordEncryption($a, $b){
	return sha1($a.$b);
}
?>