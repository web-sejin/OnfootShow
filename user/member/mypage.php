<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");
?>

<div class="mypage_area cm_padd5">
	<div class="mypage_intro">
		<p>안녕하세요</p>
		<p><?php echo $member['mb_name']?>님</p>
	</div>
	<ul class="mypage_list">
		<li><a href="<?php echo G5_URL?>/user/member/user_register.php?w=u">내 정보 수정</a></li>
		<li><a href="<?php echo G5_URL?>/user/member/user_match_score.php">내 매치 전적</a></li>
		<li><a href="<?php echo G5_URL?>/user/member/user_res_list.php">구장예약 내역</a></li>
		<li><a href="<?php echo G5_URL?>/user/board/qna_list.php">1:1 문의</a></li>
		<li><a href="<?php echo G5_URL?>/user/privacy.php">개인정보처리방침</a></li>
		<li><a href="<?php echo G5_URL?>/user/provision.php">이용약관</a></li>
		<li><a href="<?php echo G5_BBS_URL?>/logout.php">로그아웃</a></li>
	</ul>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>