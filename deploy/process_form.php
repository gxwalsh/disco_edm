<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//start session
session_start();



include "include.php";





function getUserIP() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}






 


//get the variables passed
$_SESSION["user_id"] = $_POST["user_id"];


//echo $_SESSION["user_id"];

$nick = $_POST["user_id"];
$name = $_POST["assent_sign"];
$guardian = $_POST["parent_email"];
$ip = getUserIP();

echo $nick;
echo $name;




//put signature time and date in database.

$sql = "INSERT INTO `users` (`name`,`nick`,`ip`,`guardian_email`) VALUES ('$name','$nick','$ip','$guardian');";
echo $sql;

//if it is working then run the DB query

			$conn = new mysqli($servername, $username, $password, $database);


		
			if ($conn->connect_error) {
				$message = $message . "SQL: error on new record.";
				die("Connection failed: " . $conn->connect_error);
			} 


			if ($conn->query($sql) === TRUE) {
				$location = "draw.php?user_id=".$nick."&project_id=20";
				header("Location:".$location); //redirect to project  
			} else {
			
			}







?>