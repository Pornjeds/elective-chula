<?php
/*
$app->group('/enrollmentadmin', function() use ($app){
        $app->post('/detail', 'getSubjectEnrollmentInfoByIdPost');
        $app->post('/status', 'getEnrollmentStatusByClassOfAndSemester');
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

	    $semester_state = getSemesterState($classof_id, $semester);

		$sql = "select 
				a.subject_id,
				b.name AS subject_name,
				a.dayofweek,
				a.timeofday,
				COUNT(c.student_id) AS studentcount,
				a.maxstudent,
				d.pickmethod_id,
				e.name AS pickmethod,
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

function getEnrollmentStatusByClassOfAndSemester(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
		$sql = "SELECT a.semester_state AS semester_state, 
						b.name AS pickmethod
				FROM CLASSOF_SEMESTER a
				INNER JOIN PICKMETHOD b ON a.pickmethod_id = b.pickmethod_id
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'";
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

	$semester_state = 0;
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;

	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	$semester_state = getSemesterState($classof_id, $semester);

    try {
    	if ($semester_state == 2) {
    		$table = "STUDENT_CONFIRMED_ENROLLMENT";
    	} else {
    		$table = "STUDENT_ENROLLMENT";
    	}	

		$sql = "select 
				a.subject_id,
				b.name as subject_name,
				a.dayofweek,
				a.timeofday,
				COUNT(c.student_id) AS studentcount,
				a.maxstudent,
				d.pickmethod_id,
				e.name as pickmethod,
				CAST(CASE WHEN a.minstudent <> 0 THEN CAST(a.minstudent AS NCHAR(10)) ELSE 'No' END AS NCHAR(10)) AS minstudent,
				CASE
					WHEN COUNT(c.student_id) > a.maxstudent THEN 'Must choose' 
					WHEN COUNT(c.student_id) <= a.maxstudent AND COUNT(c.student_id) >= a.minstudent THEN 'Can open'
					WHEN COUNT(c.student_id) < a.minstudent THEN 'Cannot open'
				END AS subject_status
				FROM SUBJECT_CLASSOF a
				LEFT JOIN SUBJECT b ON a.subject_id = b.subject_id
				LEFT JOIN $table c ON a.subject_id = c.subject_id
				LEFT JOIN CLASSOF_SEMESTER d ON a.classof_id = d.classof_id AND a.semester = d.semester
				LEFT JOIN PICKMETHOD e ON d.pickmethod_id = e.pickmethod_id
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester' AND c.classof_id = '$classof_id' AND c.semester = '$semester'
				GROUP BY a.subject_id, b.name, a.dayofweek, a.timeofday, a.maxstudent, d.pickmethod_id, e.name, minstudent";
	    
    } catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
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

	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $subject_id = json_decode($request->getBody())->subject_id;
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;

	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

	$semester_state = getSemesterState($classof_id, $semester);

	if ($semester_state == 2) {
    	$table = "STUDENT_CONFIRMED_ENROLLMENT";
    } else {
    	$table = "STUDENT_ENROLLMENT";
    }

    $sql = "SELECT 
    		a.student_id AS student_id,
    		b.name AS student_name,
    		a.addeddate AS addeddate
    		FROM $table a
			INNER JOIN STUDENT b ON a.student_id = b.student_id
			WHERE a.classof_id = '$classof_id' AND a.semester = '$semester' AND a.subject_id = '$subject_id'
			";
    
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

function listEnrollmentResultBySubjectGet($classof_id, $semester, $subject_id){

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Disposition', 'attachment;filename='.$subject_id.'.csv');
	$semester_state = getSemesterState($classof_id, $semester);

	if ($semester_state == 2) {
    	$table = "STUDENT_CONFIRMED_ENROLLMENT";
    } else {
    	$table = "STUDENT_ENROLLMENT";
    }

    $sql = "SELECT 
    		a.student_id AS student_id,
    		b.name AS student_name
    		FROM $table a
			INNER JOIN STUDENT b ON a.student_id = b.student_id
			WHERE a.classof_id = '$classof_id' AND a.semester = '$semester' AND a.subject_id = '$subject_id'
			";
    
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				echo $row['student_id'].",\n";
			}
		}
		$db = null;
	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
    }
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
	    $db = new DBManager();
		$AdminEnroll = new AdminStudentEnrollment($app, $db, $classof_id, $semester);
	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}

	//2. Loop through those subject one by one (sorted by student count วิชาไหนคนลงเยอะก็เอามาคิดก่อน)
	try {
		
		$db->beginSet();
		//check ก่อนว่า status ของ classof - semester นั้นเป็น 1 หรือป่าว ถ้าเป็น 1 ถึงทำ
		//0 หมายถึงเทอมนั้นไม่ active
		//1 หมายถึงเทอมนั้น active และเปิดให้ลงทะเบียน
		//2 หมายถึงทำการเลือกแล้วและรู้ผลแล้ว
		$semester_state = $AdminEnroll->getSemesterState();
		$pickmethod_id = $AdminEnroll->pickmethod_id;

		if ($semester_state != 1){
			$app->response->setBody(json_encode(array("status"=>"Not in the correct semester state to call this function")));
			return;
		}
		
		$subject_id_sortedby_studentcount = $AdminEnroll->getSubjectArraySortedStudentEnrollment();
		foreach($subject_id_sortedby_studentcount as $subject_id){
			
			//3. On each subject, perform a Selection which yield results of Students who got accepted and Students who didn't get accepted
			if($pickmethod_id == 1){
				//first come first serve
				$AdminEnroll->enrollFirstComeFirstServe($subject_id);
			}elseif($pickmethod_id == 2){
				//sort by GPA
				$AdminEnroll->enrollGPA($subject_id);
			}elseif($pickmethod_id == 3){
				//random by rank
				$AdminEnroll->enrollRanking($subject_id);
			}
			
		}
		
		//ตอนนี้ได้ TMP_SELECTION ที่มี STANDBY กับ ACCEPTED มาแล้ว
		//ทำการเกลี่ยคนที่เป็น STANDBY เข้่ามาเป็น ACCEPTED จนกว่าจะเต็ม maxstudent ของวิชานั้นๆ
		foreach($subject_id_sortedby_studentcount as $subject_id){
			$AdminEnroll->enrollReconcile($subject_id);
		}
		
		//ในกรณีที่มีสิทธิ์ลงทะเบียนได้หลายตัว(เกินกว่า maxcredit) เราจะเลือกให้เค้าลงเฉพาะวิชาที่ priority สูงๆของเค้าเท่านั้น ส่วนวิชาที่ priority ต่ำที่เค้ามีสิธิ์ลงก็จะถูกยกเลิกไป
		$someuser_dont_have_enough_credit = false;
		$student_list = $AdminEnroll->listStudentFromTmpSelectionSortedByAcceptedCount();

		foreach($student_list as $student_id){

			$student_subject_arr = $AdminEnroll->getStudentSubjectConfirmedList($student_id);
			
			$subject_id_confirmed_list = $student_subject_arr["subject_id_confirmed_list"];
			$subject_id_tobe_removed_arr = $student_subject_arr["subject_id_tobe_removed_arr"];
			if ($student_subject_arr["thisuser_dont_have_enough_credit"]){
				$someuser_dont_have_enough_credit = true;
			}
			
			//update status ให้เป็น confirm ในวิชาที่ priority สูงและได้รับเลือก
			$AdminEnroll->markAcceptedHighPrioritySubjectStatusToConfirmed($student_id, $subject_id_confirmed_list);
			
			//ทำการ mark flag ว่าวิชานี้ของคนคนนี้ confirm แล้ว และจะลบวิชาที่ทำให้หน่วยกิตเกินออกไปเลย 
			foreach($subject_id_tobe_removed_arr as $subject_id){
				//ลบวิชาที่นิสิตคนนี้ไม่ลงทะเบียนออกจาก TMP_SELECTION เพื่อให้โอกาสคนที่ลงทะเบียนได้น้อยมีสิทธิ์ได้เรียน
				$AdminEnroll->removeAcceptedLowPrioritySubjectStatus($student_id, $subject_id);

				//แล้วทำการ reconcile เพื่อดึงคนที่เป็น STANDBY ขึ้นมาเพิ่ม (แทนที่ว่างที่พึ่งถูกลบไป)
				$AdminEnroll->enrollReconcile($subject_id);
			}

		}
		
		//ย้าย data จาก TMP_SELECTION ไปไว้ใน StudentCOnfirmedEnrollment เป็นการ confirm ว่า user ลงทะเบียนแล้ว
		$AdminEnroll->moveAllConfrimedAcceptedStudentsFromTmpSelectionToStudentConfirmedEnrollment($classof_id, $semester);

		//จบกระบวนการเลือก update table classof_semester ของเทอมนี้ให้เป็น 2 
		$AdminEnroll->setStatusClassOfSemester(2);


		$db->commitWork();
		$app->response->setBody(json_encode(array("status"=>"success")));

		$db = null;

	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
    }
}

function getSemesterState($classof_id, $semester){
	$sql = "SELECT semester_state FROM CLASSOF_SEMESTER WHERE classof_id = '".$classof_id."' AND semester = '".$semester."'";
	$db = new DBManager();
	$result = $db->getData($sql);
	if ($result){
		while($row = sqlsrv_fetch_array($result)){
			$semester_state = $row['semester_state'];
		}
	}

	return $semester_state;
}

function activateClassofSemester() {
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
		$sql1 = "UPDATE CLASSOF_SEMESTER
				SET semester_state = '1', updatedate = GETDATE()
				WHERE classof_id = '$classof_id' AND semester = '$semester'";
		$sql2 = "UPDATE CLASSOF_SEMESTER
				SET semester_state = '0', updatedate = GETDATE()
				WHERE classof_id = '$classof_id' AND semester != '$semester' AND semester_state = '1'";
	} catch(Exception $e) {
		$app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
		return;
	}
    
	try {
		$db = new DBManager();
		$db->beginSet();
        if($db->setData($sql1))
        {
        	if($db->setData($sql2))
        	{
        		$db->commitWork();	
        	}
        	else
        	{
        		$db->rollbackWork();
        	}
        }
        else
        {
        	//$db->rollbackWork();
        }
		$db = null;
        $app->response->setBody(json_encode(array("status"=>"success")));

	} catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
    }
}

?>