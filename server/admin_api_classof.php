<?php
/*
    $app->group('/classof', function() use ($app){
        $app->get('/test/:name', 'test');
        $app->get('/:id', 'getClassOfById');
        $app->post('/detail', 'getClassOfByIdPost');
        $app->post('/list', 'listClassOf');
        $app->post('/add', 'addClassOf');
        $app->post('/update', 'updateClassOf');
        $app->post('/delete', 'deleteClassOf');
    });
*/

function getClassOfById($id){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
		$sql = "SELECT classof_id, classof_description FROM CLASSOF where classof_id = '$id'";
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

function getClassOfByIdPost(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $id = json_decode($request->getBody())->classof_id;
		$sql = "SELECT classof_id, classof_description FROM CLASSOF where classof_id = '$id'";
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

function listClassOf(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "SELECT classof_id, classof_description FROM CLASSOF order by classof_description DESC";
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

function listClassOfAndCountStatus(){
	try {
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
		$sql = "SELECT a.classof_id
				      ,classof_description
				      ,COUNT(b.classof_id) AS student_count
				  FROM CLASSOF a
				  LEFT JOIN STUDENT b ON a.classof_id = b.classof_id
				  group by a.classof_id, a.classof_description
				  order by classof_description DESC";
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

function addClassOf(){
	try {
		//check for duplicate first
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_detail = json_decode($request->getBody());
		$sql = "SELECT COUNT(classof_id) FROM CLASSOF where classof_description = '$classof_detail->classof_description'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
		$nrow = 0;
		$result = $db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$nrow = $row[0];
			}
		}	
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }

    if ($nrow > 0) {
    	//data duplicate
    	echo '{"error":{"source":"input","reason":"data duplicate"}}';
		return;
    }

	try {
	    $sql = "INSERT INTO CLASSOF (classof_description) VALUES ('$classof_detail->classof_description')";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
        $db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
        {
        	$db->commitWork();
        	$sql = "SELECT a.classof_id
				      ,classof_description
				      ,COUNT(b.classof_id) AS student_count
				  FROM CLASSOF a
				  LEFT JOIN STUDENT b ON a.classof_id = b.classof_id
				  group by a.classof_id, a.classof_description
				  order by classof_description DESC";
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
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateClassOf(){
	try {
		//check for duplicate first
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_detail = json_decode($request->getBody());	    
		$sql = "SELECT COUNT(classof_id) FROM CLASSOF where classof_description = '$classof_detail->classof_description'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}
    
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$nrow = 0;
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$nrow = $row[0];
			}
		}
		$db = null;
	} catch(PDOException $e) {
        echo '{"error":{"source":"SQL","reason": SQL'. $e->getMessage() .'}}';
    }

    if ($nrow > 0) {
    	//data duplicate
    	echo '{"error":{"source":"input","reason":"data duplicate"}}';
		return;
    }

	try {
	    $sql = "UPDATE CLASSOF SET classof_description = '$classof_detail->classof_description' where classof_id = '$classof_detail->classof_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
        $db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
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
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function deleteClassOf(){
	try {
	    $app = \Slim\Slim::getInstance();
	    $app->response->headers->set('Content-Type', 'application/json');
	    $request = $app->request();
	    $classof_detail = json_decode($request->getBody());
	    $sql = "DELETE FROM CLASSOF where classof_id = '$classof_detail->classof_id'";
	} catch(Exception $e) {
		echo '{"error":{"source":"input","reason":'. $e->getMessage() .'}}';
		return;
	}

    try {
        $db = new DBManager();
        $db->beginSet();
        if($db->setData($sql))
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
