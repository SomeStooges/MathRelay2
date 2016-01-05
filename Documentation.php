<?php
	session_start();
	if(!isset($_SESSION['teamID']) || !isset($_SESSION['adminLogin'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>
<html>
	<head>
		<title> Options </title>
		<style>
			*{
				text-align: center;
			}
			.type1{
				width: 90px;
				text-align: left;
			}
			.type2{
				width: 110px;
				text-align: left;
			}
			.type3{
				width: 200px;
				text-align: left;
			}
		</style>
	</head>
	
	<body>
		<h1>Documentation</h1>
		<br>This program was created by Ethan Freeburg.
		<br><br>It is currently approximately 3342 lines long.

		<h1>Changelog</h1>
		<table style="margin: 0 auto;">
			<tr>
				<td class="type1">8/15/2015</td>
				<td class="type2">Version 1.0.0</td>
				<td class="type3"> Release! </td>
			</tr>
		</table>
	</body>
</html>