<?php
require('../util/Connection.php');
require('../util/SessionCheck.php');
require('Header.php');

set_time_limit(300);

$query = "SELECT * FROM optimised_table ORDER BY last_updated DESC LIMIT 1";
$result = mysqli_query($con,$query);

$id = "";

while($row = mysqli_fetch_array($result)){
	$id = $row["id"];
}

$tablename = "optimiseddata_".$id;

foreach ($_POST as $key => $value){

	// skip helper fields
	if (substr($key, -11) === '_iddistance' || 
		substr($key, -9) === '_idreason' || 
		$value===""){
		continue;
	}

	$parts = explode("_", $key,3);

	if(count($parts) < 2){
		continue;
	}

	$fromid = $parts[0];
	$toid = $parts[1];

	$toid = str_replace('_','.', $toid);

	// district approve change
	if (substr($key, -8) === '_approve'){

		if($value=="yes"){
			$query = "UPDATE ".$tablename." 
			SET district_change_approve='yes' 
			WHERE from_id='$fromid' 
			AND to_id='$toid'";
		}
		else if($value=="no"){
			$query = "UPDATE ".$tablename." 
			SET district_change_approve='no' 
			WHERE from_id='$fromid' 
			AND to_id='$toid'";
		}

		mysqli_query($con,$query);

		continue;
	}

	// admin approval
	if($value=="yes"){

		$query = "UPDATE ".$tablename." 
		SET approve_admin='yes' 
		WHERE from_id='$fromid' 
		AND to_id='$toid'";

	}
	else if($value=="same"){

		$query = "UPDATE ".$tablename." 
		SET approve_admin='no' 
		WHERE from_id='$fromid' 
		AND to_id='$toid'";

	}
	else if($value=="no"){

		$query = "UPDATE ".$tablename." 
		SET approve_admin='', 
		new_id_admin='' 
		WHERE from_id='$fromid' 
		AND to_id='$toid'";

	}
	else{

		$query_name = "SELECT name FROM warehouse WHERE id='$value'";
		$result_name = mysqli_query($con,$query_name);

		$row_name = mysqli_fetch_assoc($result_name);

		$name = $row_name['name'];

		$reason = isset($_POST[$key."_idreason"]) ? $_POST[$key."_idreason"] : "";

		$distance = isset($_POST[$key."_iddistance"]) ? $_POST[$key."_iddistance"] : "";

		$query = "UPDATE ".$tablename." 
		SET new_id_admin='$value',
		new_name_admin='$name',
		approve_admin='yes',
		new_distance_admin='$distance',
		reason_admin='$reason'
		WHERE from_id='$fromid'
		AND to_id='$toid'";

	}

	mysqli_query($con,$query);

}

mysqli_close($con);

echo "<script>window.location.href='../OptimisedData.php';</script>";

?>

<?php require('Fullui.php'); ?>