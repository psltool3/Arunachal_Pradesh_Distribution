<?php
require('../util/Connection.php');
require('../structures/District.php');
require('../util/SessionFunction.php');
require('../structures/Login.php');

if(!SessionCheck()){
	//return;
}
$message = "";
if (!isset($_POST['month'])) {
    die(json_encode(["message" => "Missing Month Parameter"]));
}

$month = $_POST['month'];

if (!is_string($month)) {
    die(json_encode(["message" => "Invalid Month Parameter"]));
}

$valid_months = ['jan', 'feb', 'march', 'april', 'may', 'june', 'july', 'aug', 'sept', 'oct', 'nov', 'dec'];
if (!in_array(strtolower($month), $valid_months)) {
    die(json_encode(["message" => "Invalid Month Parameter"]));
}
$month = mysqli_real_escape_string($con, $month);
$query = "SELECT * FROM optimised_table WHERE month='$month'";
$result = mysqli_query($con,$query);
$response = array();
$response_data = array();
while($row = mysqli_fetch_array($result))
{
	$temp = array();
	$temp["year"] = $row["year"];
	$temp["month"] = $row["month"];
	$temp["id"] = $row["id"];
	$temp["applicable"] = $row["applicable"];
	$temp["last_updated"] = $row["last_updated"];
	array_push($response,$temp);
	$query_count = "SELECT * FROM optimiseddata_".$row["id"]." WHERE status<>'implemented' OR status IS NULL";
	$result_count = mysqli_query($con,$query_count);
	$numrows_count = mysqli_num_rows($result_count);
	if($numrows_count!=0){
		$message = "Please implemenetd all tags of leg2 first";
	}
}
if(count($response)==0){
	$message = "First optimized the leg2 for this month";
}
$response_data["data"] = $response;
$response_data["message"] = $message;
echo json_encode($response_data);

?>