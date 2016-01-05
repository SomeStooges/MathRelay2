<?php
	session_start();
	if(!isset($_SESSION['teamID']) || !isset($_SESSION['adminLogin'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>
<html>
	<head>
		<title> Scoreboard </title>
		<!--- McNeil Blue is 210082; McNeil Green is 0A6B0A; Light Green: 00E100 Light Blue: 0749C1-->
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			
			function updateData(){
				$.post('/MathRelay/server/Admin.php',"action='getTeamSummary'");
			}
			
			function getDisplayValue(){
				$.post('/MathRelay/server/Admin.php',"action=getDisplayNumber",function(data){
					var value = JSON.parse(data);

					$("#displayNumberResponse").text("Currently set to display "+value+" teams.");
				});
			}
			function getDisplayOptions(){
				$.post('/MathRelay/server/Admin.php',"action=getDisplayOptions",function(data){
					var value = JSON.parse(data);
										console.log("Recieved Value: "+value);
					//console.log(value);
					var options = value.split(";");
					//console.log(options);
					options[0]=="1" ? $("#checkRank").attr("checked",true):$("#checkRank").attr("checked",false);
					options[1]=="1" ? $("#checkTeamID").attr("checked",true):$("#checkTeamID").attr("checked",false);
					options[2]=="1" ? $("#checkNickname").attr("checked",true):$("#checkNickname").attr("checked",false);
					options[3]=="1" ? $("#checkPoints3").attr("checked",true):$("#checkPoints3").attr("checked",false);
					options[4]=="1" ? $("#checkPoints2").attr("checked",true):$("#checkPoints2").attr("checked",false);
					options[5]=="1" ? $("#checkPoints1").attr("checked",true):$("#checkPoints1").attr("checked",false);
				});
			}
			
			$(document).ready(function(){
				window.setInterval(updateData,1000);
				getDisplayValue();
				getDisplayOptions();
				$("#displayNumberSubmit").click(function(){
					obj = new Object;
					obj.value = $("#displayNumber").val();
					obj.action = "setDisplayNumber";
					$.post('/MathRelay/server/Admin.php',obj,function(){
						getDisplayValue();
					});
				});
				
				$("#checkboxSubmit").click(function(){
					obj = new Object;
					obj.value = $("#checkRank").prop("checked") ? "1":"0";
					obj.value += ";" + ($("#checkTeamID").prop("checked")  ? "1":"0");
					obj.value += ";" + ($("#checkNickname").prop("checked")  ? "1":"0");
					obj.value += ";" + ($("#checkPoints3").prop("checked")  ? "1":"0");
					obj.value += ";" + ($("#checkPoints2").prop("checked")  ? "1":"0");
					obj.value += ";" + ($("#checkPoints1").prop("checked")  ? "1":"0");
					
					obj.action = "setDisplayOptions";
					console.log(obj.value);
					$.post('/MathRelay/server/Admin.php',obj,function(){
						//getDisplayOptions();
					});
				})
			});
		</script>
		
		<style>
			
			#hyperlink{
				position:relative;
				top:10px;
				
			
				background-color: #210082;
				color: white;
				padding: 10px;
				
				text-align: center;
			}
			
			
			#numberDisplayedSelection{
				position:relative;
				top:20px;
				
			
				background-color: #341A82;
				color: white;
				padding: 10px;
				
				text-align: center;
			}
			#scoreboardPrintout{
				position: relative;
				top: 30px;
				
				background-color: red;
				padding: 10px;
			}
			.displayNumPanel{
				padding: 5px;
				color: white;
			}
			#displayNumberSubmit{
				padding: 5px;
			}
			
		
		</style>
	</head>
	
	<body>
		<div id="page-wrap">
			<div id="imageDiv">
				<img src="Math_Relay_Banner.jpg" alt="McNeil Mu Alpha Theta Math Relay">
			</div>	
		<div id="pageContent">
			<div id="header">
				<table>
					<tr>
						<th><input type="button" onclick="location.href='/MathRelay/ControlPanel.php';"class="headerPanel" value="Control Panel"></th>
						<th><input type="button" onclick="location.href='/MathRelay/InformationStream.php';" class="headerPanel" value="Information Stream"></th>
						<th><input type="button" onclick="location.href='/MathRelay/AnswerKey.php';" class="headerPanel" value="Answer Key"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Scoreboard.php';" class="headerPanel" value="Scoreboard" style="background-color:#99ff99"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Options.php';" class="headerPanel" value="Options"></th>
					</tr>
				</table>
			</div>
			
			<a href='/MathRelay/ScoreboardWindow.php' target="_blank"><div id="hyperlink">
				<h1> SEPERATE PAGE </h1>
				<i> Click this box to go to the scoreboard. </i>
			</div></a>
			
			<div id="numberDisplayedSelection">
				<h1> Scoreboard Options </h1>
				<i> Select options for the display scoreboard.</i>
				<table>
					<tr>
						<td class="displayNumPanel"> Set number of Teams to display: </td>
						<td class="displayNumPanel"><input id="displayNumber" value=""></td>
						<td class="displayNumPanel"><input type="button" id="displayNumberSubmit" value="Submit"></td>
						<td class="displayNumPanel"><span id="displayNumberResponse">Retrieving current value...</span></td>
					</tr>
				</table>
				<br><br>
				Select which data to display on the scoreboard:
				<div id="checkboxContainer" style="width: 400px;text-align: left">
				<br>
					<input type="checkbox" id="checkRank" value="checkRank"> Current Rank<br>
					<input type="checkbox" id="checkTeamID" value="checkTeamID"> Team ID<br>
					<input type="checkbox" id="checkNickname" value="checkNickname"> Nicknames (Recommended)<br>
					<input type="checkbox" id="checkPoints3" value="checkPoints3"> Level 3 Points<br>
					<input type="checkbox" id="checkPoints2" value="checkPoints2"> Level 2 Points<br>
					<input type="checkbox" id="checkPoints1" value="checkPoints1"> Level 1 Points<br>
				</div>
				<br><input id="checkboxSubmit" style="width: 150px; height: 30px;" type="button" value="Update Selection">
			</div>
			
			
			
			<div> 
		</div>
		</div>
	</body>
</html>