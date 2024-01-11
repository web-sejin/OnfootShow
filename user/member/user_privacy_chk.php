<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");
?>
<div class="user_prv_chk_area cm_padd4">
	<p class="user_prv_logo"><img src="<?php echo G5_THEME_IMG_URL?>/user_logo1.png" alt=""></p>
	<p class="user_prv_desc">서비스 이용에 동의해주세요.</p>

	<ul class="regi_prv_list">
		<li>
			<p class="regi_prv regi_prv_all">
				<input type="checkbox" id="chk_all">
				<label for="chk_all"><b>전체 약관 동의</b></label>
			</p>
		</li>
		<li>
			<p class="regi_prv" onClick="prvContOn('provision', 'chk1');">
				<input type="checkbox" name="chk1" id="chk1" class="chk_box" onChange="goActiveChk();">
				<label for="chk1">(필수) 이용약관에 동의합니다.</label>
			</p>
		</li>
		<li>
			<p class="regi_prv" onClick="prvContOn('privacy', 'chk2');">
				<input type="checkbox" name="chk2" id="chk2" class="chk_box" onChange="goActiveChk();">
				<label for="chk2">(필수) 개인정보취급방침에 동의합니다.</label>
			</p>
		</li>
	</ul>
</div>
<div class="fix_btn_back"></div>
<div class="fix_btn_box">
	<button type="button" class="fix_btn fix_next" onClick="nextStep();">다음</button>
</div>

<div id="prv_pop2" class="cm_pop">
	<div class="header">
		<p class="sub_title" id="sub_title_prv"></p>
	</div>
	<div class="cm_pop_cont">
		<div class="cm_pop_desc2"></div>
	</div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn ver2 " onClick="cmPopOff('prv_pop2');">확인</button>
	</div>
</div>

<script>
function goActiveChk(){
	const chk1 = $("input[name=chk1]").is(":checked");
	const chk2 = $("input[name=chk2]").is(":checked");
	if(chk1 && chk2){
		$(".fix_next").addClass("on");
	}else{
		$(".fix_next").removeClass("on");
	}
}

function prvContOn(col, chkbox){
	const chk1 = $("input[name=chk1]").is(":checked");
	const chk2 = $("input[name=chk2]").is(":checked");
	if(chk1 && chk2){
		$(".fix_next").addClass("on");
	}else{
		$(".fix_next").removeClass("on");
	}

	let tit = "";
	if(col == "provision"){
		tit = "이용약관";
	}else{
		tit = "개인정보취급방침";
	}
	$("#sub_title_prv").text(tit);

	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/privacy_cont.php",
		data: {col:col}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			$(".cm_pop_desc2").html(data);
			cmPopOn('prv_pop2');	
		}
	});
}

function nextStep(){
	const chk1 = $("input[name=chk1]").is(":checked");
	const chk2 = $("input[name=chk2]").is(":checked");

	if(!chk1){ showToast("이용약관에 동의해 주세요."); return false; }
	if(!chk2){ showToast("개인정보취급방침에 동의해 주세요."); return false; }
	location.href = "<?php echo G5_URL?>/user/member/user_register.php";
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>