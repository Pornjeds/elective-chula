<?php
session_start();

//$_SESSION['gie'] = "hello";
if (isset($_SESSION['loginStatus']) && $_SESSION['loginStatus'] == 1){
    echo 'gie';
}

$x = "Hello";
$x_arr = explode(",", $x);
foreach($x_arr as $xx){
	echo $xx;
}

$test = array("a"=>"gie", "b"=>"test");
$k = $test;

$m = array();
foreach($m as $mm){
	echo "mm is ".$mm['test'];
	break;
}
echo "ends mm";
$x = '' && false;
echo "x is $x";

?>