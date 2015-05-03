<?php

function getSubjectInfoById($id){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
		$sql = "SELECT subject_id, name, description, defaultpoint, addeddate, updatedate FROM SUBJECT where subject_id = '$id'";
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
        
function getSubjectInfoByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $id = json_decode($request->getBody())->subject_id;
		$sql = "SELECT subject_id, name, description, defaultpoint, addeddate, updatedate FROM SUBJECT where subject_id = '$id'";
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
        
function listSubjectByClassOfAndSemester(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
		$sql = "exec listSubjectByClassOfAndSemester @classof_id = '$classof_id', @semester = '$semester'";
		$sql2 = "SELECT * FROM CLASSOF_SEMESTER a
				INNER JOIN ADMIN_ACTIVATESCHEDULE b ON a.classof_id = b.classof_id AND a.semester = b.semester
				WHERE a.classof_id = '$classof_id' AND a.semester = '$semester'";
		
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$response_arr1 = array();
		$response_arr2 = array();
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($response_arr1, $row);
			}
		}
		$result2 = $db->getData($sql2);
		if ($result2){
			while($row = sqlsrv_fetch_array($result2)){
				array_push($response_arr2, $row);
			}
		}

		$tmp_arr1 = array('subjectsdata' => $response_arr1);
		$tmp_arr2 = array('classofsemester' => $response_arr2);

		array_push($response_arr, $tmp_arr1);
		array_push($response_arr, $tmp_arr2);

		$db = null;
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}

