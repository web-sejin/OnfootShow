<?php
	include_once("_common.php");	

	$sql = " select * from a_schedule_res where scd_idx = '{$idx}' ";
	$bf = sql_fetch($sql);

	$add_query = "";

	$sql = " update a_schedule_res set
					scd_res_cert = '{$state}'
					, scd_state_datetime = now()
					{$add_query}
					where scd_idx = '{$idx}'
					";
	sql_query($sql);

	$sql = " select * from a_schedule_res where scd_idx = '{$idx}' ";
	$row = sql_fetch($sql);

	$sql2 = " select * from a_stadium A, g5_member B where as_idx = '{$row['as_idx']}' and A.mb_idx = B.mb_no ";
	$row2 = sql_fetch($sql2);
	
	if($row['scd_match_type'] == "2"){	
		if($row['match_idx']){
			if($state == "1"){
				$sql = " update a_schedule_res set
								scd_state = 1
								, scd_join_datetime = now()
								where scd_idx = '{$idx}'
								";
				sql_query($sql);

				$sql = " update a_match set
								scd_idx = '{$idx}'
								where am_idx = '{$row['match_idx']}'
								";
				sql_query($sql);
			}

		}else{
			if($state != "2" && $state != "3"){
				$sql = " insert into a_match set 
								mb_idx = '{$row['mb_idx']}'
								, scd_idx = '{$idx}'
								, at_idx = '{$row['scd_team_idx']}'
								, am_name = '{$row['scd_name']}'
								, am_hp = '{$row['scd_hp']}'
								, am_team_name = '{$row['scd_team_name']}'
								, am_level = '{$row['scd_match_level']}'
								, sd_idx = '{$row2['sd_idx']}'
								, si_idx = '{$row2['si_idx']}'
								, do_idx = '{$row2['do_idx']}'
								, am_date = '{$row['scd_date']}'
								, am_time = '{$row['scd_start']}'
								, am_area = '2'
								, am_to = '{$row['scd_match_to']}'
								, am_bet = '{$row['scd_match_bet']}'
								, am_age = '{$row['scd_match_age']}'
								, am_gender = '{$row['scd_match_gender']}'
								, fs_mb_idx1 = '{$row2['mb_idx']}'
								, fs_mb_name1 = '{$row2['mb_fs_name']}'
								, am_datetime =  now()
								, res_st = 2
								, lat = '{$row2['mb_fs_lat']}'
								, lng = '{$row2['mb_fs_lng']}'
								";
			}
			sql_query($sql);		
		}
	}
	
	if($bf['scd_res_cert'] == 0){
		if($state == "1"){
			$sql = " update a_stadium set
							as_res_cnt = as_res_cnt + 1
							where as_idx = '{$row['as_idx']}'
							";
			sql_query($sql);

			$sql = " update g5_member set
							mb_fs_res_cnt = mb_fs_res_cnt + 1
							where mb_no = '{$row2['mb_idx']}'
							";
			sql_query($sql);			
		}
	}else if($bf['scd_res_cert'] == 1){
		$sql = " update a_stadium set
						as_res_cnt = as_res_cnt - 1
						where as_idx = '{$row['as_idx']}'
						";
		sql_query($sql);

		$sql = " update g5_member set
						mb_fs_res_cnt = mb_fs_res_cnt - 1
						where mb_no = '{$row2['mb_idx']}'
						";
		sql_query($sql);
	}

	$alim_query = "";
	if($row['match_idx']){
		$sql_match = " select * from a_match where am_idx = '{$row['match_idx']}' ";
		$match = sql_fetch($sql_match);

		$alim_query = " , am_idx = '{$row['match_idx']}', amr_idx = '{$match['amr_idx']}' ";
	}

	if($state == "1"){
		$content = "예약하신 ".$row2['mb_fs_name']." ".date("Y.m.d", strtotime($row['scd_date']))." (".getYoil($row['scd_date']).") ".sprintf('%02d', $row['scd_start'])."시 ~ ".sprintf('%02d', $row['scd_end']+1)."시 예약확정 되었습니다.";					

		$sql = " insert into a_alim_user set
						mb_idx = '{$row['mb_idx']}'
						, scd_idx = '{$idx}'
						, alim_type = 1
						, alim_content = '구장 예약 확정되었습니다.'							
						, alim_content2 = '{$content}'
						, alim_datetime = now()
						{$alim_query}
						";
		sql_query($sql);
		
		if($row['match_idx']){
			$sql_vs_team = " select * from a_match A, a_schedule_res B where A.scd_idx = B.scd_idx and am_idx = '{$row['match_idx']}' ";
			$vsTeam = sql_fetch($sql_vs_team);

			$sql = " update a_match_req set amr_st= 1 where amr_idx = '{$vsTeam['amr_idx']}' ";
			sql_query($sql);

			$datetime = str_replace("-", ". ", $vsTeam['scd_date'])." (".getYoil($vsTeam['scd_date']).") ".sprintf('%02d', $vsTeam['scd_start']).":00 ~ ".sprintf('%02d', $vsTeam['scd_end']+1).":00";
			$content = "신청한 매치가 확정되었습니다.";
			$content2 = "신청하신 ".$row2['mb_fs_name']." ".$datetime." 매치가 확정되었습니다.";
			
			$sql_match = " select * from a_match where am_idx = '{$row['match_idx']}' ";
			$match = sql_fetch($sql_match);

			$sql = " insert into a_alim_user set
							mb_idx = '{$match['mb_vs_idx']}'
							, at_idx = '{$row['scd_team_idx']}'
							, am_idx = '{$row['match_idx']}'
							, scd_idx = '{$idx}'
							, alim_type = 2
							, alim_content = '{$content}'
							, alim_content2 = '{$content2}'
							, alim_datetime = now()
							";
			sql_query($sql);
			//푸시 작업

			$sql = " select * from a_match_req where am_idx = '{$row['match_idx']}' and amr_idx != '{$vsTeam['amr_idx']}' ";
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
		}

	}else if($state == "2" || $state == "3"){
		$content = "예약하신 ".$row2['mb_fs_name']." ".date("Y.m.d", strtotime($row['scd_date']))." (".getYoil($row['scd_date']).") ".sprintf('%02d', $row['scd_start'])."시 ~ ".sprintf('%02d', $row['scd_end']+1)."시 예약취소 되었습니다.";			

		$sql = " insert into a_alim_user set
						mb_idx = '{$row['mb_idx']}'
						, scd_idx = '{$idx}'
						, alim_type = 1
						, alim_content = '구장 예약 취소되었습니다.'							
						, alim_content2 = '{$content}'
						, alim_datetime = now()
						{$alim_query}
						";
		sql_query($sql);

		$sql = " insert into a_alim set
						mb_idx = '{$row2['mb_no']}'
						, scd_idx = '{$idx}'
						, alim_type = 1
						, alim_content = '구장 예약 취소되었습니다.'							
						, alim_content2 = '{$content}'
						, alim_datetime = now()
						{$alim_query}
						";
		sql_query($sql);
	}
	
	//푸시작업 해야 함
?>