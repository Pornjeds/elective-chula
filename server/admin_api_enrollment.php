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
		$app->response->headers->set('Content-Type', 'application/json');
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

			//ตอนนี้ได้ TMP_SELECTION ที่มี STANDBY กับ ACCEPTED มาแล้ว
			//ทำการเกลี่ยคนที่เป็น STANDBY เข้่ามาเป็น ACCEPTED จนกว่าจะเต็ม maxstudent ของวิชานั้นๆ
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
			$someusers_dont_have_enough_credit = true;
			$count_while = 0;
			while($someusers_dont_have_enough_credit && $count_while < 50){
				$student_list = listStudentFromTmpSelectionSortedByAcceptedCount($db, $classof_id, $semester);
				foreach($student_list as $student_id){
					
					$student_subject_arr = getStudentSubjectConfirmedList($db, $student_id, $mincredit, $maxcredit, $classof_id, $semester);
					$subject_id_confirmed_arr = $student_subject_arr["subject_id_confirmed_arr"];
					$subject_id_NOT_confirmed_arr = $student_subject_arr["subject_id_NOT_confirmed_arr"];
					$someusers_dont_have_enough_credit = $student_subject_arr["someusers_dont_have_enough_credit"] && $someusers_dont_have_enough_credit;

					//ทำการ mark flag ว่าวิชานี้ของคนคนนี้ confirm แล้ว และจะลบวิชาที่ทำให้หน่วยกิตเกินออกไปเลย
					foreach($subject_id_confirmed_arr as $subject_id){
						$sql_confirm = "exec enrollStudentBasedOnCredit @student_id = '$student_id', @classof_id = '$classof_id', @semester = '$semester', @subject_id = '$subject_id'";
						if(!$db->setData($sql_confirm))
						{
							$db->rollbackWork();
							$app->response->setBody(json_encode(array("status"=>"fail4")));
							return;
						}
					}

					foreach($subject_id_NOT_confirmed_arr as $subject_id){
						//ลบวิชาที่นิสิตคนนี้ไม่ลงทะเบียนออกจาก TMP_SELECTION เพื่อให้โอกาสคนที่ลงทะเบียนได้น้อยมีสิทธิ์ได้เรียน
						$sql_remove = "DELETE FROM TMP_SELECTION WHERE subject_id = '$subject_id' AND classof_id = '$classof_id' AND semester = '$semester' AND student_id = '$student_id'";
						if(!$db->setData($sql_remove))
						{
							$db->rollbackWork();
							$app->response->setBody(json_encode(array("status"=>"fail5")));
							return;
						}

						//แล้วทำการ reconcile เพื่อดึงคนที่เป็น STANDBY ขึ้นมาเพิ่ม (แทนที่ว่างที่พึ่งถูกลบไป)
						$sql_reconcile = "exec enrollReconcile @subject_id = '$subject_id', @classof_id = '$classof_id', @semester = '$semester'";
						if(!$db->setData($sql_reconcile))
						{
							$db->rollbackWork();
							$app->response->setBody(json_encode(array("status"=>"fail6")));
							return;
						}
					}
				}

				$count_while++;
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

function listStudentFromTmpSelectionSortedByAcceptedCount($db, $classof_id, $semester){
	$response_arr = array();
	$sql = "SELECT student_id, 
			SUM(CASE WHEN type='ACCEPTED' AND status <> 'CONFIRM' THEN 1 ELSE 0 END) AS accept_cnt
			FROM TMP_SELECTION
			WHERE classof_id = '$classof_id' AND semester = '$semester'
			GROUP BY student_id
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

function getStudentSubjectConfirmedList($db, $student_id, $mincredit, $maxcredit, $classof_id, $semester){
	$current_sum_credit = 0;
	$subject_id_confirmed_arr = array();
	$subject_id_NOT_confirmed_arr = array();
	$dayofweek_timeofday = array();
	$duplicate_date_and_time = false;
	$someusers_dont_have_enough_credit = false;
	
	$sql = "SELECT subject_id, credit, dayofweek, timeofday
			FROM TMP_SELECTION 
			WHERE student_id = '$student_id' AND type = 'ACCEPTED' AND classof_id = '$classof_id' AND semester = '$semester'
			ORDER BY priority ASC";

	$result = $db->getData($sql);
	if($result){
		while($row = sqlsrv_fetch_array($result)){
			$duplicate_date_and_time = false;
			$subject_id = trim($row['subject_id']);
			$subject_credit = trim($row['credit']);
			$dayofweek = trim($row['dayofweek']);
			$timeofday = trim($row['timeofday']);
			//check ก่อรว่ามันไปทับกับ วันและเวลาที่เราลงทะเบียนได้แล้วรึป่าว
			foreach($dayofweek_timeofday as $dt){
				if ($dayofweek == $dt["dayofweek"] && $timeofday == $dt["timeofday"]){
					$duplicate_date_and_time = true;
					break;
				}
			}
			if ($current_sum_credit + $subject_credit <= $maxcredit && !$duplicate_date_and_time){
				$current_sum_credit += $subject_credit;
				array_push($subject_id_confirmed_arr, $subject_id);
				array_push($dayofweek_timeofday, array("dayofweek" => $dayofweek, "timeofday" => $timeofday ));
			} else {
				array_push($subject_id_NOT_confirmed_arr, $subject_id);
			}
		}

		if ($current_sum_credit < $maxcredit){
			$someusers_dont_have_enough_credit = true;
		}
	}

	return array(
		"subject_id_confirmed_arr" => $subject_id_confirmed_arr,
		"subject_id_NOT_confirmed_arr" => $subject_id_NOT_confirmed_arr,
		"someusers_dont_have_enough_credit" => $someusers_dont_have_enough_credit
	);
}

?>