<?php
	include_once("_common.php");

	$sql = " select count(*) cnt from g5_member where mb_id = '{$id}' and mb_leave_status = 0  ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		echo "0000";
	}else{
		echo "1111";
	}
?>