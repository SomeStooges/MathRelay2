<?php
	session_start();
	if(!isset($_SESSION['teamID']) || !isset($_SESSION['adminLogin'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>

<html>
	<head>
		<title> Answer Key </title>
			<!--- McNeil Blue is 210082; McNeil Green is 0A6B0A; Light Green: 00E100 Light Blue: 0749C1-->
			<!-- The calculator can be moved by modifying "left" on #calculator -->
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			function updateData(){
				$.post('/MathRelay/server/Admin.php',"action='getTeamSummary'");
			}
			
			function updateAnswers(){
				obj = new Object;
				obj.action = "updateAnswers";
				$.post('/MathRelay/server/Admin.php',obj,function(data){
					data = JSON.parse(data);
					console.log(data);
					for(x=0;x<data.length;x++){
						console.log(data[x]);
						var target = $("[id|='"+(x+1)+"']");
						switch(data[x]){
							case 0:
							$(target).css("background-color","grey");
							break;
							
							case 1:
							$(target).css("background-color","lightgreen");
							break;
							
							case 2:
							$(target).css("background-color","darkgreen");
							break;
							
							case 3:
							$(target).css("background-color","red");
							break;
							
						}
					}
				});
			}
			
			$(document).ready(function(){
				window.setInterval(updateData,1000);
				updateAnswers();
				//GENERAL FUNCTION TO MOVE CALCULATOR
				var calculatorPosition = 0; //left to right from 0 to 3
				function assignAnswer(){
					obj = new Object;
					
					obj.seriesNumber = $("#seriesNumberInput").val();
					obj.level3Input = $("#level3Input").val();
					obj.level2Input = $("#level2Input").val();
					obj.level1Input = $("#level1Input").val();
					
					obj.action = "assignAnswer";
					
					alert(obj);
					$.post('/MathRelay/server/Admin.php',obj,function(data){
						parsedData = JSON.parse(data);
						alert(parsedData);
					});
					
				}
				
				function moveCalculator(){
					switch(calculatorPosition){
						case 0:
						$("#calculator").css("left","0px");
						$("#submitButton").val("Next");
						break;
						
						case 1:
						$("#calculator").css("left","250px");
						$("#submitButton").val("Next");
						break;
						
						case 2:
						$("#calculator").css("left","500px");
						$("#submitButton").val("Next");
						break;
						
						case 3:
						$("#calculator").css("left","750px");
						$("#submitButton").val("Submit");
						break;
					}
				}
				
				//WHEN SUBMIT OR NEXT BUTTON IS CLICKED
				$("#submitButton").click(function(){
					
					if(calculatorPosition==3){
						assignAnswer();
						alert("Ran assignAnswer");
					}
					
					calculatorPosition++;
					if(calculatorPosition>3){
						calculatorPosition = 0;
					}
					moveCalculator();
				});
				
				//MOVES CALCULATOR TO FOCUSED INPUT
				$(".Input").focus(function(){
					switch($(this).attr("id")){
						case "seriesNumberInput":
						calculatorPosition=0;
						break;
						
						case "level3Input":
						calculatorPosition=1;
						break;
						
						case "level2Input":
						calculatorPosition=2;
						break;
						
						case "level1Input":
						calculatorPosition=3;
						break;
					}
					moveCalculator();
				});
				
				$(".calculatorButton").click(function(){
					
					$(".calculatorButton").css("background-color","");
					$(this).css("background-color","lightblue");
					var value = "";
					var addCharacter = true;
					var target
					
					switch(calculatorPosition){
							case 0: target = $("#seriesNumberInput"); break;
							case 1: target = $("#level3Input"); break;
							case 2: target = $("#level2Input"); break;
							case 3: target = $("#level1Input"); break;
					}
						
					switch($(this).attr("id")){
						case "b1": value="1"; break;
						case "b2": value="2"; break;
						case "b3": value="3"; break;
						case "b4": value="4"; break;
						case "b5": value="5"; break;
						case "b6": value="6"; break;
						case "b7": value="7"; break;
						case "b8": value="8"; break;
						case "b9": value="9"; break;
						case "b0": value="0"; break;
						case "b.": value="."; break;
						case "b(-)": value="-"; break;
						case "b(": value="("; break;
						case "b)": value=")"; break;
						
						case "bU": value="U"; break;
						case "b/": value="/"; break;
						case "b*": value="*"; break;
						case "b-": value="-"; break;
						case "b+": value="+"; break;
						case "bi": value="i"; break;
						
						case "bPi":	value=$("<span>").html("&#928;").text(); break;
						case "bInf": value=$("<span>").html("&#8734;").text(); break;
						case "bRoot": value=$("<span>").html("&#8730;").text(); break;
						
						case "bDel": 
						$(target).val( $(target).val().substring( 0,( $(target).val().length-1 ) ) );
						
						break;
						
						case "bClr": 
						$(target).val("");
						break;
					}
					
					if(addCharacter){
						$(target).val($(target).val()+value);
					}
				});
				
				$(".questionGridBox").click(function(){
					console.log($(this).attr("id"));
					obj = new Object;
					obj.action = 'getAnswers';
					obj.series = $(this).attr("id");
					$.post('/MathRelay/server/Admin.php',obj,function(data){
						var response = JSON.parse(data);
						if(response != null){
							console.log("example"+response[0].SeriesNumber);
							var message = "<tr><th class='keyStatusPanel'> Series Number </th><th class='keyStatusPanel'> Level </th><th class='keyStatusPanel'> Answer </th><th class='keyStatusPanel'> Delete </th></tr>"
							for(x=0;x<response.length;x++){
							
							message+="<tr><td class='keyStatusPanel'>"+response[x].SeriesNumber+"</td>";
							message+="<td class='keyStatusPanel'>"+response[x].LevelNumber+"</td>";
							message+="<td class='keyStatusPanel'>"+response[x].Answer+"</td>";
							message+="<td class='keyStatusPanel'>"+"Coming Soon!"+"</td></tr>";
							}
						
							$("#databaseAnswers").html(message);
						} else {
							$("#databaseAnswers").html("<tr><td><b>Currently no answers are set!</b></td></tr>");
						}
						
						
					});
				});
				
				
				//$("")
			});
		</script>		
		<style>
			
			#nicknameSetter{
				position: relative;
				top:10px;
				width: 600px;
				height: 50px;
				padding: 10px;
				
				
				margin: 0 auto;
				z-index: 5;
				
				text-align: center;
				
				background-color: #210082;
			}
			
			#nickname{
				position: relative;
				width: 600px;
				height: 50px;
				
				font-size: 40px;
				font-weight: bold;
				
			}
			
			#answerTableDiv{
				position: relative;
				width: 1000px;
				padding-top: 50px;
				margin-top: -30px;
				padding-bottom: 10px;
				
				z-index: 4;
				
				background-color: #210082;
				
				text-align: center;
			}
			#answerTable{
				margin: 0 auto;
				font-size:20px;
				color: white;
			}
			

			
			.answerBox{
				width: 100px;
			}
			
			#calculator{
				position: relative;
				width: 250px;
				height: 440px;
				left:0px;
				
				margin: -150px 0px 0px;
				background-color: 0A6B0A;
				
			}
			.calculatorTable{
				position: relative;
				margin: 0 auto;
				top: 160px;
			}
			.calculatorButton{
				width: 40px;
				height: 40px;
				font-weight: bold;
				text-align: center;
				margin: 2px;
			}
			
			#questionGrid{
				position: relative;
				top: 15px;
				
				text-align: center;
				
				padding-top: 10px;
				
				background-color: #341A82;
			}
			
			.questionGridBox{
				width: 100px;
				height: 40px;
				font-weight: bold;
				text-align: center;
				
				margin: 2px;
			}
			
			#level1Input, #level2Input, #level3Input, #seriesNumberInput{
				font-size: 15px;
				width: 200px;
			}
			.inputTD{
				
				width: 300px;
				text-align: center;
				padding-top: 5px;
				
			}
			
			#databaseAnswersDiv{
				position: relative;
				top: 40px;
				text-align: center;
				
				background-color: #483482;
				color: white;
			}
			.keyStatusPanel{
				padding: 5px;
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
						<th><input type="button" onclick="location.href='/MathRelay/ControlPanel.php';"class="headerPanel" value="Control Panel"></th>
						<th><input type="button" onclick="location.href='/MathRelay/InformationStream.php';" class="headerPanel" value="Information Stream"></th>
						<th><input type="button" onclick="location.href='/MathRelay/AnswerKey.php';" class="headerPanel" value="Answer Key" style="background-color:#99ff99"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Scoreboard.php';" class="headerPanel" value="Scoreboard"></th>
						<th><input type="button" onclick="location.href='/MathRelay/Options.php';" class="headerPanel" value="Options"></th>
					</tr>
				</table>
			</div>
			<div id="nicknameSetter" >
				<h1>ANSWER KEY</h1>
				
				<!-- FIX THIS-->
			</div>
			
			<div id="answerTableDiv">
			<i>Enter the answer as it would be answered. Multiple correct answers can be entered. The submit button must be hit under Level 1 to enter the answer.</i>
				<table id="answerTable">
					<tr>
						<th> Series <br> Number </th>
						<th> Level 3 </th>
						<th> Level 2 </th>
						<th> Level 1 </th>
					</tr>
					<tr>
						<td class="inputTD"><input name="seriesNumberInput" id="seriesNumberInput" value='' class="Input"></td>
						<td class="inputTD"><input name="level3Input" id="level3Input" value='' class="Input"></td>
						<td class="inputTD"><input name="level2Input" id="level2Input" value='' class="Input"></td>
						<td class="inputTD"><input name="level1Input" id="level1Input" value='' class="Input"></td>
					</tr>
				</table>
			</div>
					
			<div id="calculator">
				<table class="calculatorTable">
					<tr>
						<td><input type="button" id="b(" value="(" class="calculatorButton"></td>
						<td><input type="button" id="b)" value=")" class="calculatorButton"></td>
						<td><input type="button" id="bi" value="i" class="calculatorButton"></td>
						<td><input type="button" id="bDel" value="Del" class="calculatorButton"></td>
						<td><input type="button" id="bClr" value="Clr" class="calculatorButton"></td>
					</tr>
					<tr>
						<td><input type="button" id="b7" value="7" class="calculatorButton"></td>
						<td><input type="button" id="b8" value="8" class="calculatorButton"></td>
						<td><input type="button" id="b9" value="9" class="calculatorButton"></td>
						<td><input type="button" id="bPi" value="&#928" class="calculatorButton"></td>
						<td><input type="button" id="b/" value="/" class="calculatorButton"></td>
					</tr>
					<tr>
						<td><input type="button" id="b4" value="4" class="calculatorButton"></td>
						<td><input type="button" id="b5" value="5" class="calculatorButton"></td>
						<td><input type="button" id="b6" value="6" class="calculatorButton"></td>
						<td><input type="button" id="bInf" value="&#8734" class="calculatorButton"></td>
						<td><input type="button" id="b*" value="*" class="calculatorButton"></td>
					</tr>
					<tr>
						<td><input type="button" id="b1" value="1" class="calculatorButton"></td>
						<td><input type="button" id="b2" value="2" class="calculatorButton"></td>
						<td><input type="button" id="b3" value="3" class="calculatorButton"></td>
						<td><input type="button" id="bRoot" value="&#8730" class="calculatorButton"></td>
						<td><input type="button" id="b-" value="-" class="calculatorButton"></td>
					</tr>
					<tr>
						<td><input type="button" id="b0" value="0" class="calculatorButton"></td>
						<td><input type="button" id="b." value="." class="calculatorButton"></td>
						<td><input type="button" id="b(-)" value="(-)" class="calculatorButton"></td>
						<td><input type="button" id="bU" value="U" class="calculatorButton"></td>
						<td><input type="button" id="b+" value="+" class="calculatorButton"></td>
					</tr>
				</table>
				<table class="calculatorTable">
					<tr>
						<td><input type="button" name="submitButton" id="submitButton" value="Next" class="calculatorButton"  style="width: 216px"></td>
					</tr>
				</table>
			</div>
			
			<div id="questionGrid" >
				<h1> ANSWER STATUS </h1>
				<i>Indicates the current status of the answers in the database.
				Grey indicates no answers for the numbered series.
				<br>Light green indicates one answer for each level.
				Dark green indicates multiple answers for at least one level.
				<br> Red indicates a missing answer for at least one level.</i>
				<table>
					<?php
						$count=1;
						
						for($tens=1;$tens<5;$tens++){
							print "<tr>";
								
							for($level=1;$level<11;$level++){							
								print "<td id='$count' class='questionGridBox' style='background-color:grey'>";
								print "$count";
								print "</td>";
								$count++;
							}
							print "</tr>";						
						}
					?>
				</table>
			</div>
			<div id="databaseAnswersDiv">
				Click number to see what answers are already entered for that series.
				<table id="databaseAnswers">
				</table>
			<div>
		</div>
		</div>
	</body>
</html>