<?php
/*
$app->group('/enrollment', function() use ($app){
        $app->post('/list', 'getSubjectList');
        $app->post('/submit', 'submitEnrollment');
    });
*/
function getSubjectList(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $student_id = json_decode($request->getBody())->student_id;

	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}
    
	try {
		$db = new DBManager();
		$response_arr = array();
		$opened_subject_arr = array();
		$selected_subject_arr = array();

		//1. get student's classof_id
		$classof_id = "";

		$sql_getUserInfo = "SELECT classof_id FROM STUDENT WHERE student_id = '$student_id'";		
		$result = $db->getData($sql_getUserInfo);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$classof_id = $row["classof_id"];
			}
		}

		//2. get the active semester
		$semester = "";
		$semester_state = 0;

		$sql_getActiveSemester = "SELECT semester, semester_state FROM CLASSOF_SEMESTER WHERE classof_id = '$classof_id' AND semester_state = '1'";
		$result = $db->getData($sql_getActiveSemester);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$semester = $row["semester"];
				$semester_state = $row["semester_state"];
			}
		}

		if ($semester_state != 1){
			$app->response->setBody(json_encode(array("error"=>array("source"=>"logic", "reason"=>"This semester is not opened for registration yet"))));
			return;
		}

		//3. get subject list
		$sql_getSubjectList = "SELECT  
									a.subject_id,
									a.dayofweek,
									a.timeofday,
									a.isRequired,
									a.credit,
									b.name,
									b.description,
									a.minstudent,
									a.maxstudent,
									COUNT(c.student_id) AS studentcount
								FROM SUBJECT_CLASSOF a 
								INNER JOIN SUBJECT b ON a.subject_id = b.subject_id
								LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
								WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
								GROUP BY a.subject_id, b.name, b.description, a.minstudent, a.maxstudent, a.dayofweek, a.timeofday, a.isRequired, a.credit
								ORDER BY b.name ASC";
		$result = $db->getData($sql_getSubjectList);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($opened_subject_arr, $row);
			}
		}

		//4. get subject list
		$sql_getSelectedSubjectList = "SELECT 
										a.subject_id,
										a.priority,
										b.name
										FROM STUDENT_ENROLLMENT a
										INNER JOIN SUBJECT b ON a.subject_id = b.subject_id
										WHERE student_id = '$student_id' AND 
												classof_id = '$classof_id' AND 
												semester = '$semester'";
		$result = $db->getData($sql_getSelectedSubjectList);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($selected_subject_arr, $row);
			}
		}

		$db = null;
	} catch(PDOException $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }

    $tmp_arr1 = array('subject_list' => $opened_subject_arr);
    $tmp_arr2 = array('selected' => $selected_subject_arr);

    array_push($response_arr, $tmp_arr1);
    array_push($response_arr, $tmp_arr2);

    $app->response->setBody(json_encode($response_arr));
}

function submitEnrollment(){
	
}

?>