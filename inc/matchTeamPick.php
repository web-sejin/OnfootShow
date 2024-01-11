<?php
	include_once("_common.php");
	//구장 예약을 통한 매치 확정

	$sql = " update a_schedule_res set
					scd_state = 1
					, scd_join_datetime = now()
					, scd_vs_team_mb_idx = '{$mb_idx}'
					, scd_vs_team_at_idx = '{$at_idx}'
					where scd_idx = '{$scd_idx}'
					";
	sql_query($sql);

	$sql = " update a_match set
					mb_vs_idx = '{$mb_idx}'
					, at_vs_idx = '{$at_idx}'
					where am_idx = '{$am_idx}'
					";
	sql_query($sql);

	$sql = " select * from a_match A, a_schedule_res B where A.scd_idx = B.scd_idx and am_idx = '{$am_idx}' ";
	$row = sql_fetch($sql);

	$datetime = str_replace("-", ". ", $row['scd_date'])." (".getYoil($row['scd_date']).") ".sprintf('%02d', $row['scd_start']).":00 ~ ".sprintf('%02d', $row['scd_end']).":00";
	$content = "신청한 매치가 확정되었습니다.";
	$content2 = "신청하신 ".$row['fs_mb_name1']." ".$datetime." 매치가 확정되었습니다.";

	$sql = " insert into a_alim_user set
					mb_idx = '{$mb_idx}'
					, at_idx = '{$at_idx}'
					, am_idx = '{$am_idx}'
					, scd_idx = '{$scd_idx}'
					, alim_type = 2
					, alim_content = '{$content}'
					, alim_content2 = '{$content2}'
					";
	sql_query($sql);

	//푸시 작업
?>