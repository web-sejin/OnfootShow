<?php
	include_once("_common.php");	
	include_once(G5_LIB_PATH.'/mailer.lib.php');

	$sql = " select mb_no, count(*) cnt from g5_member where mb_id = '{$mb_id}' and mb_email = '{$mb_email}' ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0){
		$change_password = rand(100000, 999999);
		$mb_lost_certify = get_encrypt_string($change_password);

		// 임시비밀번호와 난수를 mb_lost_certify 필드에 저장
		$sql = " update {$g5['member_table']} set mb_password = '{$mb_lost_certify}' where mb_id = '{$mb_id}' and mb_email = '{$mb_email}' ";
		sql_query($sql);

		//이메일 작업
		$mailContent = '';
		$mailContent .= '<div style="text-align:center;padding:30px;">';
		$mailContent .= '<p><img src="'.G5_THEME_IMG_URL.'/logo.png" alt="" style="width:130px;"></p>';
		$mailContent .= '<p style="font-size:15px;line-height:1.5;word-break:keep-all;margin:30px 0;">온풋 임시비밀번호를 안내해 드립니다.<br>반드시 로그인 후 비밀번호를 변경해 주세요.</p>';
		$mailContent .= '<p style="font-size:20px;line-height:1.1;font-weight:600;">'.$change_password.'</p>';
		$mailContent .= '</div>';

		mailer($config['cf_title']." 온풋 임시 비밀번호", $config['cf_admin_email'], $mb_email, '온풋 임시 비밀번호', $mailContent, 1);

		echo json_encode(array('result_code'=>'1111'));
	}else{
		echo json_encode(array('result_code'=>'0000'));
	}
?>