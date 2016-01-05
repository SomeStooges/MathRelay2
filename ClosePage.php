<?php
	session_start();
	if(!isset($_SESSION['password'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>

<html>
	<head>
		<title> Thanks for Participating! </title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>		
			<script>
				function getCleanupParagraph(){
					$.post('/MathRelay/server/Runner.php',"action=getCleanupParagraph",function(data){
						var response = JSON.parse(data);
						console.log(data);
						$("#content").text(response);
					});
				}
				
				function getFinalRank(){
					$.post('/MathRelay/server/Runner.php',"action=getFinalRank",function(data){
						var response = JSON.parse(data);
						console.log(data);
						$("#finalRank").html("<h1>"+response+"</h1>");
					})
				}
				
				$(document).ready(function(){
					getCleanupParagraph();
					getFinalRank();
					$("#logoffButton").click(function(){
						$.post('/MathRelay/server/Runner.php',"action=logout");
						window.location.href="index.php";
					});
				});
			</script>
		<style>
			
			#pageContent{
				position: relative;
				top: 10px;
				width: 800px;
				
				text-align: center;
				margin: 0 auto;
				background-color: #0A6B0A;
				padding: 10px 10px 30px;
				color: white;
				
			}
			#LogoffButton{
				width: 80px;
				height: 50px;
				font-family:inherit;
			}
		</style>
	</head>

	<body>
		<div id="page-wrap">
		<div id="imageDiv">
			<img src="Math_Relay_Banner.jpg" alt="McNeil Mu Alpha Theta Math Relay"> 
		</div>	
			<div id="pageContent">
				<h1>THANKS FOR PARTICIPATING!</h1>
				<span id="finalRank"></span>
				<span id="content"> Ooops: there was some error, and the proper paragraph failed to load. Try refreshing this window.</span>
				<br><br><input type="Button" name="logoffButton" id="logoffButton" value="Logout">
			</div>
		</div>
	</body>
</html>