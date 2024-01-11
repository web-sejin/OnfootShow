<?php
	include_once("_common.php");

	$tb = "a_schedule_res";
	$tb2 = "a_schedule_res_time";
	$scdDate = str_replace(". ", "-", $scd_date);	

	$sql_fs = " select * from a_stadium A, g5_member B where A.mb_idx = B.mb_no and as_idx = '{$as_idx}' ";
	$futsal = sql_fetch($sql_fs);

	$common = " as_idx = '{$as_idx}'
								, scd_date = '{$scdDate}'
								, scd_start = '{$scd_start}'
								, scd_end = '{$scd_end}'
								, scd_match_type = '{$scd_match_type}'
								, scd_memo = '{$scd_memo}'
								, scd_team_name = '{$scd_team_name}'
								, scd_res_type = '{$scd_res_type}'
								, scd_res_cert = 1
								, scd_price = '{$futsal['as_price']}'
								";
	
	$add_query = "";	
	
	if($w == ""){
		//예약 중에 먼저 예약된 경우 체크
		$resCnt = 0;
		for($i=$scd_start; $i<=$scd_end; $i++){
			$sql = " select count(*) cnt from {$tb2} where as_idx = '{$as_idx}' and scdt_date = '{$scdDate}' and scdt_time = '{$i}' and delete_state = 1 ";
			$row = sql_fetch($sql);
			if($row['cnt'] > 0){
				$resCnt = $resCnt+1;
			}
		}

		if($resCnt > 0){
			echo json_encode(array('result_code'=>'0000', 'message'=>'예약중에 다른 회원이 예약을 했습니다.'));		
		}else{
			if($scd_match_type == "1"){
				$add_query .= " , scd_match_sort = '{$scd_match_sort}' 
													, scd_name = '{$scd_name}'
													, scd_hp = '{$scd_hp}'
													";

			}else if($scd_match_type == "2"){
				$add_query .= " , scd_match_sort = '{$scd_match_sort}' 
													, scd_name = '{$scd_name}'
													, scd_hp = '{$scd_hp}'
													, scd_match_bet = '{$scd_match_bet}' 
													, scd_match_level = '{$scd_match_level}'
													, scd_match_age = '{$scd_match_age}'
													, scd_match_gender = '{$scd_match_gender}'
													, scd_match_to = '{$scd_match_to}'													
													";
			}

			if($scd_match_type != "3" && $scd_match_sort == "2"){		
				$add_query .= " , atf_idx = '{$scd_match_team_idx}' ";
			}

			$sql = " insert into {$tb} set 
							{$common}
							{$add_query}
							, mb_idx = '{$member['mb_no']}'
							, scd_datetime = now()
							";
			sql_query($sql);
			$scd_idx = sql_insert_id();
			
			for($i=$scd_start; $i<=$scd_end; $i++){
				$sql = " insert into {$tb2} set
								scd_idx = '{$scd_idx}'
								, as_idx = '{$as_idx}'
								, scdt_date = '{$scdDate}'
								, scdt_time = {$i}
								";
				sql_query($sql);
			}

			if($scd_match_type == "2"){				
				if($scd_match_sort == "2"){
					$match_add = " , at_idx = '{$scd_match_team_idx}' ";
				}

				$sql_match = " insert into a_match set 
							mb_idx = '{$member['mb_no']}'							
							, scd_idx = '{$scd_idx}'
							, am_name = '{$member['mb_name']}'
							, am_hp = '{$member['mb_idx']}'
							, am_team_name = '{$scd_team_name}'
							, am_level = '{$scd_match_level}'
							, sd_idx = '{$futsal['sd_idx']}'
							, si_idx = '{$futsal['si_idx']}'
							, do_idx = '{$futsal['do_idx']}'
							, am_date = '{$scdDate}'
							, am_time = '{$scd_start}'
							, am_area = '2'
							, am_to = '{$scd_match_to}'
							, am_bet = '{$scd_match_bet}'
							, am_age = '{$scd_match_age}'
							, am_gender = '{$scd_match_gender}'
							, fs_mb_idx1 = '{$member['mb_no']}'
							, fs_mb_name1 = '{$member['mb_fs_name']}'
							, am_datetime =  now()
							, res_st = 3
							, lat = '{$member['mb_fs_lat']}'
							, lng = '{$member['mb_fs_lng']}'
							{$match_add}
							";
				sql_query($sql_match);
			}

			echo json_encode(array('result_code'=>'1111', 'message'=>''));
		}

	}else if($w == "u"){
		if($scd_match_type == "1"){
			$add_query .= " , scd_match_sort = '{$scd_match_sort}' 
												, scd_name = '{$scd_name}'
												, scd_hp = '{$scd_hp}'
												, scd_match_bet = NULL
												, scd_match_level = NULL
												, scd_match_age = NULL
												, scd_match_gender = NULL
												, scd_match_to = NULL
												";

			$sql_match = " update a_match set delete_st = 0, delete_datetime = now() where scd_idx = '{$scd_idx}' ";
			sql_query($sql_match);

		}else if($scd_match_type == "2"){
			$add_query .= " , scd_match_sort = '{$scd_match_sort}' 
												, scd_name = '{$scd_name}'
												, scd_hp = '{$scd_hp}'
												, scd_match_bet = '{$scd_match_bet}' 
												, scd_match_level = '{$scd_match_level}'
												, scd_match_age = '{$scd_match_age}'
												, scd_match_gender = '{$scd_match_gender}'
												, scd_match_to = '{$scd_match_to}'
												";
				
			$sql_match = " update a_match set 
							am_team_name = '{$scd_team_name}'
							, am_level = '{$scd_match_level}'
							, sd_idx = '{$futsal['sd_idx']}'
							, si_idx = '{$futsal['si_idx']}'
							, do_idx = '{$futsal['do_idx']}'
							, am_date = '{$scdDate}'
							, am_time = '{$scd_start}'
							, am_to = '{$scd_match_to}'
							, am_bet = '{$scd_match_bet}'
							, am_age = '{$scd_match_age}'
							, am_gender = '{$scd_match_gender}'
							, fs_mb_idx1 = '{$member['mb_no']}'
							, fs_mb_name1 = '{$member['mb_fs_name']}'
							, lat = '{$member['mb_fs_lat']}'
							, lng = '{$member['mb_fs_lng']}'
							, at_idx = '{$scd_match_team_idx}'
							where scd_idx = '{$scd_idx}'
							";
			sql_query($sql_match);

		}else if($scd_match_type == "3"){
			$add_query .= "  , scd_match_sort = NULL
												, scd_name = NULL
												, scd_hp = NULL
												, scd_match_bet = NULL
												, scd_match_level = NULL
												, scd_match_age = NULL
												, scd_match_gender = NULL
												, scd_match_to = NULL
												";
			
			$sql_match = " update a_match set delete_st = 0, delete_datetime = now() where scd_idx = '{$scd_idx}' ";
			sql_query($sql_match);
		}

		if($scd_match_type != "3" && $scd_match_sort == "2"){		
			$add_query .= " , atf_idx = '{$scd_match_team_idx}' ";
		}else{
			$add_query .= " , atf_idx = NULL ";
		}

		if($scd_vs_team_idx){
			$sql = " update a_other_team set 
						other_memo = '{$other_memo}'		
						, other_updatetime = now()
						where other_idx = '{$scd_vs_team_idx}'
						";
			sql_query($sql);
		}
		
		if($disabled == "disabled"){
			$sql = " update {$tb} set 
							scd_memo = '{$scd_memo}'																						
							, scd_updatetime = now()
							where scd_idx = '{$scd_idx}'
							";
			sql_query($sql);
			
			//echo json_encode(array('result_code'=>'1112', 'message'=>'수정이 완료되었습니다.'));
		}else{
			$sql = " update {$tb} set 
							{$common}
							{$add_query}																								
							, scd_updatetime = now()
							where scd_idx = '{$scd_idx}'
							";
			sql_query($sql);

			if($other_idx){
				$sql2 = " select * from a_schedule_res where scd_idx = '{$other_idx}' ";
				$row2 = sql_fetch($sql2);

				$sql3 = " insert into a_other_team set 
								scd_idx = '{$scd_idx}'
								, other_sort = '{$row2['scd_match_sort']}'
								, other_name = '{$row2['scd_name']}'
								, other_hp = '{$row2['scd_hp']}'
								, other_team_name = '{$row2['scd_team_name']}'
								, other_level = '{$row2['scd_match_level']}'
								, other_age = '{$row2['scd_match_age']}'
								, other_gender = '{$row2['scd_match_gender']}'
								, other_memo = '{$row2['scd_memo']}'
								, other_datetime = now()
								";
				sql_query($sql3);
				$ot_idx = sql_insert_id();

				$add_query2 = "";
				if($other_mb_idx){ 
					$add_query2 .= " , scd_vs_team_mb_idx = '{$other_mb_idx}' "; 
					$add_query2 .= " , scd_vs_team_at_idx = '{$other_at_idx}' ";
				}

				$sql = " update {$tb} set 
								scd_other_schedule_idx = '{$other_idx}'
								, scd_state = 1
								, scd_state2 = 1
								, scd_join_datetime = now()
								, scd_vs_team_idx = '{$ot_idx}'
								{$add_query2}
								where scd_idx = '{$scd_idx}'
								";
				sql_query($sql);
			
				if($other_mb_idx){
					$sql = " update a_match set
									mb_vs_idx = '{$other_mb_idx}'
									, at_vs_idx = '{$other_at_idx}'
									where scd_idx = '{$scd_idx}'
									";
					sql_query($sql);
				}

				$sql = " update {$tb} set
								delete_state = 0
								where scd_idx = '{$other_idx}'
								";
				sql_query($sql);
			}

			$sql = " delete from {$tb2} where scd_idx = '{$scd_idx}' ";
			sql_query($sql);
			
			for($i=$scd_start; $i<=$scd_end; $i++){
				$sql = " insert into {$tb2} set
								scd_idx = '{$scd_idx}'
								, as_idx = '{$as_idx}'
								, scdt_date = '{$scdDate}'
								, scdt_time = {$i}
								";
				sql_query($sql);
			}
		}
		
		if($scd_vs_team_idx){
			echo json_encode(array('result_code'=>'1112', 'message'=>'', 'sql'=>$sql_match));	
		}else{
			echo json_encode(array('result_code'=>'1111', 'message'=>'', 'sql'=>$sql_match));
		}
	}
?>