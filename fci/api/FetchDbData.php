<?php
require('../util/Connection.php');
require('../structures/District.php');
require('../util/SessionFunction.php');
require('../structures/Login.php');

if(!SessionCheck()){
	return;
}

$reviewed = "";
$approved = "";
$from_id = "";
$to_id = "";

if(isset($_POST['fromid'])){
	$from_id = $_POST['fromid'];
}

if(isset($_POST['toid'])){
	$to_id = $_POST['toid'];
}

if(isset($_POST['approved'])){
	$approved = $_POST['approved'];
}

if(isset($_POST['reviewed'])){
	$reviewed = $_POST['reviewed'];
}

$month = $_POST['month'];

$district = isset($_POST['district']) ? $_POST['district'] : "";

if($district=="all"){
    $district="";
}

$parts = explode('_', $month);

$month = $parts[0];
$year = $parts[1]; 

$query = "SELECT * FROM optimised_table WHERE month='$month' AND year='$year'";
$result = mysqli_query($con,$query);

$numrow = mysqli_num_rows($result);

$id = "";

if($numrow>0){
	$row = mysqli_fetch_assoc($result);
	$id = $row['id'];
}

$tablename = "optimiseddata_".$id;

$query = "SHOW TABLES LIKE '$tablename'";
$result = $con->query($query);

$data = null;

if ($result && $result->num_rows > 0) {

	$conditions = array();

	if($district!=""){
		$conditions[] = "to_district='$district'";
	}

	if($reviewed=="reviewed"){
		$conditions[] = "approve_district='yes'";
	}
	else if($reviewed=="notreviewed"){
		$conditions[] = "(approve_district='' OR approve_district IS NULL)";
	}

	if($approved=="approved"){
		$conditions[] = "approve_admin='yes'";
	}
	else if($approved=="notapproved"){
		$conditions[] = "(approve_admin='no' OR approve_admin IS NULL)";
	}

	if($from_id!=""){
		$conditions[] = "from_id='$from_id'";
	}

	if($to_id!=""){
		$conditions[] = "`to`='$to_id'";
	}

	$query = "SELECT * FROM ".$tablename;

	if(count($conditions)>0){
		$query .= " WHERE ".implode(" AND ",$conditions);
	}

	$result = mysqli_query($con,$query);

	while($row = mysqli_fetch_assoc($result)){
		$data[] = $row;
	}

	$warehouse = array();

	$query_warehouse = "SELECT id FROM warehouse WHERE active='1'";
	$result_warehouse = mysqli_query($con,$query_warehouse);

	while($row_warehouse = mysqli_fetch_assoc($result_warehouse)){
		$warehouse[] = $row_warehouse;
	}

	$resultarray = array();

	if($data==null){
		$data = array();
	}

	$resultarray["data"] = $data;
	$resultarray["warehouse"] = $warehouse;

	echo json_encode($resultarray);

}
else {

	$resultarray = array();

	$resultarray["data"] = array();
	$resultarray["warehouse"] = array();

	echo json_encode($resultarray);

}
?>