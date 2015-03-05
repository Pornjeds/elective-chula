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
		$sql2 = "SELECT * FROM CLASSOF_SEMESTER WHERE classof_id = '$classof_id' AND semester = '$semester'";
		
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
        
function submitSubjectRegistration(){
/*
input
{
"subjectsdata":
[
	{
		"subject_id": 0,
		"dayofweek": 1,
		"timeofday": 2,
		"credit": 3,
		"instructor": "Dr. Worawat XYZ",
		"minstudent": 10,
		"maxstudent": 40,
		"isRequired": 1
	},
	{
		"subject_id": 3,
		"dayofweek": 4,
		"timeofday": 2,
		"credit": 3,
		"instructor": "Dr. Worawat XYZ",
		"minstudent": 10,
		"maxstudent": 40,
		"isRequired": 1
	}
],
	"classof_id": 1,
	"semester": 4,
	"mincredit": 3,
	"maxcredit": 9,
	"pickermethod": 1
}
*/

	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $subjectarr = json_decode($request->getBody())->subjectsdata;
	    $classof_id = json_decode($request->getBody())->classof_id;
	    $semester = json_decode($request->getBody())->semester;
	    $semester_mincredit = json_decode($request->getBody())->mincredit;
	    $semester_maxcredit = json_decode($request->getBody())->maxcredit;
	    $semester_state = json_decode($request->getBody())->semester_state;
	    $semester_pickermethod = json_decode($request->getBody())->pickermethod;

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

		//update semester_state of the other semesters of this classof_id to be 0
		$sqlSemesterState = "UPDATE CLASSOF_SEMESTER set semester_state = 0 where classof_id = '$classof_id' AND semester <> '$semester'";
		
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

		if(!$db->setData($sqlSemesterState))
		{
			//echo $sqlRemove;
			$submitStatus = false;
			break;
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