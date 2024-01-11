<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	if($is_member){
		$lat = $member['mb_lat'];
		$lng = $member['mb_lng'];
	}else{
		$lat = $_SESSION['lat'];
		$lng = $_SESSION['lng'];
	}
?>
<div class="regi_area cm_padd4">
	<form name="regi_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
		<input type="hidden" name="app_chk" id="app_chk" value="<?php echo $_SESSION['appChk']?>" readonly>
		<input type="hidden" name="mb_token" id="mb_token" value="<?php echo $_SESSION['appToken']?>" readonly>
		<input type="hidden" id="chk_id" value="<?php if($is_member){?>1<?php }else{?>0<?php }?>" readonly>

		<?php if($is_member){?>
		<input type="hidden" id="base_hp" value="<?php echo $member['mb_hp']?>">
		<?php }?>

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">이름<span>*</span></p>
				<div class="regi_td">
					<input type="text" name="mb_name" id="mb_name" class="regi_ipt req_ipt" placeholder="이름을 입력해 주세요." value="<?php echo $member['mb_name']?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">핸드폰 번호<span>*</span></p>
				<div class="regi_td">
					<input type="tel" name="mb_hp" id="mb_hp" class="regi_ipt req_ipt phone" placeholder="핸드폰 번호를 입력해 주세요." maxlength="13" value="<?php echo $member['mb_hp']?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">주 활동지역<span>*</span></p>
				<div class="regi_td regi_td_flex">
					<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select ver5" onchange="chgSido(this.value); fnValueCount();">
						<option value="">시/도</option>
						<?php
						$sqlS = " select * from rb_sido where 1 order by sd_idx asc ";
						$resultS = sql_query($sqlS);
						$sido_selected = "";
						for($i=0; $sido=sql_fetch_array($resultS); $i++){
							//$sido_selected = ($row['dental_sido'] === $sido['sido_name']) ? "selected" : "";
						?>
						<option value="<?php echo $sido['sd_idx']?>"><?php echo $sido['sd_name']?></option>
						<?php }?>
					</select>
					<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select ver5" onChange="fnValueCount();">
						<option value="">시/구/군</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">소소팀<span class="max_txt">(최대 3팀)</span><span>*</span></p>
				<div class="regi_td regi_td_flex">
					<button type="button" class="regi_ipt regi_button regi_button_1 ver5" onClick="newTeamSelectPop();">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch_fff.svg" alt="">
						<span>팀 검색</span>
					</button>
					<button type="button" class="regi_ipt regi_button regi_button_2 ver5" onClick="newTeamCreatePop();">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_plus.svg" alt="">
						<span>팀 생성</span>
					</button>
				</div>
			</li>
		</ul>
		<div class="user_team_regi <?php if($is_member == "u"){ echo "on";  }?>">
			<ul class="user_team_regi_ul" id="user_team_regi_ul">
				<?php 
					$teamCnt = 0;
					for($j=1; $j<=3; $j++){
					if($member['mb_user_team'.$j]){ 
						$teamCnt++;
						$teamNum = $member['mb_user_team'.$j];
						$sql2 = " select * from a_team A, g5_member B where A.mb_idx = B.mb_no and A.at_idx = '{$teamNum}' ";
						$team = sql_fetch($sql2);

						$hpBack = explode("-", $team['mb_hp']);

						$name_x ='*';
						$name_a = mb_substr($team['mb_name'],0,1,"UTF-8");
						$name_b = mb_substr($team['mb_name'],2,10,"UTF-8");
						$name = $name_a.$name_x.$name_b;
				?>
				<li class="utr_li" data-idx="mb_user_team<?php echo $j?>">
					<input type="hidden" name="team_idx[]" value="<?php echo $team['at_idx']?>">
					<input type="hidden" name="team_type[]" value="1">
					<input type="hidden" name="team_name[]" value="<?php echo $team['at_team_name']?>">
					<p class="user_team_leader">
						<span><?php echo $name?></span>
						<span></span>
						<span><?php echo $hpBack[2]?></span>
					</p>
					<p class="user_team_p ver1">
						<strong>팀명</strong>
						<span><?php echo $team['at_team_name']?></span>
					</p>
					<p class="user_team_p ver2">
						<strong>지역</strong>
						<span><?php echo getSido($team['at_sido'])?> <?php echo getSigugun($team['at_sigugun'])?></span>
					</p>
					<button type="button">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_remove.svg" alt="">
					</button>
				</li>
				<?php }}?>
			</ul>
		</div>

		<?php if($is_member){?>
		<div class="user_leave">
			<ul class="mypage_list">
				<li><a onClick="cmPopOn('leave_confirm_pop');">회원탈퇴</a></li>
			</ul>
		</div>
		<?php }?>

		<div class="fix_btn_back"></div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button" onClick="register();">
				<?php if($is_member){ echo "정보수정"; }else{ echo "회원가입";  }?>
			</button>
		</div>
	</form>
