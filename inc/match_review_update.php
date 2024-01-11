<?php
	include_once("_common.php");
	
	if($w == "u"){
		if($res_type == "1"){
			//내가 등록한 경기, 내가 신청한 리매치 경기
			$add_query = " scd_result_memo1 = '{$scd_memo}' ";
		}else if($res_type == "2"){
			//내가 신청한 경기, 내가 요청받은 리매치 경기
			$add_query = " scd_result_memo2 = '{$scd_tention}' ";
		}

	}else{
		if($res_type == "1"){
			//내가 등록한 경기, 내가 신청한 리매치 경기
			$add_query = " scd_result1 = '{$scd_result}'
											, scd_result_memo1 = '{$scd_memo}'
											, scd_score2 = '{$scd_score}'
											, scd_tention2 = '{$scd_tention}'
											";
		}else if($res_type == "2"){
			//내가 신청한 경기, 내가 요청받은 리매치 경기
			$add_query = " scd_result2 = '{$scd_result}'
											, scd_result_memo2 = '{$scd_memo}'
											, scd_score1 = '{$scd_score}'
											, scd_tention1 = '{$scd_tention}'
											";
		}		
	}
	
	$sql = " update a_schedule_res set {$add_query} where scd_idx = '{$scd_idx}' ";
	sql_query($sql);
?>