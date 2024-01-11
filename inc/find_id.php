<?php
	include_once("_common.php");

	$sql = " select mb_no, count(*) cnt from g5_member where mb_name = '{$mb_name}' and mb_hp = '{$mb_hp}' ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		echo json_encode(array('result_code'=>'1111', 'idx'=>base64_encode($row['mb_no'])));
	}else{
		echo json_encode(array('result_code'=>'0000'));
	}
?>