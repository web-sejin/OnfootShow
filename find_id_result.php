<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	$mb_no = base64_decode($idx);
	$sql = " select * from g5_member where mb_no = '{$mb_no}' ";
	$row = sql_fetch($sql);
?>

<div class="find_info_area cm_padd2">
	<div class="find_result_desc">
		<p class="find_result_txt1"><?php echo $row['mb_id']?></p>
		<p class="find_result_txt2">고객님의 정보와 일치하는 아이디는 다음과 같습니다.</p>
	</div>
	<button type="button" class="find_pw_go" onClick="urlReplace('<?php echo G5_URL?>/find_pw.php');">비밀번호 찾기</button>
	<div class="fix_btn_box">
		<a href="<?php echo G5_BBS_URL?>/login.php" class="fix_btn on">로그인</a>
	</div>
</div>
<div class="fix_btn_back"></div>

<?php
	include_once(G5_PATH."/_tail.php");
?>