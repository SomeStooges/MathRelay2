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
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			var displayOptions = "0;0;0;0;0;0";
			var displayNumber = 0;
			var response = "PeanutButter";
			var currentEvent = "";
			function getDisplayOptions(){
				$.post('/MathRelay/server/Admin.php',"action=getDisplayOptions",function(data){
					displayOptions = JSON.parse(data);
				});
				$.post('/MathRelay/server/Admin.php',"action=getDisplayNumber",function(data){
					displayNumber = JSON.parse(data);
				});
			}
			
			function buildTable(){
				getDisplayOptions();
				var options = displayOptions.split(";");
				var build = "";
				var tag = "";
				for(x=0;x<displayNumber;x++){
					build+="<tr>";
					if(((x+1)%2)==1){
						tag = "A";
					} else {
						tag = "B";
					}
					if(options[0]==1){
						build+="<td class='rankPanel"+tag+"'>#"+(x+1)+"</td>";
					}
					if(options[1]==1){
						build+="<td class='rankPanel"+tag+"'>"+response[x].TeamID+"</td>";
					}
					if(options[2]==1){
						build+="<td class='nicknamePanel"+tag+"'>"+response[x].TeamNickname+"</td>";
					}
					if(options[3]==1){
						build+="<td class='PointsPanel'>"+response[x].Points3+"</td>";
					}
					if(options[4]==1){
						build+="<td class='PointsPanel'>"+response[x].Points2+"</td>";
					}
					if(options[5]==1){
						build+="<td class='PointsPanel'>"+response[x].Points1+"</td>";
					}
					build+="</tr>";						
				}
				//console.log("BDUILD: " + build);
				$("#scoreboardDisplay").html(build);
			}
			
			function updateTable(){
				
				var build="";
				$.post('/MathRelay/server/Admin.php',"action=getEvent",function(data){
					var response2 = JSON.parse(data);
					//console.log(response.value);
					currentEvent = response2.value;
				});
				$.post('/MathRelay/server/Admin.php',"action=getTeamSummary",function(data){
					//console.log(response);
					
					if((currentEvent == "freezeScoreboard" || currentEvent == "stop") /*&& response != "PeanutButter"*/){
						console.log("isFrozen");
						$("#scoreboardDisplay").html("<tr><td style='color:black'>Scoreboard is Frozen</td></tr>");
					} else {
						response = JSON.parse(data);
						console.log("notFrozen");		
						buildTable();
					}
					
				});
			}
			
			$(document).ready(function(){

				window.setInterval(updateTable,1000);
			});
		</script>
		
		<style>
			*{
				font-size: 40px;
				color: white;
				font-weight: bold;
			}
			#pageContent{
				width: 100%;
			}
			.rankPanelA{
				background: white url("Rank_Box_Green.jpg") no-repeat center center;
				width: 124px;
				height: 74px;
				text-align: center;
			}
			.nicknamePanelA{
				width:500px;
				height: 74px;
				background: white url("Nickname_Box_Green.jpg") no-repeat center center;
				padding-left: 15px;
			}
			
			.rankPanelB{
				background: white url("Rank_Box_Blue.jpg") no-repeat center center;
				height: 74px;
				text-align: center;
				padding: 10px;
				
			}
			.nicknamePanelB{
				width: 500px;
				height: 74px;
				background: white url("Nickname_Box_Blue.jpg") no-repeat center center;
				padding-left: 15px;
			}
			
			.pointsPanel{
				width: 124px;
				height: 74px;
				background: white url("points_Panel.jpg") no-repeat center center;
				text-align: center;
			}
			
			.movementPanelUP{
				background: white url("Arrow_Up.jpg") no-repeat center center;
				padding: 10px;
				width: 70px;
			}
			.movementPanelDOWN{
				background: white url("Arrow_Down.jpg") no-repeat center center;
				padding: 10px;
			}
			#pageContent{
				position: relative;
			}

			
					
		</style>
	</head>
	
	<body>
		<div id="page-wrap" style="width:1500px">
			<div id="imageDiv">
				<img src="Math_Relay_Banner.jpg" alt="McNeil Mu Alpha Theta Math Relay">
			</div>	
			<div id="pageContent" >
					<div id="sampleTable">
						<table id="scoreboardDisplay">
							<tr><td style="color:black">Retrieving scoreboard data...</td></tr>
						</table>
					</div>
			</div>
		</div>
	</body>
</html>