</div>

<div id="new_team_pop2" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="header">
		<button type="button" class="back_btn ver2" onClick="newTeamCancel2();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
		<div class="people_sch_box ver2">
			<input type="text" id="sch_team_val" placeholder="팀명을 검색하세요.">
			<button type="button" onClick="getTeamList();">
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
			</button>
		</div>
	</div>
	<div class="cm_pop_cont">
		<form name="new_team_frm" method="post">
		<ul class="user_team_regi_ul" id="pop_team_list"></ul>
		</form>
	</div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn on" onClick="newTeamSelect();">확인</button>
	</div>
</div>

<div id="new_team_pop" class="cm_pop ver2">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_cont2">
		<p class="cm_pop_cont2_tit">팀 생성</p>
		<input type="text" id="new_team_name" class="cm_pop_cont2_ipt" placeholder="팀명을 입력해주세요.">
		<div class="cm_pop_cont2_btn_box">
			<button type="button" class="cm_pop_cont2_btn" onClick="newTeamCancel();">취소</button>
			<button type="button" class="cm_pop_cont2_btn on" onClick="newTeamCreate();">생성하기</button>
		</div>
	</div>
</div>

<div id="delete_confirm_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">기존에 등록한 팀은 바로 삭제됩니다.<br>삭제하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('delete_confirm_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="del_cofirm_btn" onClick="">확인</button>
		</div>
	</div>
</div>

<div id="leave_confirm_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">탈퇴하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_confirm_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="del_cofirm_btn" onClick="leaveOk();">확인</button>
		</div>
	</div>
</div>

<script>
	$(function(){
		if($("#w").val() == "u"){
			$("#sd_idx").val("<?php echo $member['sd_idx']?>");
			$("#sd_idx").change();
			if("<?php echo $member['si_idx']?>" != ""){
				$("#si_idx").val("<?php echo $member['si_idx']?>");
				$("#si_idx").change();
			}

			if("<?php echo $teamCnt?>" != 0){
				listReset();	
			}
		}
	});

	function chgSido(v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/sigugun_list2.php",
			data: {sd_idx:v},
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#si_idx").empty().append(data);
			}
		});
	}

	function newTeamSelectPop(){
		if($("#user_team_regi_ul .utr_li").length >= 3){
			showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
			return false;
		}
		cmPopOn("new_team_pop2");
	}

	function newTeamSelect(){
		const base_len = $("#user_team_regi_ul .utr_li").length;
		const new_len = $("#pop_team_list .utr_li").length;
		const chk_len = $("input[name='team_pick[]']:checked").length;
		if(new_len < 1){
			showToast("팀명을 검색해 주세요.");
			return false;
		}

		if(chk_len < 1){
			showToast("팀을 선택해 주세요.");
			return false;
		}

		//console.log("base_len :: "+base_len);
		//console.log("chk_len :: "+chk_len);

		if(base_len == 3 || (base_len == 2 && chk_len >= 2) || (base_len == 1 && chk_len >= 3) || chk_len >= 3){
			showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
			return false;
		}
		
		var new_string = $("form[name=new_team_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/newTeamSelect.php",
			data: new_string, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				$("#user_team_regi_ul").append(data);
				newTeamCancel2();
				listReset();
			}
		});
	}

	function newTeamCreatePop(){
		if($("#user_team_regi_ul .utr_li").length >= 3){
			showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
			return false;
		}
		cmPopOn("new_team_pop");
	}

	function newTeamCancel2(){
		cmPopOff("new_team_pop2");
		$("#sch_team_val").val("");
		$("#pop_team_list").empty();
	}

	function getTeamList(){
		if($("#sch_team_val").val() == ""){
			showToast("팀명을 검색해 주세요.");
			return false;
		}

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getTeamList.php",
			data: {v:$("#sch_team_val").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				$("#pop_team_list").empty().append(data);
			}
		});
	}

	function newTeamCancel(){
		cmPopOff("new_team_pop");
		$("#new_team_name").val("");
	}

	function newTeamCreate(){
		if($("#new_team_name").val() == ""){ showToast("팀명을 입력해 주세요."); return false; }

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/newTeamCreate.php",
			data: {teamName:$("#new_team_name").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				newTeamCancel();
				$("#user_team_regi_ul").append(data);
				listReset();
			}
		});
	}

	function teamRemove(v){
		const data_idx = $("#utr_li_"+v).attr("data-idx");
		/*if(data_idx){
			$("#del_cofirm_btn").attr("onClick", `deleteOk('${data_idx}', '${v}');`);
			cmPopOn("delete_confirm_pop");
		}else{
			$("#utr_li_"+v).remove();
			listReset();
		}*/
		$("#utr_li_"+v).remove();
		listReset();
	}

	function listReset(){
		let cnt = 0;
		$("#user_team_regi_ul .utr_li").each(function(i){
			$(this).attr("id", "utr_li_"+i);
			$(this).children("button").attr("onClick", "teamRemove('"+i+"')");;
			cnt++;
		});
		if(cnt > 0){
			$(".user_team_regi").addClass("on");
		}else{
			$(".user_team_regi").removeClass("on");
		}
		fnValueCount();
	}

	function teamListChk(at_idx){
		$("#user_team_regi_ul .utr_li").each(function(){
			const this_idx = $(this).children("input[name='team_idx[]']").val();
			if(this_idx == at_idx){
				$("#team_pick_"+at_idx).prop("checked", false);
				showToast("이미 선택된 팀입니다.");
			}
		})
	}

	function fnValueCount(){
		let reqCnt = ($(".req_ipt").length)+1;
		let reqCurCnt = 0;
		$(".req_ipt").each(function(){
			if($(this).val() != ""){
				reqCurCnt++;
			}
		});

		if($("#user_team_regi_ul .utr_li").length > 0){ reqCurCnt++;  }

		if($("#sd_idx").val() == "36"){
			reqCnt = ($(".req_ipt").length);
		}
		//console.log(reqCurCnt+"//"+reqCnt);

		if(reqCurCnt >= reqCnt){
			$("#submit_button").addClass("on");
		}else{
			$("#submit_button").removeClass("on");
		}
	}

	$(".req_ipt").keyup(function(e) {
		fnValueCount();
	});

	function register(){
		const w = document.getElementById("w");	
		const name = document.getElementById("mb_name");	
		const hp = document.getElementById("mb_hp");
		const sd_idx = document.getElementById("sd_idx");
		const si_idx = document.getElementById("si_idx");
		const teamCnt = $("#user_team_regi_ul .utr_li").length;

		if(name.value == ""){ showToast("이름을 입력해 주세요."); return false; remove_active(); }
		if(hp.value == ""){ showToast("핸드폰 번호를 입력해 주세요."); return false; remove_active(); }
		if((hp.value).length != 13){ showToast("핸드폰 번호를 정확히 입력해 주세요."); remove_active(); return false; }
		let hpStatus = fnidChk(hp.value, "mb_hp", "1");
		if(w.value == "u" && $("#base_hp").val() == hp.value){ hpStatus = true; }
		if(!hpStatus){ showToast("이미 사용중인 핸드폰 번호 입니다."); remove_active(); return false; }
		if(sd_idx.value == ""){ showToast("풋살장 지역(시/도)을 선택해 주세요."); remove_active(); return false; }
		if(sd_idx.value == "36"){ 
		}else{
			if(si_idx.value == ""){ showToast("풋살장 지역(시/구/군)을 선택해 주세요."); remove_active(); return false; }
		}
		if(teamCnt < 1){ showToast("소속팀을 1개 이상 생성해 주세요,"); remove_active(); return false; }
		
		var string = $("form[name=regi_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/user_register_update.php",
			data: string, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				if(w.value == "u"){
					console.log(data);
					showToast("정보수정이 완료되었습니다.");
				}else{
					location.href = "<?php echo G5_URL?>/user";
				}
			}
		});
	}

	function deleteOk(col, v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/user_team_delete.php",
			data: {col:col}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				cmPopOff('delete_confirm_pop');
				$("#utr_li_"+v).remove();
				listReset();
			}
		});
	}

	function leaveOk(){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/member_leave.php",
			data: {}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				location.href = "<?php echo G5_URL?>/user/member/login.php";
			}
		});
	}
</script>
<?php
	include_once(G5_PATH."/_tail.php");
?>