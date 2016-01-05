<?php
	session_start();
	unset($_SESSION['password']);
	unset($_SESSION['login']);
	header('location: index.php');
?>