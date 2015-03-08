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
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
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
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
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
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
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
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
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
	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}

	//2. Loop through those subject one by one
	try {

		$sql = "select 
				a.subject_id,
				COUNT(c.student_id) AS studentcount,
				d.mincredit,
				d.maxcredit,
				d.pickmethod_id
				FROM SUBJECT_CLASSOF a
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				LEFT JOIN CLASSOF_SEMESTER d ON a.classof_id = d.classof_id AND a.semester = d.semester
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'
				GROUP BY a.subject_id, d.mincredit, d.maxcredit, d.pickmethod_id
				ORDER BY studentcount DESC";

		$db = new DBManager();
		$result = $db->getData($sql);
		$subject_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$subject_id = $row['subject_id'];
				$mincredit = $row['mincredit'];
				$maxcredit = $row['maxcredit'];
				$pickmethod_id = $row['pickmethod_id'];
				$studentcount = $row['studentcount'];

				//3. On each subject, perform a Selection which yield results of Students who got accepted and Students who didn't get accepted
				if($pickmethod_id == 1){
					//first come first serve
					$sql_random = "exec enrollFirstComeFirstServe @subject_id = '$subject_id', @classof_id = '$classof_id', @semester = '$semester'";
				}elseif($pickmethod_id == 2){
					//sort by GPA
					$sql_random = "exec enrollGPA @subject_id = '$subject_id', @classof_id = '$classof_id', @semester = '$semester'";
				}elseif($pickmethod_id == 3){
					//random by rank
					$sql_random = "exec enrollRanking @subject_id = '$subject_id', @classof_id = '$classof_id', @semester = '$semester'";
				}

				array_push($subject_arr, $subject_id);

				$db->beginSet();
				if(!$db->setData($sql_random))
				{
					$db->rollbackWork();
					$app->response->setBody(json_encode(array("status"=>"fail1")));
					return;
				}
			}

			//ตอนนี้ได้ TMP_SELECTION ที่มี STANDBY กับ ALMOSTACCEPTED มาแล้ว
			//ทำการเกลี่ยคนที่เป็น STANDBY เข้่ามาเป็น ALMOSTACCEPTED จนกว่าจะเต็ม maxstudent ของวิชานั้นๆ
			foreach($subject_arr as $subject_id){
				$sql_reconcile = "exec enrollReconcile @subject_id = '$subject_id', @classof_id = '$classof_id', @semester = '$semester'";
				if(!$db->setData($sql_reconcile))
				{
					$db->rollbackWork();
					$app->response->setBody(json_encode(array("status"=>"fail2")));
					return;
				}
			}

			//ในกรณีที่มีการนำหน่วยกิจเข้ามาคิด (min, max ที่ นศ จะลงได้ เราก็จะต้องจัดให้เค้าได้ลงตาม ranking ที่เค้าส่งมา และถ้าเกินก็จะต้องตัดวิชาที่ priority น้อยออกไป)
			$student_arr = listStudentTmpSelectionSortByAcceptedCount($db);
			$student_haveEnoughCredit = array();
			$student_haveNOTEnoughCredit = array();
			$firstRound = true;

			while($firstRound || count($student_haveNOTEnoughCredit) > 0){
				
				$student_haveEnoughCredit = array();
				$student_haveNOTEnoughCredit = array();
				$firstRound = false;

				foreach($student_arr as $student_id){
					//ดึง credit ของแต่ละวิชาที่นศคนนี้ได้มาคำนวณเพื่อหาว่าเค้าควรจะได้เรียนกี่วิชา
					$calStudetCredit = calculateStudentCredit($db, $student_id, $mincredit, $maxcredit, $student_haveEnoughCredit, $student_haveNOTEnoughCredit);
					$student_haveEnoughCredit = $calStudetCredit['student_haveEnoughCredit'];
					$student_haveNOTEnoughCredit = $calStudetCredit['student_haveNOTEnoughCredit'];
					$subject_id_confirm_list = $calStudetCredit['subject_id_confirm_list'];
					$subject_id_NOTconfirm_arr = $calStudetCredit['subject_id_NOTconfirm_arr'];

					if ($calStudetCredit['haveEnoughCreditToProcess']){
						$sql_student_reconcile = "exec enrollStudentBasedOnCredit @student_id = '$student_id', @subject_id_list = '$subject_id_confirm_list'";
						if(!$db->setData($sql_student_reconcile))
						{
							$db->rollbackWork();
							$app->response->setBody(json_encode(array("status"=>"fail3")));
							return;
						}
						foreach($subject_id_NOTconfirm_arr as $subject_id_NOTconfirm){
							$subject_id_NOTconfirm = $subject_id_NOTconfirm;

							$sql_reconcile = "exec enrollReconcile @subject_id = '$subject_id_NOTconfirm', @classof_id = '$classof_id', @semester = '$semester'";
							if(!$db->setData($sql_reconcile))
							{
								$db->rollbackWork();
								$app->response->setBody(json_encode(array("status"=>"fail4")));
								return;
							}
						}
					}
					
				}
			}


			$db->commitWork();
			$app->response->setBody(json_encode(array("status"=>"success")));

		} else {
			$app->response->setBody(json_encode(array("status"=>"fail5")));
			return;
		}

		$db = null;
        
	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
    }
}

