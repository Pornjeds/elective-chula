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

		//1. get student's detail and active semester
		$classof_id = "";
		$semester = "";
		$semester_state = 0;

		$sql = "exec getStudentDetail @student_id = '$student_id'";
		$result = $db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$classof_id = $row["classof_id"];
				$semester = $row["semester"];
				$semester_state = $row["semester_state"];
			}
		}

		if ($semester_state != 1){
			$app->response->setBody(json_encode(array("error"=>array("source"=>"logic", "reason"=>"This semester is not opened for registration yet"))));
			return;
		}

		//2. get subject list
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

		//3. get subject list
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
	$jsonData = '{
			"subject_enrollment": 
				[
					{
						"subject_id": 1,
						"priority": 3
					},
					{
						"subject_id": 2,
						"priority": 1
					},
					{
						"subject_id": 3,
						"priority": 2
					},
					{
						"subject_id": 4,
						"priority": 5
					},
					{
						"subject_id": 5,
						"priority": 4
					},
					{
						"subject_id": 6,
						"priority": 2
					},
					{
						"subject_id": 7,
						"priority": 1
					}
				],
				"student_id": "5682221822"
			}';
	
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    //$subject_enrollment = json_decode($jsonData)->subject_enrollment;
	    //$student_id = json_decode($jsonData)->student_id;
	    $subject_enrollment = json_decode($request->getBody())->subject_enrollment;
	    $student_id = json_decode($request->getBody())->student_id;



	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}

	try {
		
		$db = new DBManager();

		//1. get student's detail and active semester
		$classof_id = "";
		$semester = "";
		$semester_state = 0;
		$subject_id_list = "";

		$sql = "exec getStudentDetail @student_id = '$student_id'";
		$result = $db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$classof_id = $row["classof_id"];
				$semester = $row["semester"];
				$semester_state = $row["semester_state"];
			}
		}

		if ($semester_state != 1){
			$app->response->setBody(json_encode(array("error"=>array("source"=>"logic", "reason"=>"This semester is not opened for registration yet"))));
			return;
		}

		
		//2. Insert data into enrollment
		foreach($subject_enrollment as $subject){
			$sql = "merge STUDENT_ENROLLMENT as target
				using (values ('$student_id', '$subject->subject_id', '$classof_id', '$semester', '$subject->priority'))
				    as source (student_id, subject_id, classof_id, semester, priority)
				    on target.student_id = source.student_id AND target.subject_id = source.subject_id AND target.classof_id = source.classof_id AND target.semester = source.semester
				when matched then
				    update
				    set priority = source.priority,
				        addeddate = GETDATE()
				when not matched then
				    insert ( student_id, subject_id, classof_id, semester, priority, addeddate)
				    values ( source.student_id,  source.subject_id, source.classof_id, source.semester, source.priority, GETDATE() );";

			$subject_id_list .= "'$subject->subject_id',";

			if($db->setData($sql))
		    {
		        $enrollstatus = true;
		    }
		    else
		    {
		    	$db->rollbackWork();
		        $app->response->setBody(json_encode(array("status"=>"fail")));
		    	$enrollstatus = false;
		    	return;
		    }
		}

		if ($enrollstatus)
		{
			$subject_id_list = substr($subject_id_list,0,-1); //remove the last ,
			$deleteSql = "DELETE FROM STUDENT_ENROLLMENT WHERE subject_id not in ($subject_id_list) AND student_id = '$student_id' AND classof_id = '$classof_id' AND semester = '$semester'";
			if($db->setData($deleteSql))
			{
				$enrollstatus = true;
			}
			else
			{
				$enrollstatus = false;
				$db->rollbackWork();
		        $app->response->setBody(json_encode(array("status"=>"fail")));
		        $app->response->write(json_encode($db->errmsg()));    	
			}
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