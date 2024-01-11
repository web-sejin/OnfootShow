<?php
	include_once("_common.php");

	$tb = "a_schedule_res";
	$tb2 = "a_schedule_res_time";

	$expTime = explode("|", $time_pick_ipt);
	$expLast = count($expTime)-1;
	$scd_start = $expTime[0];
	$scd_end = $expTime[$expLast];

	//예약되진 않았는지 체크
	$sql = " select count(*) cnt from {$tb2} A, {$tb} B
					where 1
						and A.scd_idx = B.scd_idx
						and A.as_idx = '{$as_idx}'						
						and A.scdt_date = '{$date}'
						and A.scdt_time >= {$scd_start}
						and A.scdt_time <= {$scd_end}
						and (B.scd_res_cert = 0 or B.scd_res_cert = 1)
						and B.delete_state = 1
						and A.scd_res_cert = 1
					";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		echo "0000";

	}else{
		$sql_vs = " select * from a_match_req where amr_idx = '{$amr_idx}' ";
		$vs = sql_fetch($sql_vs);

		$add_query = "";
		$common = " as_idx = '{$as_idx}'
									, scd_date = '{$date}'
									, scd_start = '{$scd_start}'
									, scd_end = '{$scd_end}'
									, scd_match_type = '2'
									, scd_res_type = '2'
									, scd_match_sort = 1
									, scd_name = '{$member['mb_name']}'
									, scd_hp = '{$member['mb_hp']}'
									, scd_price = '{$scd_price}'
									, scd_match_level = '{$scd_match_level}' 
									 , scd_match_to = '{$scd_match_to}' 
									 , scd_match_bet = '{$scd_match_bet}'
									 , scd_match_age = '{$scd_match_age}'
									 , scd_match_gender = '{$scd_match_gender}'
									 , scd_team_name = '{$scd_team_name}'
									 , scd_team_idx = '{$scd_team_idx}'									
									 , scd_vs_team_mb_idx = '{$vs['mb_idx']}'
									 , scd_vs_team_at_idx = '{$vs['at_idx']}'
									 , match_idx = '{$am_idx}'						
									";

		$sql = " insert into {$tb} set
						{$common}
						, mb_idx = '{$member['mb_no']}'
						, scd_datetime = now()
						{$add_query}
						";
		sql_query($sql);
		$scd_idx = sql_insert_id();

		for($i=$scd_start; $i<=$scd_end; $i++){
			$sql = " insert into {$tb2} set
							scd_idx = '{$scd_idx}'
							, as_idx = '{$as_idx}'
							, scdt_date = '{$date}'
							, scdt_time = {$i}
							";
			sql_query($sql);
		}		

		$sql = " update a_match set
						mb_vs_idx = '{$vs['mb_idx']}'
						, at_vs_idx = '{$vs['at_idx']}'
						, amr_idx = '{$amr_idx}'
						where am_idx = '{$am_idx}'
						";
		sql_query($sql);

/* 구장관리자가 예약승인할 때로 이동
		$sql = " update a_match_req set
						amr_st= 1
						where amr_idx = '{$amr_idx}'
						";
		sql_query($sql);
		$content = $member['mb_fs_name']." ".date("Y. m. d", strtotime($date))." ".sprintf('%02d', $scd_start).":00 ~".sprintf('%02d', $scd_end+1).":00 ".$scd_team_name." 팀과의 매치가 승인되었습니다.";
		$sql = " insert into a_alim_user set
						mb_idx = '{$vs['mb_idx']}'
						, scd_idx = '{$scd_idx}'
						, am_idx = '{$am_idx}'
						, amr_idx = '{$amr_idx}' 
						, at_idx = '{$vs['at_idx']}'
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

			$content = $member['mb_fs_name']." ".date("Y. m. d", strtotime($date))." ".sprintf('%02d', $scd_start).":00 ~".sprintf('%02d', $scd_end+1).":00 ".$scd_team_name." 팀과의 매치가 거절되었습니다.";
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
*/
		$info_content = "";
		$info_content .= date("Y. m. d", strtotime($date))." ".sprintf('%02d', $scd_start).":00"." ~ ".sprintf('%02d', $scd_end+1).":00";
		$info_content .= " / ";
		$info_content .= $fs_name;
		$info_content .= " / ";
		$info_content .= "매치";

		$sql = " insert into a_alim set
						mb_idx = '{$mb_idx}'
						, scd_idx = '{$scd_idx}'
						, alim_type = 1
						, alim_info = '{$info_content}'
						, alim_content = '구장 예약 신청이 들어왔습니다!'							
						, alim_datetime = now()
						";
		sql_query($sql);

		//푸시작업 해야 함

		echo "1111";
	}
?>