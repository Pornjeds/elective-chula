<?php
$jsonData = '{"data":[{"student_id":47010676,"classofid":"1","name":"name1","lastname":"lastname1","email":"email1@gmail.com","password":"Welcome1","profilepic":"default.jpg"},{"student_id":47010676,"classofid":"1","name":"name2","lastname":"lastname2","email":"email2@gmail.com","password":"Welcome1","profilepic":"default.jpg"}]}';
//$jsonData = "{'gie':'xxx'}";
//$jsonData = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

$test = json_decode($jsonData);
echo $test->data[0]->student_id;

?>