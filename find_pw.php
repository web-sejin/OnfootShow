<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");
?>

<div class="find_info_area cm_padd2">
	<form name="find_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="find_type" value="id" readonly>
		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">아이디</p>
				<div class="regi_td">
					<input type="text" name="mb_id" id="mb_id" class="regi_ipt req_ipt" placeholder="아이디를 입력해 주세요.">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">이메일</p>
				<div class="regi_td">
					<input type="email" name="mb_email" id="mb_email" class="regi_ipt req_ipt" placeholder="이메일을 입력하세요.">
				</div>
				<p class="regi_alert">* 가입 시 작성한 이메일을 입력해주세요.</p>
			</li>
		</ul>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button" onClick="find_info();">비밀번호 찾기</button>
		</div>
	</form>
</div>
<div class="fix_btn_back"></div>

<script>
function find_info(){
	const id = document.getElementById("mb_id");
	const email = document.getElementById("mb_email");

	if(id.value == ""){ showToast("아이디를 입력해 주세요."); return false; }
	var emailChk = /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
	if(email.value == ""){ showToast("이메일을 입력해 주세요."); remove_active(); return false; }
	if(email.value.match(emailChk) == null){ showToast("이메일을 정확하게 입력해 주세요."); remove_active(); return false; }
	
	$(".fix_btn").attr("disabled", true);
	$(".indicator").fadeIn();
	setTimeout(function(){
		var string = $("form[name=find_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "/inc/find_pw.php",
			data: string,
			dataType : "json",
			cache: false,
			async: false,
			success: function(data) {
				$(".indicator").fadeOut();
				$(".fix_btn").attr("disabled", false);
				if(data.result_code == "1111"){
					showToast("입력하신 이메일로 임시 비밀번호를 발송했습니다.<br>이메일을 받지 못했다면 문의해 주세요.");
				}else{
					showToast("일치하는 정보가 없습니다.<br>다시 확인해 주세요.");
					remove_active();
					return false;
				}
			}
		});
	},1000);
	return false
}

function fnValueCount(){
	let reqCnt = $(".req_ipt").length;
	let reqCurCnt = 0;
	$(".req_ipt").each(function(){
		if($(this).val() != ""){
			reqCurCnt++;
		}
	});
	
	//console.log(reqCurCnt+"//"+reqCnt);

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

<?php
	include_once(G5_PATH."/_tail.php");
?>