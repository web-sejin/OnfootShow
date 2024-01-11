<?php
	include_once("_common.php");
	
	$scd_idx = base64_decode($idx);

	$sql = " update a_schedule_res set
					delete_state = 0
					where scd_idx = '{$scd_idx}'
					";
	sql_query($sql);

	echo $sql;
?>