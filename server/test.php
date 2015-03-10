<?php

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
}
echo "ends mm";

?>