<?php
require_once 'Slim/Slim.php';
require_once 'DBManager_sqlserver.php';
require_once 'admin_api_account.php';
require_once 'admin_api_student.php';
require_once 'admin_api_import.php';
require_once 'admin_api_subject.php';
require_once 'admin_api_classof.php';
require_once 'admin_api_enrollment.php';

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


//Rounting
$app->group('/api/v1', function() use ($app){
    $app->group('/account', function() use ($app){
        $app->post('/listusers', 'listAccountUsers');
        $app->post('/listadmin', 'listAccountAdmin');
        $app->post('/update', 'updateListOfAdmin');

    });

	$app->group('/student', function() use ($app){
		$app->get('/test/:name', 'test');
		$app->get('/:id', 'getStudentById');
        $app->post('/detail', 'getStudentByIdPost');
        $app->post('/classof', 'getStudentByClassOf');
		$app->post('/add' , 'addStudent');
        $app->post('/update', 'updateStudent');
        $app->post('/delete' , 'deleteStudent');
	});

    $app->group('/import', function() use ($app){
        $app->get('/test/:name', 'test');
        $app->post('/students', 'importStudents');
        $app->post('/subjects', 'importSubjects');
    });

    $app->group('/subject', function() use ($app){
        $app->get('/test/:name', 'test');
        $app->get('/:id', 'getSubjectInfoById');
        $app->post('/detail', 'getSubjectInfoByIdPost');
        $app->post('/list', 'listSubjectByClassOfAndSemester');
        $app->post('/submit', 'submitSubjectRegistration');
    });

    $app->group('/classof', function() use ($app){
        $app->get('/test/:name', 'test');
        $app->get('/', 'listClassOf');
        $app->get('/:id', 'getClassOfById');
        $app->post('/detail', 'getClassOfByIdPost');
        $app->post('/list', 'listClassOf');
        $app->post('/listandcountstudent', 'listClassOfAndCountStatus');
        $app->post('/add', 'addClassOf');
        $app->post('/update', 'updateClassOf');
        $app->post('/delete', 'deleteClassOf');
    });

    $app->group('/enrollmentadmin', function() use ($app){
        $app->post('/detail', 'getSubjectEnrollmentInfoByIdPost');
        $app->post('/list', 'listEnrollmentByClassOfAndSemester');
        $app->post('/liststudent', 'listEnrollmentResultBySubject');
        $app->post('/listpickmethod', 'listPickMethod');
        $app->post('/run', 'performEnrollment');
    });
});

$app->run();

function test($name)
{
	echo "hello ".$name;
}




?>