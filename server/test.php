<?php

$str = '100123-2';

$str_exploded = explode('-', $str);
echo count($str_exploded);

echo "<br>";

$subject_id = '1000012-1';
$subject_id_exploded = explode('-', $subject_id);
$subject_id_confirmed_list = '1000013, 1000012-2, 1000029';
$subject_id_withoutSec = $subject_id_exploded[0];
if (strpos($subject_id_confirmed_list,$subject_id_withoutSec) !== false) {
	echo 'true';
} else {
	echo 'not in';
}

?>