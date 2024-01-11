<?php
	include_once("_common.php");

	$tb = "g5_member";
	$tb2 = "a_team";

	$add_insert = "";
	if($app_chk == "1"){
		$add_insert .= " , mb_token = '{$mb_token}' ";
	}

	$mb_id = preg_replace('/\s+/', '', $mb_id);
	$mb_password = md5(pack('V*', rand(), rand(), rand(), rand()));

	if(!$w || $w == ""){
		$sql = " insert into {$tb} set 
						mb_id = '{$mb_id}'
						, mb_password = '".get_encrypt_string($mb_password)."'
						, mb_name = '{$mb_name}'
						, mb_nick = '{$mb_name}'
						, mb_hp = '{$mb_hp}'
						, mb_today_login = '".G5_TIME_YMDHIS."'
						, mb_datetime = '".G5_TIME_YMDHIS."'
						, mb_level = '{$config['cf_register_level']}'
						, mb_ip = '{$_SERVER['REMOTE_ADDR']}'
						, mb_login_ip = '{$_SERVER['REMOTE_ADDR']}'
						, mb_open_date = '".G5_TIME_YMD."'						
						, sd_idx = '{$sd_idx}'
						, si_idx = '{$si_idx}'
						, mb_type = 1
						{$add_insert}
						";
		sql_query($sql);
		$mb_idx = sql_insert_id();
		
		for($i=0; $i<count($team_name); $i++){
			if($team_type[$i] == "1"){
				$at_idx = $team_idx[$i];
			}else if($team_type[$i] == "2"){
				$sql = " insert into {$tb2} set 
								mb_idx = '{$mb_idx}'
								, at_team_name = '{$team_name[$i]}'
								, at_sido = '{$sd_idx}'
								, at_sigugun = '{$si_idx}'
								, at_datetime = now()
								";
				sql_query($sql);
				$at_idx = sql_insert_id();				
			}

			if($i == 0){
				$sql = " update {$tb} set mb_user_team1 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}else if($i == 1){
				$sql = " update {$tb} set mb_user_team2 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}else if($i == 2){
				$sql = " update {$tb} set mb_user_team3 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}
		}

		$mb = get_member($mb_id);
		// 회원아이디 세션 생성
		set_session('ss_mb_id', $mb['mb_id']);
		// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
		set_session('ss_mb_key', md5($mb['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']));

		$key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['SERVER_SOFTWARE'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
		set_cookie('ck_mb_id', $mb['mb_id'], 86400 * 31);
		set_cookie('ck_auto', $key, 86400 * 31);
		
		echo "1111";

	}else if($w == "u") {
		$mb_idx = $member['mb_no'];

		$sql = " update {$tb} set 
						mb_name = '{$mb_name}'
						, mb_nick = '{$mb_name}'
						, mb_hp = '{$mb_hp}'		
						, sd_idx = '{$sd_idx}'
						, si_idx = '{$si_idx}'
						, mb_user_team1 = NULL
						, mb_user_team2 = NULL
						, mb_user_team3 = NULL
						where mb_no = '{$mb_idx}'
						";
		sql_query($sql);

		for($i=0; $i<count($team_name); $i++){
			if($team_type[$i] == "1"){
				$at_idx = $team_idx[$i];
			}else if($team_type[$i] == "2"){
				$sql = " insert into {$tb2} set 
								mb_idx = '{$mb_idx}'
								, at_team_name = '{$team_name[$i]}'
								, at_sido = '{$sd_idx}'
								, at_sigugun = '{$si_idx}'
								, at_datetime = now()
								";
				sql_query($sql);
				$at_idx = sql_insert_id();				
			}

			if($i == 0){
				$sql = " update {$tb} set mb_user_team1 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}else if($i == 1){
				$sql = " update {$tb} set mb_user_team2 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}else if($i == 2){
				$sql = " update {$tb} set mb_user_team3 = '{$at_idx}' where mb_no = '{$mb_idx}' ";
				sql_query($sql);
			}
		}
	}
?>