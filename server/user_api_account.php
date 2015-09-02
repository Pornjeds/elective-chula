<?php
/*
$app->group('/account', function() use ($app){
        $app->post('/list', 'getUserDashboardInfo');
    });
*/
function getUserDashboardInfo(){


	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    //$student_id = json_decode($request->getBody())->student_id;
	    $student_id = $_SESSION['loginUsername'];

	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}
    
	try {
		$db = new DBManager();
		$response_arr = array();
	    $userdata_arr = array();
	    $subject_arr = array();

		//1. get student detail
		$name = "";
		$lastname = "";
		$classof_id = "";

		$sql_getUserInfo = "SELECT student_id, name, lastname, classof_id FROM STUDENT WHERE student_id = '$student_id'";
		$result = $db->getData($sql_getUserInfo);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$name = $row["name"];
				$lastname = $row["lastname"];
				$classof_id = $row["classof_id"];
				array_push($userdata_arr, $row);
			}
		}
	
    	//2. get active/status CLASSOF_SEMESTER
    	$semester = "";
		$semester_state = "";
		$mincredit = "";
		$maxcredit = "";

		$sql_getSubjectState = "SELECT * FROM CLASSOF_SEMESTER WHERE classof_id = '$classof_id' AND semester_state = 1";
		$result = $db->getData($sql_getSubjectState);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$semester = $row["semester"];
				$semester_state = $row["semester_state"];
				$mincredit = $row["mincredit"];
				$maxcredit = $row["maxcredit"];
			}
		}
	
		//3. get opened subject (of that specific classof and semester)
		$sql_getSubjectList = "SELECT  
									a.subject_id,
									b.name,
									b.description,
									a.minstudent,
									a.maxstudent,
									COUNT(c.student_id) AS studentcount,
									SUM(CASE WHEN c.priority = 1 THEN 1 ELSE 0 END) AS first_studentcount,
									SUM(CASE WHEN c.priority = 2 THEN 1 ELSE 0 END) AS second_studentcount,
									SUM(CASE WHEN c.priority = 3 THEN 1 ELSE 0 END) AS third_studentcount
								FROM SUBJECT_CLASSOF a 
								INNER JOIN SUBJECT b ON a.subject_id = b.subject_id
								LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id AND c.classof_id = '$classof_id' AND c.semester = '$semester'
								WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
								GROUP BY a.subject_id, b.name, b.description, a.minstudent, a.maxstudent
								ORDER BY b.name ASC";
		$result = $db->getData($sql_getSubjectList);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($subject_arr, $row);
			}
		}
		$db = null;

	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }

    //merge 1,2 and 3
    $tmp_arr1 = array('userdata' => $userdata_arr);
    $tmp_arr2 = array('semester_state' => $semester_state);
	$tmp_arr3 = array('subjectlist' => $subject_arr);

	array_push($response_arr, $tmp_arr1);
	array_push($response_arr, $tmp_arr2);
	array_push($response_arr, $tmp_arr3);

    $app->response->setBody(json_encode($response_arr));
}

function checkCurrentPassword() {
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    //$student_id = json_decode($request->getBody())->student_id;
	    $student_id = $_SESSION['loginUsername'];
	    $hashPassword = json_decode($request->getBody())->hashPassword;
	    $hashPassword = sha1($student_id.$hashPassword);

	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}
    
	try {
		$db = new DBManager();
		$sql = "SELECT COUNT(1) AS cnt FROM STUDENT where student_id = '$student_id' AND password = '$hashPassword'";
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$cnt = $row['cnt'];
				$app->response->setBody(json_encode(array("status"=>"success", "cnt"=>"$cnt")));
			}
		}
		$db = null;

	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }
}

function updatePassword() {
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    //$student_id = json_decode($request->getBody())->student_id;
	    $student_id = $_SESSION['loginUsername'];
	    $hashPassword = json_decode($request->getBody())->hashPassword;
	    $hashPassword = sha1($student_id.$hashPassword);

	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}
    
	try {
		$db = new DBManager();
		$sql = "UPDATE STUDENT SET password = '$hashPassword', updatedate = GETDATE() WHERE student_id = '$student_id'";
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
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }
}


?>