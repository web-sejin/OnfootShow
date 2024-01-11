<?php
	include_once("_common.php");

	$sql = " select * from a_match where am_idx = '{$am_idx}' ";
	$match = sql_fetch($sql);

	$sql = " select * from a_match_req where am_idx = '{$am_idx}' ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$sql2 = " update a_match_req set amr_st= 2 where amr_idx = '{$row['amr_idx']}' ";
		sql_query($sql2);

		$content = $row2['mb_fs_name']." ".date("Y. m. d", strtotime($vsTeam['scd_date']))." ".sprintf('%02d', $vsTeam['scd_start']).":00 ~".sprintf('%02d', $vsTeam['scd_end']+1).":00 ".$row['scd_team_name']." 팀과의 매치가 거절되었습니다.";
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
?>