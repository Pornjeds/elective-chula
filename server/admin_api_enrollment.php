<?php
/*
$app->group('/enrollmentadmin', function() use ($app){
        $app->post('/detail', 'getSubjectEnrollmentInfoByIdPost');
        $app->post('/list', 'listEnrollmentByClassOfAndSemester');
        $app->post('/liststudent', 'listEnrollmentResultBySubject');
        $app->post('/listpickmethod', 'listPickMethod');
        $app->post('/run', 'performEnrollment');
*/

function getSubjectEnrollmentInfoByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $subject_id = json_decode($request->getBody())->subject_id;
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
		$sql = "select 
				a.subject_id,
				b.name,
				a.dayofweek,
				a.timeofday,
				COUNT(c.student_id) AS studentcount,
				a.maxstudent,
				d.pickmethod_id,
				e.name,
				CAST(CASE WHEN a.minstudent <> 0 THEN CAST(a.minstudent AS NCHAR(10)) ELSE 'No' END AS NCHAR(10)) AS minstudent,
				CASE
					WHEN COUNT(c.student_id) > a.maxstudent THEN 'Must choose' 
					WHEN COUNT(c.student_id) <= a.maxstudent AND COUNT(c.student_id) >= a.minstudent THEN 'Can open'
					WHEN COUNT(c.student_id) < a.minstudent THEN 'Cannot open'
				END AS SubjectStatus
				FROM SUBJECT_CLASSOF a
				LEFT JOIN SUBJECT b ON a.subject_id = b.subject_id
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				LEFT JOIN CLASSOF_SEMESTER d ON a.classof_id = d.classof_id AND a.semester = d.semester
				LEFT JOIN PICKMETHOD e ON d.pickmethod_id = e.pickmethod_id
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester' AND a.subject_id = '$subject_id'
				GROUP BY a.subject_id, b.name, a.dayofweek, a.timeofday, a.maxstudent, d.pickmethod_id, d.pickmethod_id, e.name, minstudent";
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

function listEnrollmentByClassOfAndSemester(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
		$sql = "select 
				a.subject_id,
				b.name,
				a.dayofweek,
				a.timeofday,
				COUNT(c.student_id) AS studentcount,
				a.maxstudent,
				d.pickmethod_id,
				e.name,
				CAST(CASE WHEN a.minstudent <> 0 THEN CAST(a.minstudent AS NCHAR(10)) ELSE 'No' END AS NCHAR(10)) AS minstudent,
				CASE
					WHEN COUNT(c.student_id) > a.maxstudent THEN 'Must choose' 
					WHEN COUNT(c.student_id) <= a.maxstudent AND COUNT(c.student_id) >= a.minstudent THEN 'Can open'
					WHEN COUNT(c.student_id) < a.minstudent THEN 'Cannot open'
				END AS SubjectStatus
				FROM SUBJECT_CLASSOF a
				LEFT JOIN SUBJECT b ON a.subject_id = b.subject_id
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				LEFT JOIN CLASSOF_SEMESTER d ON a.classof_id = d.classof_id AND a.semester = d.semester
				LEFT JOIN PICKMETHOD e ON d.pickmethod_id = e.pickmethod_id
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
				GROUP BY a.subject_id, b.name, a.dayofweek, a.timeofday, a.maxstudent, d.pickmethod_id, e.name, minstudent";
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

function listEnrollmentResultBySubject(){


}

function listPickMethod(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "SELECT pickmethod_id, name FROM PICKMETHOD order by name";
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

function performEnrollment(){
	//1. Gather input and init a sql statement to get list of opened subject ordered by count student enrolled
	try {
		$app = \Slim\Slim::getInstance();
		//$app->response->headers->set('Content-Type', 'application/json');
		$request = $app->request();
		$classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
	    $request = $app->request();
		$sql = "select 
				a.subject_id,
				b.name,
				a.dayofweek,
				a.timeofday,
				COUNT(c.student_id) AS studentcount,
				a.minstudent,
				a.maxstudent,
				d.pickmethod_id
				FROM SUBJECT_CLASSOF a
				LEFT JOIN SUBJECT b ON a.subject_id = b.subject_id
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				LEFT JOIN CLASSOF_SEMESTER d ON a.classof_id = d.classof_id AND a.semester = d.semester
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
				GROUP BY a.subject_id, b.name, a.dayofweek, a.timeofday, a.minstudent,a.maxstudent, d.pickmethod_id
				ORDER BY studentcount DESC, dayofweek ASC";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

	//2. Loop through those subject one by one
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$subject_id = $row['subject_id'];
				$pickmethod_id = $row['pickmethod_id'];
				//3. On each subject, perform a Selection which yield results of Students who got accepted and Students who didn't get accepted
				if(performSelection($subject_id, $pickmethod_id)){
					//4.1 Loop through Students who got accepted, check if there is any selected higher priority subject

						//4.1.1 Got higher subject priority (on any date) -> put the user into TMP_STANDBY as standby and countStandby+=1

						//4.1.2 This is the highest priority subject -> put the user into TMP_STANDBY as an almost accepted

					//4.2 Loop through Students who didn't get accepted -> move its subject assigned ranking to be the lowest (this to make sure that he will got an automatic higher priority on the rest subject)

						//4.2.1 Insert these students into TMP_STANDBY standby
				}

			}
		}
		$db = null;
        
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function performSelection($subject_id, $pickmethod_id){
	$result = true;
	if($pickmethod_id == 1){
		//first come first serve

	}elseif($pickmethod_id == 2){
		//sort by GPA

	}elseif($pickmethod_id == 3){
		//random by rank

	}
	return $result;
}

?>