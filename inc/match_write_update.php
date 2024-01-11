<?php
	include_once("_common.php");

	$at_exp = explode("||", $at_idx);
	
	$common = " 	at_idx = '{$at_exp[0]}'									
									, am_team_name = '{$at_exp[1]}'
									, am_level = '{$am_level}'
									, sd_idx = '{$sd_idx}'
									, si_idx = '{$si_idx}'
									, am_date = '{$am_date}'
									, am_time = '{$am_time}'
									, am_area = '{$am_area}'
									, am_to = '{$am_to}'
									, am_bet = '{$am_bet}'
									, am_age = '{$am_age}'
									, am_gender = '{$am_gender}'
									";

	$add_query = "";

	if($do_idx){ $add_query .= " , do_idx = '{$do_idx}' "; }

	if($do_idx && $do_idx != "all"){
		$sql = " select * from rb_dongli where do_idx = '{$do_idx}' ";				
		$row = sql_fetch($sql);
		$add_query .= " , lat = '{$row['li_lat']}' , lng = '{$row['li_lng']}' ";

	}else if($do_idx == "" || $do_idx == "all") {
		if($am_area == "1"){
			$sql = " select * from rb_sigungu where si_idx = '{$si_idx}' ";	
			$row = sql_fetch($sql);		
			$add_query .= " , lat = '{$row['si_lat']}' , lng = '{$row['si_lng']}' ";
		}
	}

	if($pop_size){ $add_query .= " , pop_size = '{$pop_size}' "; }
	if($pop_sort){ $add_query .= " , pop_sort = '{$pop_sort}' "; }
	if($pop_use1){ $add_query .= " , pop_use1 = '{$pop_use1}' "; }
	if($pop_use2){ $add_query .= " , pop_use2 = '{$pop_use2}' "; }
	if($pop_use3){ $add_query .= " , pop_use3 = '{$pop_use3}' "; }

	if($am_area == "1"){
		
	}else if($am_area == "2"){
		if($fs_idx){
			for($i=0; $i<count($fs_idx); $i++){
				if($i == 0){
					$sql = " select * from g5_member where mb_no = '{$fs_idx[$i]}' ";	
					$row = sql_fetch($sql);		
					$add_query .= " , lat = '{$row['mb_fs_lat']}' , lng = '{$row['mb_fs_lng']}' ";
				}
				$add_query .= " , fs_mb_idx".($i+1)." = '{$fs_idx[$i]}' , fs_mb_name".($i+1)." = '{$fs_idx_name[$i]}' ";
			}
		}
	}

	if($rematch == "1"){
		$add_query .= " , am_rematch = 1, am_rematch_am_idx = '{$rematch_am_idx}' ";
	}  
	
	if(!$w || $w == ""){
		$sql = " insert into a_match set 
						{$common}
						, mb_idx = '{$member['mb_no']}'					
						, am_name = '{$member['mb_name']}'
						, am_hp = '{$member['mb_hp']}'
						, am_datetime =  now()
						{$add_query}
						";
		sql_query($sql);

		echo "1111";
	}else if($w == "u"){
		$add_query .= " , fs_mb_idx".($i+1)." = '{$fs_idx[$i]}' , fs_mb_name".($i+1)." = '{$fs_idx_name[$i]}' ";

		$sql = " update a_match set 
						fs_mb_idx1 = NULL
						, fs_mb_name1 = NULL
						, fs_mb_idx2 = NULL
						, fs_mb_name2 = NULL
						, fs_mb_idx3 = NULL
						, fs_mb_name3 = NULL
						where am_idx = '{$am_idx}'
						";
		sql_query($sql);
		
		$sql = " update a_match set 
						{$common}	
						, am_updatetime =  now()
						{$add_query}
						where am_idx = '{$am_idx}'
						";
		sql_query($sql);
	
		echo "1112";
	}
?>