<?php
	session_start();
	if(!isset($_SESSION['teamID']) || !isset($_SESSION['adminLogin'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>

<html>
	<head>
		<title> Control Panel </title>
		<!-- McBlue: #210082
			 McGreen: #0A6B0A
			 Light Blue 1: #341A82
			 Light Blue 2: #483482 -->
		<link rel="stylesheet" type="text/css" href="style.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src='scripts/retrieval.js'></script>
		<script>
			var logCounter = 0;
			var lastTime = 0;
			
			//Switches which button appears selected
			function switchVisualEvent(name){
				console.log("1. Looking for event with id "+name);
				$(".eventPanel").css("background-color","");
				$(".eventPanel").prop("disabled",false);	
				if(name == "reset" || name == "clear"){
					$("#none").css("background-color","lightCoral");
				} else {
					var eventButtons = ["none","openGates","start","freetime","freezeScoreboard","stop","unfreezeScoreboard"];				
					var a = eventButtons.indexOf(name);
					console.log("2. The array location is : "+a);
					for(i=0;i<a;i++){
						$("[id|='"+eventButtons[i]+"']").prop("disabled",true);
					}
				}				
				$("[id|='"+name+"']").css("background-color","lightCoral");	
			}
			
			//Switches which event is registered in the database
			function changeEvent(value){
				if(value == "clear" || value == "reset"){
					value = "none";
				}					
				setRelayOption("currentEvent",value,function(data){
					switchVisualEvent(data);
				});				
				
			}
			
			function getTimer(){
				$.post('/MathRelay/server/Admin.php',"action=getTimer",function(data){
					console.log("The data coming back is: " + data);
					lastTime = JSON.parse(data);
				});
			}

			/*function switchEvent(target,resetTime){
				//Resets all the event buttons and colors the selected button
					$(".eventPanel").css("background-color","");
					$(target).css("background-color","LightBlue");
					
					//Takes actions on the rest of the buttons.
					switch($(target).attr("id")) {
						case "openGates":
						break;
						
						case "start":
							$("#openGates").prop("disabled",true);
							//Post something
						break;
						
						case "freetime":
							$("#openGates").prop("disabled",true);
							$("#start").prop("disabled",true);
							if(resetTime){
								$.post('/MathRelay/server/Admin.php',"action=rankFreetime",function(){
									alert("FIRED!");
								})
							}
						break;
						
						case "freezeScoreboard":
							$("#openGates").prop("disabled",true);
							$("#start").prop("disabled",true);
							$("#freetime").prop("disabled",true);
							//Post something
						break;
						
						case "stop":
							$("#openGates").prop("disabled",true);
							$("#start").prop("disabled",true);
							$("#freetime").prop("disabled",true);
							$("#freezeScoreboard").prop("disabled",true);
							//Post something
						break;
						
						case "unfreezeScoreboard":
							$("#openGates").prop("disabled",true);
							$("#start").prop("disabled",true);
							$("#freetime").prop("disabled",true);
							$("#freezeScoreboard").prop("disabled",true);
							$("#stop").prop("disabled",true);
							//Post something
						break;
						
						case "clear":
							$(".eventPanel").prop("disabled",false);
							$(target).css("background-color","LightCoral");
							//Post something
						break;
						
						case "reset":
							$(".eventPanel").prop("disabled",false);
							$(target).css("background-color","LightCoral");
							//Post something
						break;						
					}
					if(resetTime){
						//console.log("Resetting the timer");
						var triggerTime = new Date();
						
						obj = new Object;																		
						obj.action="setTimer";
						obj.value = triggerTime.getTime();
						
						$.post('/MathRelay/server/Admin.php',obj,function(data){;
							//console.log("calling the callback function");
							getTimer();
						});
					}
					
					
					obj = new Object;
					obj.action = "changeEvent";
					obj.eventName = $(target).attr("id");
					$.post('/MathRelay/server/Admin.php',obj,function(data){
						var data2 = JSON.parse(data);
						//alert(data2);
					});
			}*/
			
			function UpdateData(){
				//updating the Timers
				var currentTime = new Date();
				var hour = currentTime.getHours();
				var minute = currentTime.getMinutes();
				var second = currentTime.getSeconds();
				
				minute = ( minute < 10 ? "0":"") + minute;
				second = ( second < 10 ? "0":"") + second;
				
				var appendage = (hour < 12) ? "AM":"PM";
				hour = (hour > 12) ? hour-12:hour;
				
				var readout = hour +":"+minute+":"+second+" "+appendage;
				
				$("#currentTimeElement").text(readout);
				
				var elapsedTime = currentTime.getTime() - lastTime;
				
				if(elapsedTime < 1000000){
					var eMinutes = Math.floor(elapsedTime/60000);
					var eSeconds = Math.floor(elapsedTime/1000)-60*eMinutes;

					var feSeconds = (eSeconds < 10 ? "0":"") + eSeconds;
					
					var formatTime = (eMinutes) +":"+(feSeconds);
					
					
					$("#elapsedTimeElement").html(formatTime);
				} else {
					$("#elapsedTimeElement").html("<i>Unavailable</i>");
				}
				
				
				//updating the login status
				obj = new Object;
				obj.action = "getLoggedInTeams";
				$.post('/MathRelay/server/Admin.php',obj,function(data){
					var teams = JSON.parse(data);
					if(teams!=null){
						console.log("TEAMS is: " + teams + " with length " + teams.length);
						$(".teamLoginPanel").css("background-color","grey");
						for(x=0;x<teams.length;x++){
							$("[id|='"+teams[x]+"']").css("background-color","lightGreen");
						}
					}
					
				});
				
				$.post('/MathRelay/server/Admin.php',"action='getTeamSummary'");
			}
			
			$(document).ready(function(){
				switchVisualEvent("unfreezeScoreboard");
				
				setRelayOption("currentEvent","stop",function(data){
					console.log("Set data is: "+data);
				});
				getRelayOption("currentEvent",function(data){
					console.log("Data in the callback is: " + data);
				});
				getTimer();
				//getEvent();
				
				$(".eventPanel").click(function(){
					changeEvent($(this).attr("id"));
				});
				
				window.setInterval(UpdateData,1000);
				
			});
		</script>
		<style>
			
			#teamLogin{
				position: relative;
				top: 30px;
				text-align: center;				
				padding-top: 10px;
				background-color: #483482;
			}
			.teamLoginPanel{				
				
				width: 200px;
				height: 50px;
				text-align: center;
				font-weight: bold;
				
			}
			
			#eventScreen{
				position: relative;
				top: 10px;
				width: 1000px;				
				background-color:#210082;;
				
				text-align: center;
				
				padding-top: 10px;
			}
			.eventPanel{
				width: 80x;
				height: 140px;
				margin: 5px;
				padding: 5px;
			}
			
			#timers{
				position:relative;
				background-color: blue;
				width: 1000px;
				top: 20px;
				
				background-color: #341A82;
				
				text-align: center;				
				padding-top: 10px;
			}
			
			.timerPanel{
				width: 400px;
				padding: 10px;
				
				font-size: 20px;
				text-align: center;
				
				color: white;
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
						<th><input type="button" onclick="location.href='/MathRelay/ControlPanel.php';"class="headerPanel" value="Control Panel" style="background-color:#99ff99"></th>
						<th><input type="button" onclick="location.href='/MathRelay/InformationStream.php';" class="headerPanel" value="Information Stream"></th>
						<th><input type="button" onclick="location.href='/MathRelay/AnswerKey.php';" class="headerPanel" value="Answer Key"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Scoreboard.php';" class="headerPanel" value="Scoreboard"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Options.php';" class="headerPanel" value="Options"></th>
					</tr>
				</table>
			</div>
			
			<div id="eventScreen">
				<h1>EVENT CONTROL PANEL</h1>
				<i> Select the current event of the program or reset program data.<br>Events can only be selected from left to right until a "clear" or "reset."</i>
				<table>
					<tr>
						<th><button type="button" name="none" id="none" class="eventPanel">
							<b>None</b>
							<br><br> No event is currently selected.
						</button></th>
						<th><button type="button" name="openGates" id="openGates" class="eventPanel">
							<b>Open Gates</b>
							<br><br> Allow team captains to log in.
						</button></th>
						<th><button type="button" name="start" id="start" class="eventPanel">
							<b>Start</b>
							<br><br> Allow submissions of answers.
						</button></th>
						<th><button type="button" name="freetime" id="freetime" class="eventPanel">
							<b>Free Time</b>
							<br><br> Alert team captains of free time visually.
						</button></th>
						<th><button type="button" name="freezeScoreboard" id="freezeScoreboard" class="eventPanel">
							<b>Freeze Scoreboard</b>
							<br><br> Stop the scoreboard from updating.
						</button></th>
						<th><button type="button" name="stop" id="stop" class="eventPanel">
							<b>Stop</b>
							<br><br> Stop submissions of answers. 
						</button></th>
						<th><button type="button" name="unfreezeScoreboard" id="unfreezeScoreboard" class="eventPanel">
							<b>Unfreeze Scoreboard</b>
							<br><br> Update the scoreboard to final ranks. 
						</button></th>
						<th><button type="button" name="clear" id="clear" class="eventPanel">
							<b>Clear</b>
							<br><br> Clear all points and nicknames.
						</button></th>
						<th><button type="button" name="reset" id="reset" class="eventPanel">
							<b>Reset</b>
							<br><br> Reset all points, nicknames, and passwords.
						</button></th>
					</tr>
				</table>
			</div>
			
			<div id="timers">
			<h1>EVENT TIMERS</h1>
			<i>Displays the current time and the elapsed time since the latest event started.</i>
				<table>
					<tr>
						<td class="timerPanel"> Current Time </td>
						<td class="timerPanel"> Elapsed Time </td>
					</tr>
					<tr>
						<td class="timerPanel"><span id="currentTimeElement"><i>Retrieving current time</i></span> </td>
						<td class="timerPanel"><span id="elapsedTimeElement"><i>Retrieving elapsed time</i></span></td>
					</tr>
				</table>	
			</div>
			
			<div id="teamLogin">
			<h1>TEAM LOGIN STATUS</h1>
			<i>Displays whether a team is logged-in or not. 
			<br>Grey indicates the team is not logged-in. Green indicates the team is logged-in.</i>
				<table>
				<?php	
					//This determines the number of rows necessary to print all the teams.
					$numberOfTeams=50;
					$teamsPerRow=10;
					//This prints the rows
					$counter=0;
					while($counter<$numberOfTeams){
						print "<tr>";
						for($i=0;$i<$teamsPerRow;$i++){
							$counter++;
							print "<td name='$counter' id='$counter' class='teamLoginPanel' style='background-color: grey'>";
							print "$counter";
							print "</td>";							
							if($counter>=$numberOfTeams){
								$i+=$teamsPerRow;
							}
						}
						print "</tr>";
					}
				?>
				</table>
			</div>
			</div>
			
		</div>
	</body>
</html>