<?php
include_once('./_common.php');

if(function_exists('social_provider_logout')){
    social_provider_logout();
}

$app_type = $member['mb_type'];
$token = $_SESSION["appToken"];
$app_chk = $_SESSION["appChk"];

//추후 세션으로 일반, 구단관리자 페이지 분기처리 해야 함
if($app_chk == 1){
	$sql= " update {$g5['member_table']} set mb_token = NULL,  mb_login_auto = '0' where mb_id = '{$member['mb_id']}' ";
	sql_query($sql);
}

// 이호경님 제안 코드
session_unset(); // 모든 세션변수를 언레지스터 시켜줌
session_destroy(); // 세션해제함

// 자동로그인 해제 --------------------------------
set_cookie('ck_mb_id', '', 0);
set_cookie('ck_auto', '', 0);
// 자동로그인 해제 end --------------------------------

if ($url) {
    if ( substr($url, 0, 2) == '//' )
        $url = 'http:' . $url;

    $p = @parse_url(urldecode($url));
    /*
        // OpenRediect 취약점관련, PHP 5.3 이하버전에서는 parse_url 버그가 있음 ( Safflower 님 제보 ) 아래 url 예제
        // http://localhost/bbs/logout.php?url=http://sir.kr%23@/
    */
    if (preg_match('/^https?:\/\//i', $url) || $p['scheme'] || $p['host']) {
        alert('url에 도메인을 지정할 수 없습니다.', G5_URL);
    }

    if($url == 'shop')
        $link = G5_SHOP_URL;
    else
        $link = $url;
} else if ($bo_table) {
    $link = get_pretty_url($bo_table);
} else {
    $link = G5_URL;
}

run_event('member_logout', $link);

if($app_chk == 1){
	//추후 세션으로 일반, 구단관리자 페이지 분기처리 해야 함
	goto_url($link."?app_chk=".$app_chk."&app_token=".$token."&app_type=".$app_type);
}else{
	goto_url($link);
}