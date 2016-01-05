<?php
	session_start();
	
	
	//mysqli_connect('localhost','asdlf4','k3j5l2kj542lkcn54nt');
	//mysqli_select_db("mathrelay");

	
	function db_Query($Query){
		$con = mysqli_connect('localhost','root','','mathrelay2');
		$result=mysqli_query($con, $Query);
	
		if (!$result) {
			print mysqli_error($con);
			die("insert failed for query\n"); 
		}
		
		mysqli_close($con);
		return $result;
	}
	
	function assignNickname(){
		$teamID=$_SESSION['teamID'];
		$nickname=$_REQUEST['nickname'];
		$Query="UPDATE team_data SET TeamNickname='$nickname' WHERE TeamID='$teamID';";
		echo "hello";
		db_Query($Query);
		return($nickname);
	}
	
	function submitAnswer(){
		//creates and object to pack all the answers into.
		$information = null;
		$information->teamID = $_SESSION['teamID'];
		$information->series = $_REQUEST['series'];
		$information->answer3 = $_REQUEST['answer3'];
		$information->answer2 = $_REQUEST['answer2'];
		$information->answer1 = $_REQUEST['answer1'];
		
		//grades each answer individually
		$information->award3 = gradeAnswer($information->series, 3, $information->answer3);
		if($information->award3==2){ //if the first one is missing, then it skips the other two.
			$information->award2 = 3;
			$information->award1 = 3;
		} else {
			$information->award2 = gradeAnswer($information->series, 2, $information->answer2);
			$information->award1 = gradeAnswer($information->series, 1, $information->answer1);
		}
		
		//scores points for the object; if the person has already scored, it rewrites awards to 0
		if($information->award3==1){
			scorePoints($information->teamID, $information->series, 3);
			$time = time();
			$teamID = $information->teamID;
			db_Query("UPDATE team_data SET lastPointTime='$time' WHERE TeamID='$teamID'");
		}
		if($information->award2==1){scorePoints($information->teamID, $information->series, 2);}
		if($information->award1==1){scorePoints($information->teamID, $information->series, 1);}
		
		//sends object to the log to enter.
		logScoreAttempt($information);
		return $information;
		
	}
	
	function scorePoints($teamID, $series, $level){
		$temp = "Award" .  $level;
		$Query = "SELECT * FROM log WHERE TeamID='$teamID' AND Series='$series' AND $temp='1';";
		//error_log("Testing\n", 3, "C:\wamp\www\MathRelay2\server\.log");
		$result = db_Query($Query);
		if(mysqli_num_rows($result)==0){
			$temp = "Points" . $level;
			$Query = "SELECT $temp FROM team_data WHERE TeamID='$teamID';";
			//error_log("Testing - $Query\n", 3, "C:\wamp\www\MathRelay2\server\.log");
			$result3 = db_Query($Query);
			$result2 = mysqli_fetch_array($result3);
			
			$result2[0]++;
			
			$Query = "UPDATE team_data SET $temp='$result2[0]' WHERE TeamID='$teamID';";
			//error_log("Testing - $Query\n", 3, "C:\wamp\www\MathRelay2\server\.log");
			db_Query($Query);	
		}	
	}
	
	function gradeAnswer($series,$level,$answer){
		//check if the answer is empty
		if($answer==""){
			return 2;
		}
		
		//finds if there is any record in the database
		$Query = "SELECT * FROM answer_table WHERE SeriesNumber='$series' AND LevelNumber='$level' AND Answer='$answer';";
		$result = db_Query($Query);
		
		//if none, then its wrong; else it's right
		if(mysqli_num_rows($result)<1){
			return 0;
		} else {
			return 1;
		}
	}
	
	function getCleanupParagraph(){
		$Query = "SELECT Value FROM relay_options WHERE Name='cleanupParagraph';";
		$result = db_Query($Query);
		$result2 = mysqli_fetch_object($result);
		return $result2->Value;
	}
	
	function logScoreAttempt($information){
		$timestamp = time();
		$Query = "INSERT INTO log VALUES ('$information->teamID','$information->series','$information->award3','$information->answer3','$information->award2','$information->answer2','$information->award1','$information->answer1','$timestamp');";
		db_Query($Query);
	}
	
	function updateQuestionStatus(){
		$teamID = $_SESSION['teamID'];
		$Query = "SELECT Series, Award3 FROM log WHERE TeamID='$teamID';";
		//error_log("Testing - $Query\n", 3, "C:\wamp\www\MathRelay2\server\.log");
		$result = db_Query($Query);
		$response = null;
		while($row = mysqli_fetch_object($result)){
			if(!isset($response[$row->Series])){
			$response[$row->Series] = 2;
			}
			switch($row->Award3){
				case 1:
				$response[$row->Series] = 1;
				break;
				
				case 0:
				if($response[$row->Series] != 1){
					$response[$row->Series]=0;
				}
				break;
				
				case 2:
				if($response[$row->Series]!= 0 || $response[$row->Series] != 1){
					$response[$row->Series]=2;
				}
				break;
			}
		}
		
		return $response;
	}
	
	function login(){
		$teamID=$_REQUEST['teamID'];
		$teamPassword=$_REQUEST['teamPassword'];
		$currentEvent = getEvent();
		if($currentEvent->value == "none" && $teamID != "admin"){
			return "notAllowed";
		}
		if($teamID=="admin"){
			$Query = "SELECT * FROM relay_options WHERE Name='adminPassword' AND Value='$teamPassword';";
			$result = db_Query($Query);
			if(mysqli_num_rows($result)==1){
				$_SESSION['teamID']="administrator";
				$_SESSION['password']="admin";
				$_SESSION['adminLogin']=true;
				return "administrator";
			} else {
				return "false";
			}
		} else {
			$Query = "SELECT * FROM team_data WHERE TeamID='$teamID' AND Password='$teamPassword';";
			$result = db_Query($Query);
			if(mysqli_num_rows($result)==1){
				$_SESSION['teamID']=$teamID;
				$_SESSION['password']=$teamPassword;
				return "true";
			} else {
				return "false";
			}
		}
	}
	function logout(){
		unset($_SESSION['teamID']);
		unset($_SESSION['password']);
		return true;
	}
	
	function getEvent(){
		$Query="SELECT value FROM relay_options WHERE Name='currentEvent';";
		$result = db_Query($Query);
		
		//Shows the team was logged in:
		if(isset($_SESSION['teamID'])){
			$carry = time();
			$teamID = $_SESSION['teamID'];
			$Query="UPDATE team_data SET lastCheckTime = '$carry' WHERE TeamID='$teamID'";
			db_Query($Query);
		}
		
		return mysqli_fetch_object($result);
	}
	
	function getFinalRank(){
		$teamID = $_SESSION['teamID'];
		
		$Query = "SELECT Rank FROM team_data WHERE TeamID='$teamID';";
		$result = mysqli_fetch_object(db_Query($Query));
		$rank = $result->Rank;
		
		switch($rank){
			case 1: $suffix = "st"; break;
			case 2: $suffix = "nd"; break;
			case 3: $suffix = "rd"; break;
			
			case 21: $suffix = "st"; break;
			case 22: $suffix = "nd"; break;
			case 23: $suffix = "rd"; break;
		}
		
		$message = "";
		
		if($rank<=25){
			$message = "<h1> You finished $rank<sup>$suffix</sup>!</h1>";
		}
		if($rank<=10){
			$message = "<h1>Congratulations! You finished $rank<sup>$suffix</sup>!</h1>";
		}
		
		return $message;
	}
	
	
	
	$action=$_REQUEST["action"];
	switch($action){
		case "login":
		$action=login();
		break;
		
		case "logout":
		$action=logout();
		break;
		
		case "assignNickname":
		$action=assignNickname();
		break;
		
		case "getFinalRank":
		$action = getFinalRank();
		break;
		
		case "submitAnswer":
		$action = submitAnswer();
		break;
		
		case "getCleanupParagraph":
		$action = getCleanupParagraph();
		break;
		
		case "updateQuestionStatus":
		$action = updateQuestionStatus();
		break;
		
		case "getEvent":
		$action=getEvent();
		break;
	}
	print json_encode($action);
?>