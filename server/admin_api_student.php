<?php
function getStudentById($id){
	try {
	    $app = \Slim\Slim::getInstance();
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
		$app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function getStudentByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
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
		$app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function getStudentByClassOf(){
	try {
		$app = \Slim\Slim::getInstance();
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
		$request = $app->request();
	    $student_detail = json_decode($request->getBody());
	    $sql = "INSERT INTO STUDENT (student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status) VALUES ('$student_detail->student_id', '$student_detail->classof_id', '$student_detail->name', '$student_detail->lastname', '$student_detail->email', '$student_detail->password', '$student_detail->profilepic', '$student_detail->GPA', GETDATE(), '$student_detail->student_status')";
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
		$request = $app->request();
	    $student_detail = json_decode($request->getBody());
	    $sql = "UPDATE STUDENT SET 
	    	classof_id = '$student_detail->classof_id', 
	    	name = '$student_detail->name', 
	    	lastname = '$student_detail->lastname', 
	    	email = '$student_detail->email', 
	    	password = '$student_detail->password', 
	    	profilepic = '$student_detail->profilepic', 
	    	GPA = '$student_detail->GPA', 
	    	updatedate = GETDATE(), 
	    	student_status = '$student_detail->student_status' 
			WHERE student_id = '$student_detail->student_id'";
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

function deleteStudent(){
	try {
	    $app = \Slim\Slim::getInstance();
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
?>