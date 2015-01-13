<?php
require 'Slim/Slim.php';
require 'DBManager_sqlserver.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'mode' => 'development',
	));

$app->setName('CUYExAdmin');

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

$app->group('/api/v1', function() use ($app){
	$app->group('/student', function() use ($app){
		$app->get('/test/:name', 'test');
		$app->get('/:id', 'getStudentById');
		$app->post('/add' , 'addStudent');
	});
});

$app->run();

function test($name)
{
	echo "hello ".$name;
}

function getStudentById($id){
	$sql = "SELECT * FROM STUDENT where student_id = '$id'";
	try {
		$db = new DBManager();
		$result = $db->getData($sql);
		$db = null;
		print_r($result);
	} catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function addStudent(){
	$request = \Slim\Slim::getInstance()->request();
    $student_detail = json_decode($request->getBody());
    echo $student_detail;
    /*
    $sql = "INSERT INTO STUDENT (student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    try {
        $db = new DBManager();
        $params = array();
        $db->setData($sql, $params);
        $db->beginSet();
        $db = null;
        echo json_encode($wine);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    */
}

?>