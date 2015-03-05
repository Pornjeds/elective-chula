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
	    $student_id = json_decode($request->getBody())->student_id;
	    $response_arr = array();
	    $userdata_arr = array();
	    $subject_arr = array();

	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
    //get student detail
	try {
		$name = "";
		$lastname = "";
		$classof_id = "";

		$sql_getUserInfo = "SELECT student_id, name, lastname, classof_id FROM STUDENT WHERE student_id = '$student_id'";
		$db = new DBManager();
		$result = $db->getData($sql_getUserInfo);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$name = $row["name"];
				$lastname = $row["lastname"];
				$classof_id = $row["classof_id"];
				array_push($userdata_arr, $row);
			}
		}
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
        return;
    }

    //get active/status CLASSOF_SEMESTER
    try {
    	$semester = "";
		$semester_state = "";
		$mincredit = "";
		$maxcredit = "";

		$sql_getSubjectState = "SELECT * FROM CLASSOF_SEMESTER WHERE classof_id = '$classof_id' AND semester_state <> 0";
		$db = new DBManager();
		$result = $db->getData($sql_getSubjectState);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$semester = $row["semester"];
				$semester_state = $row["semester_state"];
				$mincredit = $row["mincredit"];
				$maxcredit = $row["maxcredit"];
			}
		}
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
        return;
    }

	//get opened subject (of that specific classof and semester)
	try {
		$sql_getSubjectList = "SELECT  
									a.subject_id,
									b.name,
									b.description,
									a.minstudent,
									a.maxstudent,
									COUNT(c.student_id) AS studentcount
								FROM SUBJECT_CLASSOF a 
								INNER JOIN SUBJECT b ON a.subject_id = b.subject_id
								LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
								WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
								GROUP BY a.subject_id, b.name, b.description, a.minstudent, a.maxstudent
								ORDER BY b.name ASC";
		$db = new DBManager();
		$result = $db->getData($sql_getSubjectList);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($subject_arr, $row);
			}
		}
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
        return;
    }

    $tmp_arr1 = array('userdata' => $userdata_arr);
    $tmp_arr2 = array('semester_state' => $semester_state);
	$tmp_arr3 = array('subjectlist' => $subject_arr);

	array_push($response_arr, $tmp_arr1);
	array_push($response_arr, $tmp_arr2);
	array_push($response_arr, $tmp_arr3);

	$db = null;
    $app->response->setBody(json_encode($response_arr));
}

?>