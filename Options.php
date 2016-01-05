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
		<link rel="stylesheet" type="text/css" href="style.css">
		<!-- McBlue: #210082
			 McGreen: #0A6B0A
			 Light Blue 1: #341A82
			 Light Blue 2: #483482 -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			function updateData(){
				$.post('/MathRelay/server/Admin.php',"action='getTeamSummary'");
			}
			
			function changeNumberOfTeamsText(){
				obj = new Object;
				obj.action="getNumberOfTeams";
				$.post('/MathRelay/server/Admin.php',obj,function(data){
					var number=JSON.parse(data)
					console.log(data);
					$("#numberOfTeamsTD").text("Currently set to create "+number+" teams.")
				});
			}
			
			function getCleanupParagraph(){
				$.post('/MathRelay/server/Admin.php',"action=getCleanupParagraph",function(data){
					var response = JSON.parse(data);
					console.log(data);
					$("#cleanupParagraph").text(response);
				});
			}
			
			function changeRankingStyle(target){
				$(".rankingStylePanel").css("background-color","");
				$(target).css("background-color","lightblue");
			}
			
			$(document).ready(function(){
				window.setInterval(updateData,1000);
				changeNumberOfTeamsText();
				$.post('/MathRelay/server/Admin.php',"action=getRankingStyle",function(data){
					response = JSON.parse(data);
					var target = $("[id|='"+response+"']");
					console.log("RAN!" + response);
					changeRankingStyle(target);
				});
				
				getCleanupParagraph();
				$(".rankingStylePanel").click(function(){
					$(".rankingStylePanel").css("background-color","");
					$(this).css("background-color","lightblue");
				});
				
				$("#numberOfTeamsSubmit").click(function(){
					obj = new Object;
					obj.numberOfTeams = $("#numberOfTeamsInput").val();
					obj.action = "setNumberOfTeams";
					//console.log("got this far");
					$.post('/MathRelay/server/Admin.php',obj,changeNumberOfTeamsText());					
				});
				
				$(".rankingStylePanel").click(function(){
					var target = $(this);
					changeRankingStyle(target);
					
					obj = new Object;
					obj.value = $(this).attr("id");
					obj.action = "setRankingStyle";
					
					console.log(obj.value);
					
					$.post('/MathRelay/server/Admin.php',obj)
				});
				
				$("#cleanupParagraph").keypress(function(e){
					var key = e.which;
					if(key==13){
						$("#cleanupParagraph").blur();
					}
				});
				
				$("#cleanupParagraph").blur(function(){
					if($(this).val()!=""){
						$(this).css("background-color","lightblue");
						obj.action = "setCleanupParagraph";
						obj.value = $("#cleanupParagraph").val();
						$.post('/MathRelay/server/Admin.php',obj,getCleanupParagraph());
					}
				});
				
				$("#adminPasswordSubmit").click(function(){
					obj = new Object;
					obj.oldPassword = $("#oldPassword").val();
					obj.newPassword1 = $("#newPassword1").val();
					obj.newPassword2 = $("#newPassword2").val();
					obj.action = "changeAdminPassword";
					
					console.log("obj.oldPassword: "+obj.oldPassword);
					console.log("obj.newPassword1: "+obj.newPassword1);
					console.log("obj.newPassword2: "+obj.newPassword2);
					
					$.post('/MathRelay/server/Admin.php',obj,function(data){
						console.log("Recieved data: " +data);
						var response = JSON.parse(data);
						$("#adminPasswordFeedback").html(response);
					})
				})
				
			});
			
		</script>
		
		<style>
			#rankingDisplay{
				position: relative;
				top: 10px;
				
				padding: 10px;
				background-color:  #210082;
				text-align: center;
			}
			
			#cleanupParagraphDisplay{
				position: relative;
				top: 20px;
				
				padding: 10px;
				background-color: #341A82;
				text-align: center;
			}
			#teamOptionsDiv{
				position: relative;
				top: 30px;
				
				padding: 10px;
				background-color: #483482;
				text-align: center;
			}
			.teamOptionsPanel{
				padding: 5px;
			}
			.rankingStylePanel{
				margin: 5px;
				width: 180px;
				height: 200px;
				
			}
			#administratorOptionsDiv{
				position: relative;
				top: 40px;				
				
				background-color: #514182;				 
				padding: 10px;
				text-align: center;
			}
			.adminOptionsPanel{
				padding: 5px;
			}
			#documentation{
				position: relative;
				top: 50px;
				
				background-color: 5b4e82;
				padding: 10px;
				text-align: center;
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
						<th><input type="button" onclick="location.href='/MathRelay/Scoreboard.php';" class="headerPanel" value="Scoreboard"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Options.php';" class="headerPanel" value="Options" style="background-color:#99ff99"></th>
					</tr>
				</table>
			</div>
			
			<div id="rankingDisplay">
				<h1> Ranking System </h1>
				<i>Select the way in which the program determines the rank of teams.</i>
				<table style="margin: 0 auto">
					<tr>
						<td><button type="button" id="rankingStyle1" name="rankingStyle1" class="rankingStylePanel"> 
						<b>Option 1 </b>
						<br><br>Ranks teams by level 3 points. Ties are broken by submission time.
						</button></td>
						
						<td><button type="button" id="rankingStyle2" name="rankingStyle2" class="rankingStylePanel">  
						<b>Option 2</b>
						<br><br>Ranks teams by level 3 points. Ties are broken by the teams' ranks at free time. Further ties are broken by submission time.
						</button></td>
						
						<td style='vertical-align: top;'><button type="button" id="rankingStyle3" name="rankingStyle3" class="rankingStylePanel">  
						<b>Option 3</b>
						<br><br>Ranks teams by level 3 points. Ties are then broken by level 2 points, then level 1 points, then time of submission.
						</button></td>
					</tr>
				</table>	
			</div>
			
			<div id="cleanupParagraphDisplay">
				<h1>Clean-Up Paragraph</h1>
				<i>Enter the text which will be displayed after the program has stopped.</i>
				<br><textarea name="clean-upParagraph" id="cleanupParagraph" rows="4" cols="100"></textarea>
			</div>
			
			<div id="teamOptionsDiv">
				<h1>Miscellaneous Options</h1>
				<table>
					<tr>
						<td class="teamOptionsPanel" style="color: white;"> Number of Teams: </td>
						<td class="teamOptionsPanel"> <input id="numberOfTeamsInput" value=""></td>
						<td class="teamOptionsPanel"> <input id="numberOfTeamsSubmit" type="button" value="Set" style="padding: 3px;"></td>
						<td class="teamOptionsPanel" style="color: white;" id="numberOfTeamsTD"> Reload to See how many teams are currently set </td>
					</tr>
				</table>
			</div>
			<div id="administratorOptionsDiv">
				<h1> Administrator Password </h1>
				<i>Options to change the administrator password</i>
				<table class="adminPasswordTable">
					<tr>
						<td class="adminOptionsPanel" style="color: white; text-align: right"> Current Password: </td>
						<td class="adminOptionsPanel"><input type="password" id="oldPassword" value=""></td>
					</tr>
					<tr>
						<td class="adminOptionsPanel" style="color: white; text-align: right"> New Password: </td>
						<td class="adminOptionsPanel"><input type="password" id="newPassword1" value=""></td>
					</tr>
					<tr>
						<td class="adminOptionsPanel" style="color: white; text-align: right"> Repeat New Password: </td>
						<td class="adminOptionsPanel"><input type="password" id="newPassword2" value=""></td>
					</tr>
				</table>
				<input type="button" id="adminPasswordSubmit" value="Submit" style="width: 100px; height: 30px;">
				<br><i><span id="adminPasswordFeedback"></span></i>
			</div>
			<a href="/MathRelay/Documentation.php" target="_blank"><div id="documentation">
				<h1>Documentation</h1>
				<i>Click on this box to the full documentation page</i>
			</div></a>
			</div>
		</div>
	</body>
</html>