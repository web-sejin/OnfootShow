<?php
	include_once('../../common.php');
	//include_once(G5_PATH."/head.sub.php");
	include_once(G5_PATH."/_head.php");
?>
<style>
#sub_div {margin-top:0;}
.inner {padding:0;}
</style>
<div class="user_login_area">
	<p class="user_login_img">
		<img src="<?php echo G5_THEME_IMG_URL?>/user_login_img.png" alt="">
	</p>
	<button type="button" class="user_login_btn" onClick="kakaoRN();">
		<img src="<?php echo G5_THEME_IMG_URL?>/kakao_logo.svg" alt="">
		카카오톡 로그인
	</button>
	<?php //if($app_chk != "1"){?>
	<?php @include_once(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>
	<?php //}?>
</div>

<script>
	function kakaoRN(){
		alert("RN에서 작업!");
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>