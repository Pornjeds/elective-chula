<?php

function getSubjectInfoById($id){
	try {
	    $app = \Slim\Slim::getInstance();
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
		$app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}
        
function getSubjectInfoByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
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
		$app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($response_arr));
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }
}
        
function listSubjectByClassOfAndSemester(){

}
        
function submitSubjectRegistration(){

}
        
function updateSubjectRegistration(){

}

?>