<?php
	include_once("_common.php");				

	// 회원탈퇴일을 저장
	$sql = " update {$g5['member_table']} set 
					mb_leave_date = '".date("Ymd")."' 
					, mb_password = ''
					, mb_hp_leave = '{$member['mb_hp']}'
					, mb_hp = ''					
					, mb_email_leave = '{$member['mb_email']}'
					, mb_email = ''
					, mb_token = NULL
					, mb_leave_status = 0
					, mb_level = 1
					, mb_homepage = ''
					, mb_tel = ''
					, mb_profile = ''
					, mb_signature = ''
					, mb_memo = '".date('Ymd', G5_SERVER_TIME)." 삭제함\n".sql_real_escape_string($member['mb_memo'])."' 
					where mb_id = '{$member['mb_id']}' 
					";
	sql_query($sql);

	unset($_SESSION['ss_mb_id']);

	if(function_exists('social_member_link_delete')){
		social_member_link_delete($member['mb_id']);
	}
?>