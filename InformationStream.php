<?php
	session_start();
	if(!isset($_SESSION['teamID']) || !isset($_SESSION['adminLogin'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>
<html>
	<head>
		<title> Information Stream </title>
		<!-- McBlue: #210082
			 McGreen: #0A6B0A
			 Light Blue 1: #341A82
			 Light Blue 2: #483482 -->
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			function UpdateData(){								
				$.post('/MathRelay/server/Admin.php',"action=getTeamSummary",function(data){
					//console.log(data);
					var response = JSON.parse(data);
					var message = "<tr><th>Team ID</th><th>Password</th><th>Nickname</th><th>Level 3 Points</th><th>Level 2 Points</th><th>Level 1 Points</th><th>Rank At Freetime</th></tr>";
					for(x=0;x<response.length;x++){
						if(response[x].FreetimeRank==0){response[x].FreetimeRank="N/A"}
						
						message+="<tr>"
						message+="<td class='dataTablePanel' style='width:40px;text-align:center'>" + response[x].TeamID + "</td> ";
						message+="<td class='dataTablePanel' style='width:80px'>" + response[x].Password + "</td>";
						message+="<td class='dataTablePanel' style='width:150px'>" + response[x].TeamNickname + "</td> ";
						message+="<td class='dataTablePanel' style='width:70px;text-align:center'>" + response[x].Points3 + "</td> ";
						message+="<td class='dataTablePanel' style='width:70px;text-align:center'>" + response[x].Points2 + "</td> ";
						message+="<td class='dataTablePanel' style='width:70px;text-align:center'>" + response[x].Points1 + "</td> ";
						message+="<td class='dataTablePanel' style='width:70px;text-align:center'>" + response[x].FreetimeRank + "</td> ";
						message+="</tr>"
					}
					$("#dataTable").html(message);
					//console.log(message);
					
					//changes the height of the log.
					$("#tableContainer").height(($("#teamSummary").height()-$("#manualOverride").height()-150));
				});
				
				$.post('/MathRelay/server/Admin.php','action=getLog',function(data){
					var response = JSON.parse(data);
					console.log("The response is:" + response);
					var message = "";
					console.log("Checkpoint 1");
					var adjTime=0;
					var printTime = "";
					var hours;
					var minutes;
					var seconds;
					var color;
					
					if(response){
						for(x=0;x<response.length;x++){
							/*switch(response[x].Award3){
								case "0":
								color = "lightCoral";
								break;
								
								case 1:
								color = "lightGreen";
								break;
								
								case 2:
								color = "orange";
								break;
								
								default:
								color = "";
								break;
							}*/
							adjTime = response[x].Timestamp % 86400 + 68400;
							console.log("Timestamp is: " + response[x].Timestamp + " The adjTime is: " + adjTime);
							
							hours = Math.floor(adjTime/3600);
							adjTime-=Math.floor(adjTime/3600)*3600;
							
							minutes = Math.floor(adjTime/60);
							adjTime-=Math.floor(adjTime/60)*60;
							
							seconds = adjTime;
							
							printTime = hours +":"+(minutes<10 ? "0":"") + minutes + ":"+(seconds<10 ? "0":"")+ seconds;
							
							message +="<tr ><td> Team ID: "+response[x].TeamID+"<br>";
							message +=printTime + "<br>";
							message +="Series: " + response[x].Series+"<br>";
							if(response[x].Award3==0){
								message += "\""+response[x].Answer3+"\" was incorrect for level 3.<br>";
							} else if(response[x].Award3==1){
								message += "\""+response[x].Answer3+"\" was correct for level 3.<br>";
							} else if(response[x].Award3==2){
								message +="No grade was given because level 3 was missing!<br>"
							}
							
							if(response[x].Award2==0){
								message += "\""+response[x].Answer2+"\" was incorrect for level 2.<br>";
							} else if(response[x].Award2==1){
								message += "\""+response[x].Answer2+"\" was correct for level 2.<br>";
							}
							
							if(response[x].Award1==0){
								message += "\""+response[x].Answer1+"\" was incorrect for level 1.<br>";
							} else if(response[x].Award1==1){
								message += "\""+response[x].Answer1+"\" was correct for level 1.<br>";
							}

							
							
							message +="<br></td></tr>";
							
						}
						$("#logPrintoutTable").html(message);
					}
				});
				
				window.setTimeout(UpdateData,1000);
			}
			
			$(document).ready(function(){
				UpdateData();
				$("#manualOverrideSubmit").click(function(){
					if($("#overrideTeamID").val()==""){
						alert("Cannot submit override because a Team ID is missing.");
					} else if($("#overrideType").val()==""){
						alert("Cannot submit override because an Action is not selected.");
					} else if($("#overrideCategory").val()==""){
						alert("Cannot submit override because a category is not selected.");
					} else if($("#overrideValue").val()==""){
						alert("Cannot submit override because a Value is missing.");
					} else if( ( $("#overrideCategory").val()=="teamPassword" || $("#overrideCategory").val()=="teamNickname" ) && ( $("#overrideType").val() != "set" ) ){
						alert("Passwords and Nicknames cannot use the 'add' or 'subtract' action.");
					} else if (($("#overrideCategory").val()!="teamPassword" && $("#overrideCategory").val()!="teamNickname") && (isNaN($("#overrideValue").val()))){
						alert("Points must be modified with numeric values.");
					} else {
						alert(isNaN($("#overrideValue").val()));
						
						obj = new Object;
						obj.action= "manualOverride";
						obj.teamID = $("#overrideTeamID").val();
						obj.type = $("#overrideType").val();
						obj.category = $("#overrideCategory").val();
						obj.value = $("#overrideValue").val();
						
						var message = "";
						message+="teamID: " + obj.teamID;
						message+="type: " + obj.type;
						message+="category: " + obj.category;
						message+="value: " + obj.value;
						console.log(message);
						$.post('/MathRelay/server/Admin.php',obj,function(data){
							console.log(data);
						});
						$(".overrideInput").val("");
					}
					
				});
			});
		</script>
		
		<style>
			#teamSummary{
				position: relative;
				top: 10px;
				float: left;
				
				width: 590px;
				background-color: #210082;
				
				padding: 10px;
				text-align: center;

			}
			#dataTable{
				color: white;
			}
			#manualOverride{
				position: relative;
				top: 10px;
				float: right;
				width: 360px;
				padding: 10px;
				text-align: center;
				background-color: red;
				
				font-weight: bold;
				color: yellow;
			}
			
			#logPrintout{
				position: relative;
				top: 20px;
				background-color: #341A82;
				float: right;
				
				width: 360px;
				padding: 10px;
				
				text-align: center;
			}
			#logPrintoutTable{
				color: white;
			}
			.overrideInput{
				width: 150px;
				margin: 3px;
			}
			.overridePanel{
				width: 150px;
				margin: 3px;
				color: yellow;
				text-align: right;
			}
			.dataTablePanel{
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
						<th><input type="button" onclick="location.href='/MathRelay/InformationStream.php';" class="headerPanel" value="Information Stream" style="background-color:#99ff99"></th>
						<th><input type="button" onclick="location.href='/MathRelay/AnswerKey.php';" class="headerPanel" value="Answer Key"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Scoreboard.php';" class="headerPanel" value="Scoreboard"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Options.php';" class="headerPanel" value="Options"></th>
					</tr>
				</table>
			</div>
			
			<div id="teamSummary">
				<h1>TEAM SUMMARY</h1>
				<i>Summary of all team data currently held in the database.<br>Arranged descending by Level 3 points.</i>
				<table id="dataTable">
					<tr>
						<th>Team ID</th>
						<th>Team Password</th>
						<th>Team Nickname</th>
						<th>Level 1 Points</th>
						<th>Level 2 Points</th>
						<th>Level 3 Points</th>
					</tr>
					<!-- The rest of the table will be built by the script-->
				</table>
			</div>
			
			<div id="manualOverride">	
				<h1 style="color:yellow">MANUAL OVERRIDE</h1>
				<i style="color:yellow">Enter a specific value into the database.</i><br>
				<table>
					<tr>
						<td class="overridePanel">
							Team ID: 
						</td>
						<td>
							<input name="overrideTeamID" id="overrideTeamID" value='' class="overrideInput">
						</td>
					</tr>
					<tr>
						<td class="overridePanel">
							Action: 
						</td>						
						<td>
							<select name="overrideType" id="overrideType" class="overrideInput">
							<option value=""></option>
							<option value="set">Set</option>
							<option value="add">Add</option>
							<option value="subtract">Subtract</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="overridePanel">
							Category:
						</td>
						<td>
							<select name="overrideCetagory" id="overrideCategory" class="overrideInput">
							<option value=""></option>
							<option value="teamPassword">Team Password</option>
							<option value="teamNickname">Team Nickname</option>
							<option value="level1Points">Level 1 Points</option>
							<option value="level2Points">Level 2 Points</option>
							<option value="level3Points">Level 3 Points</option>
							</select>
						</td>
					<tr>
						<td class="overridePanel">
							Value:
						</td>
						<td>
							<input name="overrideValue" id="overrideValue" value="" class="overrideInput">
						</td>
					</tr>
				</table>
				<input type="Button" id="manualOverrideSubmit" value="Submit" style="padding: 5px; margin: 2px; font-weight: bold;">
			</div>
			
			<div id="logPrintout">
				<h1>ACTION PRINTOUT</h1>
				<i>A summary of all actions taken by teams.</i>
				<div id="tableContainer" style="height: 260px; overflow: auto;">
				<table id="logPrintoutTable" cellspacing="0" cellpadding="1" border="1" width="300">
					
				</table>
				</div>
			</div>
		</div>
		</div>
	</body>
</html>