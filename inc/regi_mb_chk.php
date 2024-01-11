<?php
	include_once("_common.php");

	$add_query = "";
	if($mb_type == "1" || $mb_type == "2"){
		$add_query = " and mb_type = '{$mb_type}' ";
	}

	$sql = " select count(*) cnt from g5_member where {$col} = '{$col2}' {$add_query} ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		echo "0000";
	}else{
		echo "1111";
	}
?>