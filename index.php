<html>
	<head>
		<title> Login </title>
		
		<!--- McNeil Blue is 210082; McNeil Green is 0A6B0A; Light Green: 00E100 Light Blue: 0749C1-->
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			function interpretResponse(response){
				if(response=="true"){
					window.location.href="AnswerSheet.php";
				}
				if(response=="administrator"){
					window.location.href="ControlPanel.php";
				}
				if(response=="false"){
					$("#incorrectAlert").html("<br><br><i>An incorrect password was entered.</i>");
				}
				if(response=="notAllowed"){
					$("#incorrectAlert").html("<br><br><i>You cannot log in right now.</i>");
				}
			}
			
			$(document).ready(function(){
				$("#teamPassword").keypress(function(){
					if(event.which == 13){
						$("#submitButton").click();
					}
				});
				
				$("#submitButton").click(function(){
					obj = new Object;
					obj.teamID = $("#teamNumber").val();
					obj.teamPassword = $("#teamPassword").val();
					obj.action="login";
					
					if(isNaN(obj.teamID) && obj.teamID!="admin"){
						$("#incorrectAlert").html("<br><br><i>The 'Team Number' is a number.</i>");
					} else if (obj.teamID<1 || obj.teamID > 50){
						$("#incorrectAlert").html("<br><br><i>The 'Team Number' is from 1 to 50.</i>");
					} else {
						$.post('/MathRelay/server/Runner.php',obj,function(data){
							console.log(data);
							mydata=JSON.parse(data);
							interpretResponse(mydata);
						});
					}
					
				})
				
			});
		</script>
		<style>

			
			#informationPanel{
				position: relative;
				top: 10px;
				
				max-width: 400px;
				margin: 0 auto;
				text-align: center;
				
				padding: 10px;
				
				background-color: 0a6b0a;
				
				font-family: "Verdana";

			}
			#submitButton{
				width: 80px;
				height: 50px;
				font-family:inherit;
			}
			.textBox{
				text-align: right;
				padding-right:3px;
				color: white;

			}
		</style>
	</head>
	
	<body>
		<div id="page-wrap">
			<div id="imageDiv">
				<img src="Math_Relay_Banner.jpg" alt="McNeil Mu Alpha Theta Math Relay">
			</div>
			
			<div id="informationPanel">
				<h1>LOGIN</h1>
				
				<table style="margin: 0 auto;">
					<tr>
						<td class="textBox"> Enter Team Number: </td>
						<td><input name="teamNumber" id="teamNumber" value="" style=""></td>
					</tr>
					<tr>
						<td class="textBox"> Enter Team Password: </td>
						<td><input name="teamPassword" id="teamPassword" value="" style=""></td>					
				</table>
				
				<br><input type="Button" name="submitButton" id="submitButton" value="Submit">
				<span id="incorrectAlert"></span>
			</div>
		</div>
	</body>
</html>
