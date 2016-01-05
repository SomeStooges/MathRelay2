<?php
	session_start();
	error_reporting(0);

	//mysql_connect('localhost','asdlf4','k3j5l2kj542lkcn54nt');
   // mysql_select_db("mathrelay");
	
	function db_Query($Query){
		$con = mysqli_connect('localhost','root','','mathrelay2');
		$result=mysqli_query($con, $Query);
	
		if (!$result) {
			print mysqli_error($con);
			die("insert failed for query\n"); 
		}
		
		//mysqli_close($con);
		return $result;
	}
	
	function setRelayOption(){
		$name = $_REQUEST['name'];
		$value = $_REQUEST['value'];
		$Query = "SELECT * FROM relay_options WHERE Name='$name';";
		if(mysqli_num_rows(db_Query($Query))==0){
			$Query = "INSERT INTO relay_options VALUES ('$name','');";
		}
		$Query = "UPDATE relay_options SET Value='$value' WHERE Name='$name';";
		db_Query($Query);
		
		return getRelayOption();
	}
	
	function getRelayOption(){
		$name = $_REQUEST['name'];
		$Query = "SELECT `Value` FROM relay_options WHERE Name='$name';";
		
		$result = db_Query($Query);
		$row = mysqli_fetch_object($result);
		
		//return "$Query";
		//$result->Value = "Hello World!";
		return $row->Value;
	}
	
	function getEvent(){
		$Query="SELECT value FROM relay_options WHERE Name='currentEvent';";
		$result = db_Query($Query);
		return mysqli_fetch_object($result);
	}
	
	/*function assignAnswer(){
		$series = $_REQUEST['seriesNumber'];
		$level = $_REQUEST['level'];
		$answer = $_REQUEST['answer'];
		if($answer!="" && $level !="" && $series !=""){
			$Query = "INSERT INTO answer_tables VALUES ('$series','$level','$answer');";
			db_Query($Query);
		}
	}*/
	
	function assignAnswer(){
		$series = $_REQUEST['seriesNumber'];
		$level3 = $_REQUEST['level3Input'];
		$level2 = $_REQUEST['level2Input'];
		$level1 = $_REQUEST['level1Input'];
		
		if($level3 != ""){
			$Query = "INSERT INTO answer_table VALUES ('$series','3','$level3');";
			db_Query($Query);
		}
		if($level2 != ""){
			$Query = "INSERT INTO answer_table VALUES ('$series','2','$level2');";
			db_Query($Query);
		}
		if($level1 != ""){
			$Query = "INSERT INTO answer_table VALUES ('$series','1','$level1');";
			db_Query($Query);
		}
		
		return true;
	}
	
	function getAnswers(){
		$series = $_REQUEST['series'];
		$Query = "SELECT * FROM answer_table WHERE SeriesNumber='$series';";
		$result = db_Query($Query);
		$message = null;
		while($row = mysqli_fetch_object($result)){
			$message[]=$row;
		}
		return $message;
	}
	
	function changeAdminPassword(){
		$old = $_REQUEST['oldPassword'];
		$new1 = $_REQUEST['newPassword1'];
		$new2 = $_REQUEST['newPassword2'];
		if($old == ""){ return "The old password was not entered.";}
		if($new1 == ""){ return "The new password was not entered.";}
		if($new2 == ""){ return "The new password was not repeated.";}
		if($new1 != $new2){ return "The new passwords were not the same!";}
		
		$Query = "SELECT value FROM relay_options WHERE Name='adminPassword';";
		$hold = mysqli_fetch_array(db_Query($Query));
		$response = $hold[0];
		if($old == $response){
			db_Query("UPDATE relay_options SET Value='$new1' WHERE Name='adminPassword';");
			return "The password was reset.";
		} else {
			return "The current password is incorrect.";
		}
		
		return "There was an error. Sorry.";
	}
	
	function setCleanupParagraph(){
		$input = $_REQUEST['value'];		
		$Query="UPDATE relay_options SET Value='$input' WHERE Name='cleanupParagraph';";
		db_Query($Query);		
	}
	
	function getCleanupParagraph(){
		$Query = "SELECT Value FROM relay_options WHERE Name='cleanupParagraph';";
		$result = db_Query($Query);
		$result2 = mysqli_fetch_object($result);
		return $result2->Value;
	}
		
	function changeEvent(){
		//RECIEVES THE REQUEST
		$eventName = $_REQUEST['eventName'];
		
		if($eventName=="clear"){
			$eventName="none";
			clearTeams();
		}
		
		if($eventName=="reset"){
			$eventName="none";
			generateTeams();
		}
		
		//CREATES THE CATAGORY IF IT DOESN'T EXIST
		$Query="SELECT * FROM relay_options WHERE Name='currentEvent';";
		$result = db_Query($Query);
		if(mysqli_num_rows($result)==0){
			$Query = "INSERT INTO relay_options VALUES ('currentEvent','none');";
			db_Query($Query);
		}
		
		//UPDATES THE EVENT LISTING
		$Query = "UPDATE relay_options SET Value='$eventName' WHERE Name='currentEvent';";
		db_Query($Query);		
	}
	
	function clearTeams(){
		$number = getNumberOfTeams();
		$Query = "UPDATE team_data SET TeamNickname='',Points3='0',Points2='0',Points1='0',Rank='0',FreetimeRank='0';";
		db_Query($Query);
		$Query = "DELETE FROM log;";
		db_Query($Query);
	}
	
	function setDisplayNumber(){
		$value = $_REQUEST['value'];
		$Query = "UPDATE relay_options SET Value='$value' WHERE Name='displayNumber';";
		db_Query($Query);
	}
	
	function getDisplayNumber(){
		$Query = "SELECT Value FROM relay_options WHERE Name='displayNumber';";
		$result = mysqli_fetch_object(db_Query($Query));
		return $result->Value;
	}
	
	function setDisplayOptions(){
		$value = $_REQUEST['value'];
		//error_log("Value: $value\n", 3, "C:\wamp\www\MathRelay2\server\.log");
		$Query = "UPDATE relay_options SET Value='$value' WHERE Name='displayOptions';";
		//error_log("Query: $Query\n", 3, "C:\wamp\www\MathRelay2\server\.log");
		db_Query($Query);
	}
	
	function getDisplayOptions(){
		$Query = "SELECT Value FROM relay_options WHERE Name='displayOptions';";
		$result = mysqli_fetch_object(db_Query($Query));
		return $result->Value;
	}
	
	function generateTeams(){
		$number = getNumberOfTeams();
		$passwordLength=6;
		$Query = "DELETE FROM team_data;";
		db_Query($Query);
		for($i=1;$i<=$number;$i++){
			$holder=makePassword($passwordLength);
			$Query = "INSERT INTO team_data VALUES ('$i','','$holder','0','0','0','0','0','','');";
			db_Query($Query);
		}
		$Query = "DELETE FROM log;";
		db_Query($Query);
	}
	
	function manualOverride(){
		$teamID=$_REQUEST['teamID'];
		$type=$_REQUEST['type'];
		$category=$_REQUEST['category'];
		$value=$_REQUEST['value'];
		
		$column=null;
		$response = "Function Ran";
		//le gran switch
		switch($category){
			case "teamPassword":
			$column = "Password";
			break;
			
			case "teamNickname":
			$column = "TeamNickname";
			break;
			
			case "level1Points":
			$column = "Points1";
			break;
			
			case "level2Points":
			$column = "Points2";
			break;
			
			case "level3Points":
			$column = "Points3";
			break;
		}
		
		switch($type){
			case "set":
			$Query="UPDATE team_data SET $column='$value' WHERE TeamID='$teamID';";
			db_Query($Query);
			break;
			
			case "add":
			$Query="SELECT $column FROM team_data WHERE TeamID='$teamID';";
			$result = db_Query($Query);
			$result2 = mysqli_fetch_array($result);
			$result2[0]+=$value;
			$Query="UPDATE team_data SET $column='$result2[0]' WHERE TeamID='$teamID';";
			db_Query($Query);			
			break;
			
			case "subtract":
			$Query="SELECT $column FROM team_data WHERE TeamID='$teamID';";
			$result = db_Query($Query);
			$result2 = mysqli_fetch_array($result);
			$result2[0]-=$value;
			$Query="UPDATE team_data SET $column='$result2[0]' WHERE TeamID='$teamID';";
			db_Query($Query);
			break;
		}
		
		return $response;
	}
	
	//RETURNS TEAM DATA FROM THE DATABASE AND UPDATES THE RANK OF TEAMS.
	function getTeamSummary(){
		$rankingStyle = getRankingStyle();
		switch ($rankingStyle){
			case "rankingStyle1":
			$Query = "SELECT TeamID,TeamNickname,Password,Points3,Points2,Points1,FreetimeRank FROM team_data ORDER BY Points3 DESC, lastPointTime DESC;";
			break;
			
			case "rankingStyle2":
			$Query = "SELECT TeamID,TeamNickname,Password,Points3,Points2,Points1,FreetimeRank FROM team_data ORDER BY Points3 DESC, FreetimeRank DESC, lastPointTime DESC;";
			break;
			
			case "rankingStyle3":
			$Query = "SELECT TeamID,TeamNickname,Password,Points3,Points2,Points1,FreetimeRank FROM team_data ORDER BY Points3 DESC, Points2 DESC, Points1 DESC, lastPointTime DESC;";
			break;
		}
		
		//error_log("Query: $Query", 3, "C:\wamp\www\MathRelay2\server\.log");
		$result = db_Query($Query);
		//error_log("result: $result", 3, "C:\wamp\www\MathRelay2\server\.log");
		$response = array();
		while($row = mysqli_fetch_object($result)){
			$response[]=$row;
		}
		
		$size = count($response);
		for($i=0;$i<$size;$i++){
			$carry = $response[$i]->TeamID;
			$rank = $i+1;
			$Query = "UPDATE team_data SET Rank='$rank' WHERE TeamID='$carry';";
			//error_log("Query: $Query\n", 3, "C:\wamp\www\MathRelay2\server\.log");
			db_Query($Query);
		}
		
		return $response;
	}
	
	function rankFreetime(){
		$Query = "SELECT TeamID,Rank FROM team_data ORDER BY Rank ASC;";
		$result = db_Query($Query);
		while($row = mysqli_fetch_object($result)){
			$rank = $row->Rank;
			$teamID = $row->TeamID;
			$Query = "UPDATE team_data SET FreetimeRank='$rank' WHERE TeamID='$teamID';";
			db_Query($Query);
		}
	}
	
	function makePassword($size){
		$chars="ABCDEFGHJKLMNPQRSTUVWXYZ123456789";
		$length=strlen($chars);
		$i=0;
		$out=null;
		while($i<$size){
			$out.=$chars[rand(0,$length)];
			$i++;
		}
		return $out;
	}
	
	function getLog(){
		$Query="SELECT * FROM log ORDER BY Timestamp DESC;";
		$result = db_Query($Query);
		$response = null;
		while($row = mysqli_fetch_object($result)){
			$response[]=$row;
		}
		return $response;
	}
	
	function setNumberOfTeams(){
		$number = $_REQUEST{'numberOfTeams'};
		
		$Query="SELECT * FROM relay_options WHERE Name='numberOfTeams';";
		$result = db_Query($Query);
		if(mysqli_num_rows($result)==0){
			$Query = "INSERT INTO relay_options VALUES ('numberOfTeams','50');";
			db_Query($Query);
		}
		
		$Query = "UPDATE relay_options SET Value='$number' WHERE Name='numberOfTeams';";
		db_Query($Query);
	}
	
	function getNumberOfTeams(){
		$Query="SELECT value FROM relay_options WHERE Name='numberOfTeams';";
		$result = mysqli_fetch_row(db_Query($Query));
		
		return $result[0];
		
	}
	
	function getLoggedInTeams(){
		$Query ="SELECT TeamID,lastCheckTime FROM team_data WHERE lastCheckTime!='NULL';";
		$result = db_Query($Query);
		
		$currentTime = time();
		$response = null;
		
		while($row = mysqli_fetch_object($result)){
			if(($currentTime - $row->lastCheckTime) < 20){
				$response[] = $row->TeamID;
			}
		}
		
		return $response;
	}
	
	function setRankingStyle(){
		$value = $_REQUEST['value'];
		$Query = "UPDATE relay_options SET Value='$value' WHERE Name='rankingStyle';";
		db_Query($Query);
	}
	
	function getRankingStyle(){
		$Query = "SELECT Value FROM relay_options WHERE Name='rankingStyle';";
		$result = mysqli_fetch_object(db_Query($Query));
		return $result->Value;
	}
	function setTimer(){
		$_SESSION['lastTime'] = $_REQUEST['value'];
	}
	
	function getTimer(){
		return $_SESSION['lastTime'];
	}
	
	
	function updateAnswers(){
		$message = null;
		for($i=1;$i<=40;$i++){
			$result3 = mysqli_num_rows(db_Query("SELECT * FROM answer_table WHERE SeriesNumber='$i' AND LevelNumber='3';"));
			$result2 = mysqli_num_rows(db_Query("SELECT * FROM answer_table WHERE SeriesNumber='$i' AND LevelNumber='2';"));
			$result1 = mysqli_num_rows(db_Query("SELECT * FROM answer_table WHERE SeriesNumber='$i' AND LevelNumber='1';"));
			if($result3!=0 && $result2!=0 && $result1!=0){
				if($result3>1 || $result2>1 || $result1>1){
					$x=2;
				} else {
					$x = 1;
				}
			} else if($result3==0 && $result2==0 && $result1==0){
				$x = 0;
			} else {
				$x = 3;
			}
			
			$message[]=$x;
		}
		return $message;
	}
	
	$action=$_REQUEST["action"];
	switch($action){
		
		case "setRelayOption":
		$action = setRelayOption();
		break;
		
		case "getRelayOption":
		$action = getRelayOption();
		break;
		
		case "assignAnswer":
		$action=assignAnswer();
		break;
		
		case "getAnswers":
		$action = getAnswers();
		break;
		
		case "changeAdminPassword":
		$action = changeAdminPassword();
		break;
		
		case "setCleanupParagraph":
		$action=setCleanupParagraph();
		break;
		
		case "setDisplayNumber":
		$action=setDisplayNumber();
		break;
		
		case "getDisplayNumber":
		$action=getDisplayNumber();
		break;
		
		case "setDisplayOptions":
		$action=setDisplayOptions();
		break;
		
		case "getDisplayOptions":
		$action=getDisplayOptions();
		break;
		
		case "getCleanupParagraph":
		$action=getCleanupParagraph();
		break;
		
		case "getEvent":
		$action = getEvent();
		break;
		
		case "getLoggedInTeams":
		$action = getLoggedInTeams();
		break;
		
		case "getTeamSummary":
		$action = getTeamSummary();
		break;
		
		case "changeEvent":
		$action = changeEvent();
		break;
		
		case "getLog":
		$action = getLog();
		break;
		
		case "setNumberOfTeams":
		$action = setNumberOfTeams();
		break;
		
		case "getNumberOfTeams":
		$action = getNumberOfTeams();
		break;
		
		case "manualOverride":
		$action = manualOverride();
		break;
		
		case "rankFreetime":
		$action = rankFreetime();
		break;
		
		case "setRankingStyle":
		$action = setRankingStyle();
		break;
		
		case "getRankingStyle":
		$action = getRankingStyle();
		break;
		
		case "setTimer":
		$action = setTimer();
		break;
		
		case "getTimer":
		$action = getTimer();
		break;
		
		case "updateAnswers":
		$action = updateAnswers();
		break;

	}
	print json_encode($action);
?>