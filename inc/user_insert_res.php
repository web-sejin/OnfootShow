<?php
	include_once("_common.php");

	$tb = "a_schedule_res";
	$tb2 = "a_schedule_res_time";

	//예약되진 않았는지 체크
	$sql = " select count(*) cnt from {$tb2} A, {$tb} B
					where 1
						and A.scd_idx = B.scd_idx
						and A.as_idx = '{$as_idx}'						
						and A.scdt_date = '{$scd_date}'
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
		$add_query = "";
		$common = " as_idx = '{$as_idx}'
									, scd_date = '{$scd_date}'
									, scd_start = '{$scd_start}'
									, scd_end = '{$scd_end}'
									, scd_match_type = '{$scd_match_type}'
									, scd_res_type = '{$scd_res_type}'
									, scd_match_sort = 1
									, scd_name = '{$member['mb_name']}'
									, scd_hp = '{$member['mb_hp']}'
									, scd_price = '{$scd_price}'
									";

		if($scd_match_type == "2"){
			$exp = explode("||", $match_team);
			$add_query .= " ,scd_match_level = '{$scd_match_level}' 
												 ,scd_match_to = '{$scd_match_to}' 
												 , scd_match_bet = '{$scd_match_bet}'
												 , scd_match_age = '{$scd_match_age}'
												 , scd_match_gender = '{$scd_match_gender}'
												 , scd_team_name = '{$exp[1]}'
												 , scd_team_idx = '{$exp[0]}'
												";			
		}

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
							, scdt_date = '{$scd_date}'
							, scdt_time = {$i}
							";
			sql_query($sql);
		}

		$info_content = "";
		$info_content .= date("Y. m. d", strtotime($scd_date))." ".sprintf('%02d', $scd_start).":00"." ~ ".sprintf('%02d', $scd_end+1).":00";
		$info_content .= " / ";
		$info_content .= $fs_name;
		$info_content .= " / ";
		if($scd_match_type == "1"){
			$info_content .= "자체";
		}else{
			$info_content .= "매치";
		}

		$sql = " insert into a_alim set
						mb_idx = '{$v1}'
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