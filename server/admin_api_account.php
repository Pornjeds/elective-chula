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
		$sql = "exec listAccountMember";
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
		$adminRole = "Admin"; //hardcode, add more logic if you want roles more than Admin 
	    foreach($admin_arr->data as $admin_data){
			$mergeSql = "merge USER_ROLE as target
					using (values ('$adminRole'))
					    as source (role)
					    on target.user_id = '$admin_data->user_id'
					when matched then
					    update
					    set role = source.role,
					        updatedate = GETDATE()
					when not matched then
					    insert ( user_id, role, updatedate)
					    values ( '$admin_data->user_id',  source.role, GETDATE());";

			$adminUserIdList .= "'$admin_data->user_id',";

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
?>