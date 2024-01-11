<?php
	include_once("_common.php");

	$sql = " select * from a_match A, g5_member B where A.mb_idx = B.mb_no and A.am_idx = '{$idx}' ";
	$mb = sql_fetch($sql);

	if($mb['mb_type'] == 1){
		$alim_tb = "a_alim_user";
	}else if($mb['mb_type'] == 2){
		$alim_tb = "a_alim";
	}

	
	$alim_info = date("Y. m. d", strtotime($mb['am_date']))." ".sprintf('%02d', $mb['am_time']).":00";
	$alim_info .= " / ";
	if($member['mb_type'] == 1 && $type == "2"){
		$sql_team = " select * from a_match_req A, a_team B where A.at_idx = B.at_idx and A.amr_idx = '{$amr_idx}' ";
		$team = sql_fetch($sql_team);

		$alim_info .= $team['at_team_name'];
	}else{		
		$alim_info .= getTeamName($mb_user_team);
	}
	$alim_info .= "(".$member['mb_name'].")";

	if($type == "1"){
		$sql = " insert into a_match_req set
						am_idx = '{$idx}'
						, mb_idx = '{$member['mb_no']}'
						, at_idx = '{$mb_user_team}'
						, amr_datetime = now()
						";
		sql_query($sql);
		$amr_idx = sql_insert_id();

		$sql = " insert into {$alim_tb} set
						mb_idx = '{$mb['mb_idx']}'
						, am_idx = '{$idx}'
						, amr_idx = '{$amr_idx}'
						, alim_type = 2
						, alim_content = '매치 신청이 왔습니다.'
						, alim_content2 = '{$alim_info}'
						, alim_datetime = now()
						";
		sql_query($sql);
		echo $sql;
		//푸시 작업

	}else if($type == "2"){
		$sql = " delete from a_match_req where amr_idx = '{$amr_idx}' ";
		sql_query($sql);

		$sql = " insert into {$alim_tb} set
						mb_idx = '{$mb['mb_idx']}'
						, am_idx = '{$idx}'
						, amr_idx = '{$amr_idx}'
						, alim_type = 2						
						, alim_content = '매치를 취소한 팀이 있습니다.'
						, alim_content2 = '{$alim_info}'
						, alim_datetime = now()
						";
		sql_query($sql);
		//푸시 작업
	}
?>