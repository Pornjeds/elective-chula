<?php

function importStudents(){

	//sameple data 
	//$jsonData = '{"data":[{"student_id":47010678,"classofid":"1","name":"name1","lastname":"lastname1","email":"email1@gmail.com","password":"Welcome1","profilepic":"default.jpg","GPA":"3.42","status":"1"},{"student_id":47010677,"classofid":"1","name":"name2","lastname":"lastname2","email":"email2@gmail.com","password":"Welcome1","profilepic":"default.jpg","GPA":"2.59","status":"1"}]}';

	$app = \Slim\Slim::getInstance();
	$student_arr = json_decode($request->getBody());
	//$student_arr = json_decode($jsonData);
	$importstatus = true;
	$db = new DBManager();
	$db->beginSet();
	foreach($student_arr as $students){
		foreach($students as $student){
			$sql = "merge STUDENT as target
				using (values ('$student->name', '$student->lastname', '$student->classofid', '$student->email', '$student->password', '$student->profilepic', '$student->GPA', '$student->status'))
				    as source (name, lastname, classofid, email, password, profilepic, GPA, student_status)
				    on target.student_id = '$student->student_id'
				when matched then
				    update
				    set name = source.name,
				        lastname = source.lastname,
				        classof_id = source.classofid,
				        email = source.email,
				        password = source.password,
				        profilepic = source.profilepic,
				        GPA = source.GPA,
				        student_status = source.student_status,
				        updatedate = GETDATE()
				when not matched then
				    insert ( student_id, name, lastname, classof_id, email, password, profilepic, GPA, addeddate, student_status )
				    values ( '$student->student_id',  source.name, source.lastname, source.classofid, source.email, source.password, source.profilepic, source.GPA, GETDATE(), source.student_status );";

			if($db->setData($sql))
		    {
		        $importstatus = true;
		    }
		    else
		    {
		    	$importstatus = false;
		    	break;
		    }
		}
	}
	if ($importstatus)
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
}

function importSubjects(){
	
}



?>