function listStudentTmpSelectionSortByAcceptedCount($db){
	$response_arr = array();
	$sql = "SELECT student_id, 
			SUM(CASE WHEN type='ACCEPTED' AND status <> 'CONFIRM' THEN 1 ELSE 0 END) AS accept_cnt
			FROM TMP_SELECTION
			group  by student_id
			ORDER BY accept_cnt DESC, student_id ASC";
	$result = $db->getData($sql);
	if($result){
		while($row = sqlsrv_fetch_array($result)){
			//ดึง credit ของแต่ละวิชาที่นศคนนี้ได้มาคำนวณเพื่อหาว่าเค้าควรจะได้เรียนกี่วิชา
			array_push($response_arr, $row['student_id']);
		}
	}

	return $response_arr;
}

function calculateStudentCredit($db, $student_id, $mincredit, $maxcredit, $student_haveEnoughCredit, $student_haveNOTEnoughCredit){
	$current_sum_credit = 0;
	$subject_id_confirm_list = "";
	$subject_id_NOTconfirm_arr = array();
	$haveEnoughCreditToProcess = false;
	
	$sql = "SELECT subject_id, credit FROM TMP_SELECTION WHERE student_id = '$student_id' AND type = 'ACCEPTED' ORDER BY priority ASC";
	$result = $db->getData($sql);
	if($result){
		while($row = sqlsrv_fetch_array($result)){
			$subject_id = trim($row['subject_id']);
			$subject_credit = trim($row['credit']);
			if ($current_sum_credit + $subject_credit <= $maxcredit){
				$current_sum_credit += $subject_credit;
				$subject_id_confirm_list .= "''$subject_id'',";
			} else {
				array_push($subject_id_NOTconfirm_arr, $subject_id);
			}
		}

		if ($current_sum_credit >= $mincredit && $current_sum_credit <= $maxcredit){
			$subject_id_confirm_list = substr($subject_id_confirm_list, 0, -1);
			array_push($student_haveEnoughCredit, $student_id);
			$haveEnoughCreditToProcess = true;
		} else {
			array_push($student_haveNOTEnoughCredit, $student_id);
		}
		
	}

	return array(
		"subject_id_confirm_list" => $subject_id_confirm_list,
		"subject_id_NOTconfirm_arr" => $subject_id_NOTconfirm_arr,
		"haveEnoughCreditToProcess" => $haveEnoughCreditToProcess,
		"student_haveEnoughCredit" => $student_haveEnoughCredit,
		"student_haveNOTEnoughCredit" => $student_haveNOTEnoughCredit
	);
}

?>