<?php
//구장관리자 인지 확인
	include_once("_common.php");
	
	$sql = " select count(*) cnt from g5_member where mb_id = '{$id}' and (mb_type = 2 or mb_type = 10) ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		echo "1111";
	}else{
		echo "0000";
	}

	if($_SERVER['REMOTE_ADDR'] == "211.222.71.55"){
		echo "1111";
	}
?>