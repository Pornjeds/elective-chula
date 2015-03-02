<?php
/*
$app->group('/account', function() use ($app){
        $app->post('/listusers', 'listAccountUsers');
        $app->post('/listadmin', 'listAccountAdmin');
        $app->post('/update', 'updateListOfAdmin');

    });
*/
function listAccountUsers(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "exec listNonAdminMember";
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

function listAccountAdmin(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "SELECT DISTINCT b.user_id, b.role, b.user_type, b.updatedate FROM STUDENT a, USER_ROLE b, ADMIN_MEMBER c
				WHERE a.student_id in (b.user_id) OR CAST(c.admin_id as NCHAR(10)) in (b.user_id)";
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

function updateListOfAdmin(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $admin_arr = json_decode($request->getBody());
	    $updateStatus = false;
	    $db = new DBManager();
		$db->beginSet();
		$adminUserIdList = "";
	    foreach($admin_arr->data as $admin_date){
			$mergeSql = "merge USER_ROLE as target
					using (values ('$admin_date->role', '$admin_date->user_type'))
					    as source (role, user_type)
					    on target.user_id = '$admin_date->user_id'
					when matched then
					    update
					    set role = source.role,
					    	user_type = source.user_type,
					        updatedate = GETDATE()
					when not matched then
					    insert ( user_id, role, user_type, updatedate)
					    values ( '$admin_date->user_id',  source.role, source.user_type, GETDATE());";

			$adminUserIdList .= "'$admin_date->user_id',";

			if($db->setData($mergeSql))
			{
				$updateStatus = true;
			}
			else
			{
				$updateStatus = false;
				break;
			}
		}

		$adminUserIdList = substr($adminUserIdList,0,-1); //remove the last ,
		if ($updateStatus) {
			$deleteSql = "DELETE FROM USER_ROLE WHERE user_id not in ($adminUserIdList)";
			if($db->setData($deleteSql))
			{
				$updateStatus = true;
			}
			else
			{
				$updateStatus = false;
				break;
			}
		}
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	if ($updateStatus)
	{
		$db->commitWork();
		//response with list of the new admin user
		$sql = "SELECT DISTINCT b.user_id, b.role, b.user_type, b.updatedate FROM STUDENT a, USER_ROLE b, ADMIN_MEMBER c
				WHERE a.student_id in (b.user_id) OR CAST(c.admin_id as NCHAR(10)) in (b.user_id)";
		$result = $db->getData($sql);
		$response_arr = array();
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				array_push($response_arr, $row);
			}
		}
        $app->response->setBody(json_encode($response_arr));
    }
    else
	{
		$db->rollbackWork();
        $app->response->setBody(json_encode(array("status"=>"fail")));
        $app->response->write(json_encode($db->errmsg()));    	
	}

	$db = null;
}
?>