function listAllSubjects(){
	try {
		$app = \Slim\Slim::getInstance();
		//$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "SELECT * FROM SUBJECT order by subject_id";
		
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
        
function submitSubjectRegistration(){
/*
input

//NOT ACTIVE NOW -- schedule
{
  "subjectsdata": [
    {
      "subject_id": "2602601",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "a",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2602648",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "b",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2602650",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "c",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2604644",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "d",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605620",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "e",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605624",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "f",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605671",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "g",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    }
  ],
  "classof_id": "2",
  "semester": "4",
  "mincredit": "9",
  "maxcredit": "9",
  "semester_state": "1",
  "pickermethod": "1",
  "isActiveNow": "0",
  "startDate": "2015/04/24 22:31",
  "endDate": "2015/04/28 22:31"
}

//ACTIVE NOW (or already active)
{
  "subjectsdata": [
    {
      "subject_id": "2602601",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "a",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2602648",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "b",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2602650",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "c",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2604644",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "d",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605620",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "e",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605624",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "f",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    },
    {
      "subject_id": "2605671",
      "dayofweek": null,
      "timeofday": null,
      "credit": "3",
      "instructor": "g",
      "minstudent": "30",
      "maxstudent": "50",
      "isRequired": 0
    }
  ],
  "classof_id": "2",
  "semester": "4",
  "mincredit": "9",
  "maxcredit": "9",
  "semester_state": "1",
  "pickermethod": "1",
  "isActiveNow": "1",
  "startDate": "",
  "endDate": ""
}
*/

/*
 * semester_state
 * 0 -- created but not open for enrollment AND not scheduled
 * 1 -- created and open for enrollment (either by schedule or manual -- will be known by var status)
 * 2 -- enrollment is done. Selection already took place
 *
 * isActiveNow
 * 0 -- no meaning
 * 1 -- schedule is created
 * 2 -- schedule was run
*/

	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $jsonInput = json_decode($request->getBody());
	    $subjectarr = $jsonInput->subjectsdata;
	    $classof_id = $jsonInput->classof_id;
	    $semester = $jsonInput->semester;
	    $semester_mincredit = $jsonInput->mincredit;
	    $semester_maxcredit = $jsonInput->maxcredit;
	    $semester_pickermethod = $jsonInput->pickermethod;
	    $isActiveNow = $jsonInput->isActiveNow;
	    $activeStartDate = $jsonInput->startDate;
	    $activeEndDate = $jsonInput->endDate;
	    $semester_state = $jsonInput->semester_state;

	    $startDate = "";
	    $endDate = "";
	    $startTime = "";
	    $endTIme = "";

	    if (strlen($activeStartDate) > 0) {
	    	//2015/04/22 00:36
	    	$tmp = explode(" ", $activeStartDate);
	    	$tmp_startDate = explode("/", $tmp[0]);
	    	$tmp_startTime = explode(":", $tmp[1]);
	    	$startDate = $tmp_startDate[0].$tmp_startDate[1].$tmp_startDate[2];
	    	$startTime = $tmp_startTime[0].$tmp_startTime[1]."00";
	    } 

	    if (strlen($activeEndDate) > 0) {
	    	//2015/04/22 00:36
	    	$tmp = explode(" ", $activeStartDate);
	    	$tmp_endDate = explode("/", $tmp[0]);
	    	$tmp_endTime = explode(":", $tmp[1]);
	    	$endDate = $tmp_endDate[0].$tmp_endDate[1].$tmp_endDate[2];
	    	$endTime = $tmp_endTime[0].$tmp_endTime[1]."00";
	    }

		if ($isActiveNow == "2") {			
			$sqlUpdateActivateJob = "UPDATE ADMIN_ACTIVATESCHEDULE set status = 2 where classof_id = $classof_id AND semester = $semester";
			//update semester_state of the other semesters of this classof_id to be 0
			if ($semester_state == "1") {
				$sqlSemesterState = "UPDATE CLASSOF_SEMESTER set semester_state = 0 where classof_id = '$classof_id' AND semester <> '$semester'";
				$sqlClearTmpSelection = "DELETE FROM TMP_SELECTION where classof_id = '$classof_id' AND semester = '$semester'";
				$sqlSetLogicalPriority = "UPDATE STUDENT_ENROLLMENT set logical_priority = priority where classof_id = '$classof_id' AND semester = '$semester'";
				$sqlClearStudentConfirmedEnrollment = "DELETE FROM STUDENT_CONFIRMED_ENROLLMENT where classof_id = '$classof_id' AND semester = '$semester'";
			}
		} else {
			$semester_state = "3";
			$sqlActivateJob = "merge ADMIN_ACTIVATESCHEDULE as target
				using (values ('activate classof semester', '$classof_id', '$semester', '$activeStartDate', '$activeEndDate', 0, GETDATE()))
				    as source (jobname, classof_id, semester, startdate, finishdate, status, logdate)
				    on target.classof_id = '$classof_id' AND target.semester = '$semester'
				when matched then
				    update
				    set startdate = source.startdate,
				        finishdate = source.finishdate,
				        logdate = source.logdate
				when not matched then
				    insert ( jobname, classof_id, semester, startdate, finishdate, status, logdate)
				    values ( source.jobname, source.classof_id, source.semester, source.startdate, source.finishdate, source.status, source.logdate);";
		}

		//prepare sql for insert subject data
	    $sqlarr = array();
	    $i = 0;
	    foreach ($subjectarr as $subject) {
	    	$subject_id = $subject->subject_id;
	    	$dayofweek = $subject->dayofweek;
	    	$timeofday = $subject->timeofday;
	    	$credit = $subject->credit;
	    	$instructor = $subject->instructor;
	    	$minstudent = $subject->minstudent;
	    	$maxstudent = $subject->maxstudent;
	    	$isRequired = $subject->isRequired;

	    	$sqlarr[$i] = "INSERT INTO 
	    				SUBJECT_CLASSOF (subject_id, classof_id, semester, minstudent, maxstudent, credit, dayofweek, timeofday, instructor, isRequired, addeddate, updatedate)
	    				VALUES ('$subject_id', '$classof_id', '$semester', '$minstudent', '$maxstudent', '$credit', '$dayofweek', '$timeofday', '$instructor', '$isRequired', GETDATE(), GETDATE())";
	    	$i++;
	    }

	    //prepare classof_semester sql
	    $sqlClassofSemester = "merge CLASSOF_SEMESTER as target
				using (values ('$classof_id', '$semester', '$semester_mincredit', '$semester_maxcredit', '$semester_state', '$semester_pickermethod'))
				    as source (classof_id, semester, semester_mincredit, semester_maxcredit, semester_state, semester_pickermethod)
				    on target.classof_id = '$classof_id' AND target.semester = '$semester'
				when matched then
				    update
				    set mincredit = source.semester_mincredit,
				        maxcredit = source.semester_maxcredit,
				        semester_state = source.semester_state,
				        pickmethod_id = source.semester_pickermethod,
				        updatedate = GETDATE()
				when not matched then
				    insert ( classof_id, semester, mincredit, maxcredit, semester_state, pickmethod_id, addeddate)
				    values ( source.classof_id, source.semester, source.semester_mincredit, source.semester_maxcredit, source.semester_state, source.semester_pickermethod, GETDATE());";

		//sql to remove the current SUBJECT_CLASSOF of the specified classof_id and semester
		$sqlRemove = "DELETE FROM SUBJECT_CLASSOF where classof_id = '$classof_id' AND semester = '$semester'";
		
		
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

	try {
		$submitStatus = true;
		$db = new DBManager();
		$db->beginSet();
		if(!$db->setData($sqlRemove))
		{
			//echo $sqlRemove;
			$submitStatus = false;
			break;
		}
		foreach ($sqlarr as $sql){
			if(!$db->setData($sql))
        	{
        		//echo "$sql";
        		$submitStatus = false;
        		break;
        	}
		}

		if ($isActiveNow == "2") {
			//please activate now
			if ($semester_state == "1") {

				if(!$db->setData($sqlUpdateActivateJob))
				{
					$submitStatus = false;
					break;
				}

				if(!$db->setData($sqlSemesterState))
				{
					//echo $sqlRemove;
					$submitStatus = false;
					break;
				}

				if(!$db->setData($sqlClearTmpSelection))
				{
					//echo $sqlRemove;
					$submitStatus = false;
					break;
				}

				if(!$db->setData($sqlSetLogicalPriority))
				{
					//echo $sqlRemove;
					$submitStatus = false;
					break;
				}

				if(!$db->setData($sqlClearStudentConfirmedEnrollment))
				{
					//echo $sqlRemove;
					$submitStatus = false;
					break;
				}
			}

			$Activator = new AdminActivateSchedule($db, $classof_id, $semester);
			$Activator->removeActivateSchedule();

		} else {
			//Will be activated later, so set up a schedule job
			if(!$db->setData($sqlActivateJob))
			{
				$submitStatus = false;
				break;
			}

			$Activator = new AdminActivateSchedule($db, $classof_id, $semester);
			$Activator->addActivateSchedule($startDate, $startTime, $endDate, $endTime);
		}


		if(!$db->setData($sqlClassofSemester))
    	{
    		//echo $sqlClassofSemester;
    		$submitStatus = false;
    	}

        if($submitStatus)
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