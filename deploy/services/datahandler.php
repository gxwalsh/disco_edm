<?php
include "../include.php";

//message for feedback
$message = "";

$allowed_host = 'digitalwhimsylab.com'; //sets up blocking non sites from getting data like an API
$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

$mode = $_POST['mode'];

$returnData = "";

if(substr($host, 0 - strlen($allowed_host)) == $allowed_host) { //it's allowed


	if ($mode == "add") {
		$message = $message . "mode:add,";
		
		$saveData = $_POST['rawdata'];
		
		$jsonData = json_decode(urldecode($saveData));

		$aJSON = (array)$jsonData;

		$userid = $jsonData->user_id;
		$projectid = $jsonData->project_id;
		$type = $jsonData->type;
		$data = urlencode($aJSON['draw']);

		if(isset($data) && $data != "") {
		$message = $message . "data is set,";
		// Create SQL query
		
		
		
		$sql = "INSERT INTO `drawdata` (`projectid`,`userid`,`draw`) VALUES ('$projectid','$userid','$data');";

		$message = $message . "SQL: " . urlencode($sql);
			//if it is working then run the DB query

			$conn = new mysqli($servername, $username, $password, $database);

			if ($conn->connect_error) {
				$message = $message . "SQL: error on new record.";
				die("Connection failed: " . $conn->connect_error);
			} 


			if ($conn->query($sql) === TRUE) {
				$message = $message . "SQL: New record created successfully.";
			} else {
			
			}
		} else {
			$message = $message . "SQL: No data was sent.";
		}

	} elseif ($mode == "retrieve") {
		$projectid = $_POST["project_id"];

		$sql = "SELECT `userid`,`draw` FROM `drawdata` WHERE `projectid` = '" . $projectid . "';";


		$conn = new mysqli($servername, $username, $password, $database);


			// Check connection
		if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
		} 

		$result = $conn->query($sql);

		$message = $message . "SQL: DB connected.";
		
		$count = 0;
		
		if ($result->num_rows > 0) {
				// output data of each row

			$returnData = "";

			
			while($row = $result->fetch_assoc()) {

				if ($count > 0) { 
					$returnData = $returnData . ',';
				}
				
				$count = $count + 1;
			
				$returnData = $returnData . '{"id":"' . $row["userid"] . '","draw":' .  $row["draw"] . '}';	
		
			}


		

		} else {
			$message = $message . "SQL: No records to retrieve.";
		}


		
	}
 	$conn->close();


} else {
	$message = $message . "FORBIDDEN: No API at this time.";
}

if ($returnData == "") {
	echo '{"msg":"'.$message.'"}';
} else {
	echo '{"data":['. urlencode($returnData) .']}';
}


?>