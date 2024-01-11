<?php
	include_once("_common.php");

	$sql = " select * from g5_member where mb_no = '{$mb_idx}' ";
	$mbAdm = sql_fetch($sql);

	$sql = " select * from a_schedule_res where scd_idx = '{$scd_idx}' ";
	$scd = sql_fetch($sql);

	if($state == "1"){
		$sql = " update a_schedule_res set
						scd_state = 1
						, scd_join_datetime = now()
						, scd_vs_team_mb_idx = '{$mb_idx2}'
						, scd_vs_team_at_idx = '{$at_idx}'
						where scd_idx = '{$scd_idx}'
						";
		sql_query($sql);

		$sql = " update a_match set
						mb_vs_idx = '{$mb_idx2}'
						, at_vs_idx = '{$at_idx}'
						, amr_idx = '{$amr_idx}'
						where am_idx = '{$am_idx}'
						";
		sql_query($sql);

		$sql = " update a_match_req set
						amr_st= 1
						where amr_idx = '{$amr_idx}'
						";
		sql_query($sql);

		$content = $mbAdm['mb_fs_name']." ".$scd['scd_date']." ".sprintf('%02d', $scd['scd_start']).":00 ~".sprintf('%02d', $scd['scd_end']).":00 ".$scd['scd_team_name']." 팀과의 매치가 승인되었습니다.";
		$sql = " insert into a_alim_user set
						mb_idx = '{$mb_idx2}'
						, scd_idx = '{$scd_idx}'
						, am_idx = '{$am_idx}'
						, amr_idx = '{$amr_idx}' 
						, at_idx = '{$at_idx}'
						, alim_type = 2
						, alim_content = '매치신청이 승인되었습니다.'							
						, alim_content2 = '{$content}'
						, alim_datetime = now()
						";
		sql_query($sql);
		//푸시 작업
		
		$sql = " select * from a_match_req where am_idx = '{$am_idx}' and amr_idx != '{$amr_idx}' ";
		$result = sql_query($sql);
		for($i=0; $row=sql_fetch_array($result); $i++){
			$sql2 = " update a_match_req set amr_st= 2 where amr_idx = '{$row['amr_idx']}' ";
			sql_query($sql2);

			$content = $mbAdm['mb_fs_name']." ".$scd['scd_date']." ".sprintf('%02d', $scd['scd_start']).":00 ~".sprintf('%02d', $scd['scd_end']).":00 ".$scd['scd_team_name']." 팀과의 매치가 거절되었습니다.";
			$sql = " insert into a_alim_user set
							mb_idx = '{$row['mb_idx']}'
							, scd_idx = '{$scd_idx}'
							, am_idx = '{$am_idx}'
							, amr_idx = '{$amr_idx}' 
							, at_idx = '{$at_idx}'
							, alim_type = 2
							, alim_content = '매치신청이 거절되었습니다.'							
							, alim_content2 = '{$content}'
							, alim_datetime = now()
							";
			sql_query($sql);
			//푸시 작업
		}
		
	}else if($state == "2"){
		$sql = " update a_match_req set
						amr_st = 2
						where amr_idx = '{$amr_idx}'
						";
		sql_query($sql);

		$sql = " update a_match set
						mb_vs_idx = NULL
						, at_vs_idx = NULL
						, amr_idx = NULL
						where am_idx = '{$am_idx}'
						";
		sql_query($sql);

		$sql = " update a_schedule_res set
						scd_state = 0
						, scd_join_datetime = NULL
						, scd_vs_team_mb_idx = NULL
						, scd_vs_team_at_idx = NULL
						where scd_idx = '{$scd_idx}'
						";
		sql_query($sql);

		$content = $mbAdm['mb_fs_name']." ".$scd['scd_date']." ".sprintf('%02d', $scd['scd_start']).":00 ~".sprintf('%02d', $scd['scd_end']).":00 ".$scd['scd_team_name']." 팀과의 매치가 거절되었습니다.";
		$sql = " insert into a_alim_user set
						mb_idx = '{$mb_idx2}'
						, scd_idx = '{$scd_idx}'
						, am_idx = '{$am_idx}'
						, amr_idx = '{$amr_idx}' 
						, at_idx = '{$at_idx}'
						, alim_type = 2
						, alim_content = '매치신청이 거절되었습니다.'							
						, alim_content2 = '{$content}'
						, alim_datetime = now()
						";
		sql_query($sql);
		//푸시 작업
	}
?>