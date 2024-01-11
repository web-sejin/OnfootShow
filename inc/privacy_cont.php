<?php
	include_once("_common.php");

	$sql = " select * from a_privacy where 1 ";
	$row = sql_fetch($sql);

	if($col == "provision"){
		echo $row['ap_provision'];
	}else if($col == "privacy"){
		echo $row['ap_privacy'];
	}
?>