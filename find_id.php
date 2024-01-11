<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");
?>

<div class="find_info_area cm_padd2">
	<form name="find_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="find_type" value="id" readonly>
		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">이름</p>
				<div class="regi_td">
					<input type="text" name="mb_name" id="mb_name" class="regi_ipt req_ipt" placeholder="이름을 입력해 주세요.">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">핸드폰 번호</p>
				<div class="regi_td">
					<input type="tel" name="mb_hp" id="mb_hp" class="regi_ipt req_ipt phone" placeholder="핸드폰 번호를 입력해 주세요." maxlength="13">
				</div>
				<p class="regi_alert">* 가입 시 작성한 핸드폰 번호를 입력해주세요.</p>
			</li>
		</ul>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button" onClick="find_info();">아이디 찾기</button>
		</div>
	</form>
</div>
<div class="fix_btn_back"></div>

<script>
function find_info(){
	const name = document.getElementById("mb_name");
	const hp = document.getElementById("mb_hp");

	if(name.value == ""){ showToast("이름을 입력해 주세요."); remove_active(); return false; }
	if(hp.value == ""){ showToast("핸드폰 번호를 입력해 주세요."); remove_active(); return false; }
	if((hp.value).length != 13){ showToast("핸드폰 번호를 정확히 입력해 주세요."); remove_active(); return false; }

	var string = $("form[name=find_frm]").serialize();
	$.ajax({
		type: "POST",
		url: "/inc/find_id.php",
		data: string,
		dataType : "json",
		cache: false,
		async: false,
		success: function(data) {
			if(data.result_code == "1111"){
				location.href = "/find_id_result.php?idx="+data.idx;
			}else{
				showToast("일치하는 정보가 없습니다.<br>다시 확인해 주세요.");
				remove_active();
				return false;
			}
		}
	});
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