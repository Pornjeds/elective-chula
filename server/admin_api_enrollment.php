<?php
/*
$app->group('/enrollmentadmin', function() use ($app){
        $app->get('/:id', 'getSubjectEnrollmentInfoById');
        $app->post('/detail', 'getSubjectEnrollmentInfoByIdPost');
        $app->post('/list', 'listEnrollmentByClassOfAndSemester');
        $app->post('/liststudent', 'listEnrollmentResultBySubject');
        $app->post('/listpickmethod', 'listPickMethod');
        $app->post('/run', 'performEnrollment');
*/
function getSubjectEnrollmentInfoById($id){

}

function getSubjectEnrollmentInfoByIdPost(){

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
				COUNT(c.student_id) AS CountStudent,
				a.maxstudent,
				CAST(CASE WHEN a.minstudent <> 0 THEN CAST(a.minstudent AS NCHAR(10)) ELSE 'No' END AS NCHAR(10)) AS minstudent,
				CASE
					WHEN COUNT(c.student_id) > a.maxstudent THEN 'Must choose' 
					WHEN COUNT(c.student_id) <= a.maxstudent AND COUNT(c.student_id) >= a.minstudent THEN 'Can open'
					WHEN COUNT(c.student_id) < a.minstudent THEN 'Cannot open'
				END AS SubjectStatus
				FROM SUBJECT_CLASSOF a
				LEFT JOIN SUBJECT b ON a.subject_id = b.subject_id
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
				GROUP BY a.subject_id, b.name, a.dayofweek, a.timeofday, a.maxstudent, minstudent";
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
	
}

?>