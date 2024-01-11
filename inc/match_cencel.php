<?php
	include_once("_common.php");

	echo $am_idx."//".$scd_idx;	

	$sql = " select * from a_match A, a_schedule_res B where A.scd_idx = B.scd_idx and am_idx = '{$am_idx}' ";
	$row = sql_fetch($sql);

	$datetime = str_replace("-", ". ", $row['scd_date'])." (".getYoil($row['scd_date']).") ".sprintf('%02d', $row['scd_start']).":00 ~ ".sprintf('%02d', $row['scd_end']).":00";
	$content = "확정됐던 매치가 취소되었습니다.";
	$content2 = "확정됐던 ".$row['fs_mb_name1']." ".$datetime." 매치가 취소되었습니다.";

	$sql = " insert into a_alim_user set
					mb_idx = '{$row['scd_vs_team_mb_idx']}'
					, at_idx = '{$at_idx}'
					, am_idx = '{$am_idx}'
					, scd_idx = '{$scd_idx}'
					, alim_type = 2
					, alim_content = '{$content}'
					, alim_content2 = '{$content2}'
					";
	sql_query($sql);

	$sql = " update a_schedule_res set
					scd_join_datetime = NULL
					, scd_vs_team_mb_idx = NULL
					, scd_vs_team_at_idx = NULL
					, scd_state = 0
					where scd_idx = '{$scd_idx}'
					";
	sql_query($sql);
	
	$add_query = "";
	if($row['res_st'] == 1){
		$add_query = " , scd_idx = NULL ";
	}
	$sql = " update a_match set
					mb_vs_idx = NULL
					, at_vs_idx = NULL
					{$add_query}
					where am_idx = '{$am_idx}'
					";
	sql_query($sql);
?>