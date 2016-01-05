<?php
	session_start();
	if(!isset($_SESSION['password'])){
		header('location: index.php');
	}
	
	$teamID = $_SESSION['teamID'];
?>

<html>
	<head>
		<title> Answer Sheet </title>
			<!--- McNeil Blue is 210082; McNeil Green is 0A6B0A; Light Green: 00E100 Light Blue: 0749C1-->
			<!-- The calculator can be moved by modifying "left" on #calculator -->
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			//REFRESHES THE PAGE
			function UpdateData(){								
				$.post('/MathRelay/server/Runner.php',"action=getEvent",function(information){
					var response = JSON.parse(information);
					console.log(response.value);
					switch(response.value){
						case "none":
						$(".calculatorButton").prop("disabled",true);
						$("#page-wrap").css("background","white");
						$("#questionGradeAler").hide();
						window.location.href="index.php";
						break;
						
						case "openGates":
						$(".calculatorButton").prop("disabled",true);
						$("#page-wrap").css("background","white");
						$("#questionGradeAler").show();
						break;
						
						case "start":
						$(".calculatorButton").prop("disabled",false);
						$("#page-wrap").css("background","white");
						$("#questionGradeAler").show();
						break;
						
						case "freetime":
						$(".calculatorButton").prop("disabled",false);
						$("#page-wrap").css("background",'url("Freetime_Banner.jpg") no-repeat center center');
						$("#questionGradeAler").show();
						break;
						
						case "freezeScoreboard":
						$(".calculatorButton").prop("disabled",false);
						$("#page-wrap").css("background",'url("Freetime_Banner.jpg") no-repeat center center');
						$("#questionGradeAler").show();
						break;
						
						case "stop":
						$(".calculatorButton").prop("disabled",true);
						$("#page-wrap").css("background","white");
						$("#questionGradeAler").show();
						break;
						
						case "unfreezeScoreboard":
						$(".calculatorButton").prop("disabled",true);
						$("#page-wrap").css("background","white");
						window.location.href="ClosePage.php";
						break;
					}
				});
				window.setTimeout(UpdateData,1000);
			}
			
			
			$(document).ready(function(){				
				UpdateData();
				
				var calculatorPosition = 0; //left to right from 0 to 3
				var lastSeries = "";
				
				function moveCalculator(){
					var totalPoints = 0;
					var pointStatement = "";
					if(calculatorPosition==0){
							$.post('/MathRelay/server/Runner.php',"action=updateQuestionStatus",function(information){
								console.log(information);
								var response = JSON.parse(information);
								
								//alert(response);
								for(x in response){ 
									//alert(x+" : "+response[x]);
									var target = $("[id|='"+x+"']");
									switch(response[x]){
										case 0:
										$(target).css("background-color","lightCoral");
										break;
										
										case 1:
										totalPoints++;
										$(target).css("background-color","lightGreen");
										break;
										
										case 2:
										$(target).css("background-color","orange");
										break;
									}
								}
								
								
								if(totalPoints!=0){
									if(totalPoints==1){
										pointStatement = totalPoints+" Total Point";
									} else {
										pointStatement = totalPoints+" Total Points";
									}
									$("#qsHeader").html("QUESTION STATUS <br> "+pointStatement);
									console.log("Current Total Points: "+totalPoints);
								}
							});
						}
						
					if(calculatorPosition!=0 && $("#seriesNumberInput").val()!=lastSeries){
						$("#level1Input").val("");
						$("#level2Input").val("");
						$("#level3Input").val("");
					}
					
					console.log("Current Total Points2: "+totalPoints);
					switch(calculatorPosition){
						case 0:
						$("#calculator").css("left","0px");
						$("#submitButton").val("Next");
						if($("#questionGradeAlertTitle").text()!="No question has been attempted yet..."){$("#questionGradeAlert").show();}
						break;
						
						case 1:
						$("#calculator").css("left","250px");
						$("#submitButton").val("Next");
						$("#questionGradeAlert").hide();
						break;
						
						case 2:
						$("#calculator").css("left","500px");
						$("#submitButton").val("Next");
						$("#questionGradeAlert").hide();
						break;
						
						case 3:
						$("#calculator").css("left","750px");
						$("#submitButton").val("Submit");
						$("#questionGradeAlert").hide();
						break;
					}
				}
				
				function submitAnswer(){
					//packs an object to send to server
					obj = new Object;
					obj.series = $("#seriesNumberInput").val();
					obj.answer1 = $("#level1Input").val();
					obj.answer2 = $("#level2Input").val();
					obj.answer3 = $("#level3Input").val();
					obj.action = "submitAnswer";
					
					lastSeries = obj.series;
					
					if(obj.series == ""){
						$("#questionGradeAlertTitle").text("A series number is required.");
					}else{
						//posts to the server
						$.post('/MathRelay/server/Runner.php',obj,function(information){
							var response = JSON.parse(information);
							parseResponse(response);					
						});
					}

					
				}
				function parseResponse(obj){
					//0 means incorrect; 1 means correct; 2 means missing; 3 means error
					var answer = "";
					var award = null;
					switch(obj.award3){
						case 0:
							$("#questionGradeAlertTitle").text("Sorry, the answer to question "+obj.series+" was incorrect.");
							$("#questionGradeAlert").css("background-color","red");	
						break;
						
						case 1:
							$("#questionGradeAlertTitle").text("The answer to question "+obj.series+" was correct!");
							$("#questionGradeAlert").css("background-color","#0A6B0A");
							$(".Input").val("");
							calculatorPosition=0;
							moveCalculator();
							
						break;
						
						case 2:
							$("#questionGradeAlertTitle").text("A level 3 answer was missing from question "+obj.series+"!");
							$("#questionGradeAlert").css("background-color","orange");							
						break;
					}
					
					for(i=1;i<4;i++)
					{
						var target = null;
						switch(i){
							case 1: answer = obj.answer3; award = obj.award3; target = $("#question3Response"); break;
							case 2: answer = obj.answer2; award = obj.award2; target = $("#question2Response"); break;
							case 3: answer = obj.answer1; award = obj.award1; target = $("#question1Response"); break;
						}
						switch(award){
							case 0: 
							$(target).css("background-color","lightCoral"); 
							$(target).text("\""+answer+"\" was incorrect."); 
							break;
							
							case 1: 
							$(target).css("background-color","lightGreen"); 
							$(target).text("\""+answer+"\" was correct."); 
							break;
							
							case 2: 
							$(target).css("background-color","lightGrey"); 
							$(target).text("An answer was missing."); 
							break;
							
							case 3: 
							$(target).css("background-color","orange"); 
							$(target).text("The answer given was not graded because an answer for Level 3 is missing."); 
							break;
						}
					}
					
				}
				
				$("#submitButton").click(function(){
					if(calculatorPosition!=0){submitAnswer()};
					
					calculatorPosition++;
					if(calculatorPosition>3){
						calculatorPosition = 0;
					}
					moveCalculator();					
				});
				
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
				
				$("#nickname").focus(function(){
					if($(this).val()=="[Enter Nickname Here]"){
						$(this).css("background-color","#0A6B0A");
						$(this).css("font-style","normal");
						$(this).css("font-weight","bold");
						$(this).css("color","white");
						$(this).val("");
					}
				});
				
				$("#nickname").keypress(function(e){
					var key = e.which;
					if(key==13){
						$("#nickname").blur();
					}
				});
				
				$("#nickname").blur(function(){
					obj = new Object;
					obj.nickname=$(this).val();
					obj.action="assignNickname";
					alert("Checkpoint 1"+obj.nickname);
					$.post('/MathRelay/server/Runner.php',obj);
				});
				
				//PRINTS CHARACTER TO INPUT
				$(".calculatorButton").click(function(){
					
					$(".calculatorButton").css("background-color","");
					$(this).css("background-color","lightblue");
					var value = "";
					var addCharacter = true;
					var target;
					
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
				
				$("#logoutButton").click(function(){
					$.post('/MathRelay/server/Runner.php',"action=logout");
					window.location.href="index.php";
				});
			});
		</script>
		<style>	
			
			#nicknameSetter{
				position: relative;
				width: 600px;
				height: 50px;
				padding: 10px;
				
				background-color: #0A6B0A;
				margin: 0 auto;
				z-index: 5;
			}
			
			#nickname{
				position: relative;
				width: 600px;
				height: 50px;
				
				background-color: Grey;
				
				
				font-size: 40px;
				font-weight: normal;
				text-align: center;
				font-style: italic;
				color: black;
				
			}
			
			#answerTableDiv{
				position: relative;
				width: 1000px;
				padding-top: 50px;
				padding-bottom: 10px;
				margin-top: -40px;
				
				background-color: #0A6B0A;
				
				z-index: 4;
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
				background-color: #210082;
				
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
				
				width: 1000px;
				
				text-align: center;
				
				padding-top: 10px;
				background-color: #0A6B0A;
				color: white;
			}
			
			.questionGridBox{
				width: 100px;
				height: 40px;
				font-weight: bold;
				text-align: center;
				
				background-color: grey;
			}
			
			#level1Input, #level2Input, #level3Input, #seriesNumberInput{
				font-size: 20px;
				width: 200px;
			}
			.inputTD{
				
				width: 300px;
				text-align: center;
				padding-top: 5px;
				
			}
			#questionGradeAlert{
				position: relative;
				width: 650px;
				height: 260px;
				margin: 0;
				margin-top: -270px;
				margin-right: 50px;
				float: right;
				background-color: #0A6B0A;
				text-align: center;
			}
			
			.questionGradeAlertPanel{
				background-color: grey;
				padding: 10px;
				width: 180px;
				height: 120px;
				
				text-align: center;
			}
			
			.alertHeaderPanel{
				color: white;
				padding: 5px;
				font-size: 20px; 
			}
			
			#logoutButton{
				width: 100px;
				height: 30px;
				padding: 10px;
				
				font-weight: bold;
			}
		
		</style>
	</head>
	
	<body>
		<div id="page-wrap">
			<div id="imageDiv">
				<img src="Math_Relay_Banner.jpg" alt="McNeil Mu Alpha Theta Math Relay">
			</div>	
		<div id="pageContent">	
			<div id="nicknameSetter">
				<input name="nickname" id="nickname" value="[Enter Nickname Here]">
			</div>
			
			<div id="answerTableDiv">
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
						<td><input type="button" name="submitButton" id="submitButton" value="Next" class="calculatorButton"  style="width: 215px"></td>
					</tr>
				</table>
			</div>
			
			<div id="questionGradeAlert" hidden>
				<h1 id="questionGradeAlertTitle" >No question has been attempted yet...</h1>
				<table>
					<tr>
						<th class="alertHeaderPanel">Level 3</th>
						<th class="alertHeaderPanel">Level 2</th>
						<th class="alertHeaderPanel">Level 1</th>
					</tr>
					<tr>
						<td id="question3Response" class="questionGradeAlertPanel"></td>
						<td id="question2Response" class="questionGradeAlertPanel"></td>
						<td id="question1Response" class="questionGradeAlertPanel"></td>
					</tr>
				</table>
			</div>
			
			
			
			<div id="questionGrid">
				<table style="margin: 0 auto;">
					<tr>
						<td style="width: 200px;"></td>
						<td style="width: 500px; text-align: center">
							<h1 id="qsHeader" > QUESTION STATUS </h1>
						</td>
						<td style="text-align: right; width: 200px;"> <input type="button" id="logoutButton" value="Logout"> </td>
					</tr>
				</table>
				Grey means the question is unattempted.
				Green means the question was answered correctly.
				Orange means the question was not graded because a level 3 answer was not input.
				Red means the question was not answered correctly.
				<table>
					<?php
						$count=1;
						
						for($tens=1;$tens<5;$tens++){
							print "<tr>";
								
							for($level=1;$level<11;$level++){							
								print "<td id='$count' class='questionGridBox' style='background-color: grey'>";
								print "$count";
								print "</td>";
								$count++;
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