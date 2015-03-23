<?php
session_start();
require_once 'Slim/Slim.php';
require_once 'DBManager_sqlserver.php';
require_once 'user_api_account.php';
require_once 'user_api_enrollment.php';
require 'Slim/Middleware.php';
require 'Slim/Middleware/HttpBasicUserAuth.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'mode' => 'development',
	));

$app->add(new \HttpBasicUserAuth());

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
        $app->post('/list', 'getUserDashboardInfo');
    });
    
    $app->group('/enrollment', function() use ($app){
        $app->post('/list', 'getSubjectList');
        $app->post('/submit', 'submitEnrollment');
    });
    
});

$app->run();



?>