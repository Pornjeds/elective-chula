<?php

session_start();

session_destroy();
if(isset($_SESSION['loginStatus'])){
	session_unset($_SESSION['loginStatus']);
	session_unset($_SESSION['loginUsername']);
	session_unset($_SESSION['loginType']);
}

echo "Successfully logout";

header('Location: ../index.html');

?>