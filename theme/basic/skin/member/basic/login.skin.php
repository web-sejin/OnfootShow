<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);

include_once(G5_THEME_PATH.'/head.php');
?>

<!-- 로그인 시작 { -->
<div class="login_area cm_padd">	
    <div class="login_box">
		<p class="login_logo"><img src="<?php echo G5_THEME_IMG_URL?>/logo.png" alt=""></p>
        <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
			<input type="hidden" name="url" value="<?php echo $login_url ?>">		
			<input type="hidden" name="app_chk" value="<?php echo $_SESSION['appChk'] ?>">		
			<input type="hidden" name="app_token" value="<?php echo $_SESSION['appToken'] ?>">		
			<ul class="regi_ul">
				<li>
					<p class="regi_th">아이디</p>
					<div class="regi_td">
						<input type="text" name="mb_id" id="login_id" class="regi_ipt req_ipt" placeholder="아이디를 입력해주세요.">
					</div>
				</li>
				<li>
					<p class="regi_th">비밀번호</p>
					<div class="regi_td">
						<input type="password" name="mb_password" id="login_pw" class="regi_ipt req_ipt" placeholder="비밀번호를 입력해주세요.">
					</div>
				</li>
			</ul>
			<div class="login_opt">
                <p class="auto_login">
                    <input type="checkbox" name="auto_login" id="login_auto_login">
                    <label for="login_auto_login"> 자동로그인</label>  
                </p>
                <p class="find_login">
					<a href="<?php echo G5_URL?>/find_id.php">아이디 찾기</a>
					<span>/</span>
					<a href="<?php echo G5_URL?>/find_pw.php">비밀번호 재설정</a>
                </p>
            </div>           		   
		   <div class="fix_btn_box ver2">
				<button type="submit" class="fix_btn" id="submit_button">로그인</button>
			</div>
        </form>

		<div class="not_mb_box">
			<p>아직 회원이 아니세요?</p>
			<a href="<?php echo G5_URL?>/register.php" class="frm_btn ver1">회원가입</a>
		</div>

        <?php //@include_once(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>
    </div>
</div>
<div class="fix_btn_back"></div>

<script>
/*
jQuery(function($){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});
*/

function snsLogin(snsType){
	$(".social_link").attr("disabled", true);
	$(".indicator").fadeIn();

	if (window.ReactNativeWebView) {		
		window.ReactNativeWebView.postMessage(
		  //JSON.stringify({data:"snsLogin", sns:snsType})
			JSON.stringify({data:snsType})
		);
	}
	setTimeout(function(){
		$(".social_link").attr("disabled", false);
		$(".indicator").fadeOut();
	}, 1000);
}

function flogin_submit(f){   
	const id = document.getElementById("login_id");
	const pw = document.getElementById("login_pw");
	const idStatus = fnidChk(id.value, 'mb_id');
	const pwStatus = chkPassword(id.value, pw.value);	
	const leaveChk = chkMbLeave(id.value);	
	const certChk = chkMbCert(id.value);
	const typeChk = chkMbType(id.value);

	if(id.value == ""){ 
		showToast('아이디를 입력해주세요. ');
		remove_active();
		return false; 
	}

	if(idStatus){
		showToast('존재하지 않는 아이디입니다. ');
		remove_active();
		return false; 
	}

	if(!typeChk){
		showToast('구장관리자가 아닙니다. 다시 확인해 주세요.');
		remove_active();
		return false; 
	}

	if(!certChk){ 
		showToast('승인대기 중인 아이디입니다.<br>승인 완료 후 이용해 주세요. ');
		remove_active();
		return false; 
	}

	if(!leaveChk){ 
		showToast('이미 탈퇴한 아이디입니다. ');
		remove_active();
		return false; 
	}

	if(pw.value == ""){ 
		showToast('비밀번호를 입력해주세요.');
		remove_active();
		return false; 
	}

	if(!pwStatus){ 
		showToast('비밀번호가 일치하지 않습니다.');
		remove_active();
		return false; 
	}	
}

function fnValueCount(){
	const reqCnt = $(".req_ipt").length;
	let reqCurCnt = 0;
	$(".req_ipt").each(function(){
		if($(this).val() != ""){
			reqCurCnt++;
		}
	});

	console.log(reqCurCnt+"//"+reqCnt);

	if(reqCurCnt === reqCnt){
		$("#submit_button").addClass("on");
	}else{
		$("#submit_button").removeClass("on");
	}
}

$(".req_ipt").keyup(function(e) {
	fnValueCount();
});
</script>
<!-- } 로그인 끝 -->

<?php
	include_once(G5_THEME_PATH.'/tail.php');
?>