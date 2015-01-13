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
    $sql = "INSERT INTO STUDENT (student_id, classof_id, name, lastname, email, password, profilepic, GPA, addeddate, student_status) VALUES ('$student_detail->student_id', '$student_detail->classof_id', '$student_detail->name', '$student_detail->lastname', '$student_detail->email', '$student_detail->password', '$student_detail->profilepic', '$student_detail->GPA', GETDATE(), '$student_detail->student_status')";
    try {
        $db = new DBManager();
        $db->setData($sql);
        if($db->beginSet())
        {
        	$db->commitWork();	
        }
        else
		{
			$db->rollbackWork();			
		}
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

?>