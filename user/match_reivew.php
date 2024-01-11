<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	//경기 종료일 기준 8일 이내에 작성가능
	//8일 이내 작성한 팀만 결과에 반영
	//8일 이후 한 팀만 작성한 경우 작성하지 않은 팀은 패
	//최초 작성 후 수정은 메모만 가능

	$sql = " SELECT * FROM a_match A, a_schedule_res B WHERE A.scd_idx = B.scd_idx AND A.am_idx = '{$am_idx}' AND B.scd_idx = '{$scd_idx}' ";
	$row = sql_fetch($sql);

	$sql2 = " SELECT * FROM a_stadium A, g5_member B WHERE A.mb_idx = B.mb_no AND as_idx = '{$row['as_idx']}' ";
	$row2 = sql_fetch($sql2);
	
	$score = 0;
	if($row['mb_idx'] == $member['mb_no']){
		$result = $row['scd_result1'];
		$score = $row['scd_score2'];
		$tention = $row['scd_tention2'];
		$memo = $row['scd_result_memo1'];
	}else if($row['mb_vs_idx'] == $member['mb_no']){
		$result = $row['scd_result2'];
		$score = $row['scd_score1'];
		$tention = $row['scd_tention1'];
		$memo = $row['scd_result_memo2'];
	}

	if($result == 1){
		$resultText = "승";
	}else if($result == 2){
		$resultText = "무";
	}else if($result == 3){
		$resultText = "패";
	}

	if(!$w && $score != 0){
		goto_url(G5_URL."/user/match_reivew.php?w=u&am_idx=".$am_idx."&scd_idx=".$scd_idx);
	}
	
	$disabled = false;
	$after8 = date("Y-m-d", strtotime($row['am_date']." +8 days"));
	if($after8 >= G5_TIME_YMD){
		//echo "1";
	}
?>

<form name="review_frm" method="post">
	<input type="hidden" name="w" id="w" value="<?php echo $w?>">
	<input type="hidden" name="am_idx" value="<?php echo $am_idx?>">
	<input type="hidden" name="scd_idx" value="<?php echo $scd_idx?>">
	<input type="hidden" name="res_type" value="<?php if($row['mb_idx'] == $member['mb_no']){?>1<?php }else if($row['mb_vs_idx'] == $member['mb_no']){?>2<?php }?>">
	<input type="hidden" name="scd_score" id="scd_score" class="req_ipt" value="<?php if(!$w || $w==""){ echo "3"; }else{ echo $score; }?>">

	<div class="match_review cm_padd4">	
		<div class="mr_info_box">
			<p class="mr_name <?php if($row['res_st'] == 2 || $row['res_st'] == 3){ echo "ver2"; }?>">
				<?php if($row['res_st'] == 2 || $row['res_st'] == 3){?><span>예약</span><?php }?>
				<strong><?php echo getFutsalStadiumName($row['as_idx'])?> (<?php echo getStadiumName($row['as_idx'])?>)</strong>
			</p>
			<p class="mv_date">
				<?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row['scd_start']).":00 ~ ".sprintf('%02d', $row['scd_end']+1).":00"; ?>
			</p>
			<p class="mr_addr">
				<img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt="">
				<span>[<?php echo $row2['mb_fs_zip']?>] <?php echo $row2['mb_fs_addr1']?> <?php echo $row2['mb_fs_addr2']?> <?php echo $row2['mb_fs_addr3']?></span>
			</p>
			<?php if($row['res_st'] == 3){?>
			<p class="mv_adm_mode">관리자 등록</p>
			<p class="mv_adm_alert">* 관리자 등록 경기는 메모만 작성 가능합니다.</p>
			<?php }?>
		</div>

		<div class="mr_team">
			<p class="mr_team_p1">
				<strong><?php echo getTeamName($row['at_idx']);?></strong>
				<?php if($row['mb_idx'] == $member['mb_no']){?>
					<?php if($w == "u"){?>
						<input type="hidden" name="scd_result" id="scd_result" class="req_ipt" readonly value="<?php echo $result?>">
						<input type="text" class="review_select ver2" readonly value="<?php echo $resultText?>">
					<?php }else{?>
						<select name="scd_result" id="scd_result" class="req_ipt review_select" onChange="fnValueCount();">
							<option value="">선택</option>
							<option value="1">승</option>
							<option value="2">무</option>
							<option value="3">패</option>
						</select>
					<?php }?>					
				<?php }?>
			</p>
			<p class="mr_team_p2"><strong>vs</strong></p>
			<p class="mr_team_p3">
				<?php if($row['mb_vs_idx'] == $member['mb_no']){?>
					<?php if($w == "u"){?>
						<input type="hidden" name="scd_result" id="scd_result" class="req_ipt" readonly value="<?php echo $result?>">
						<input type="text" class="review_select ver2" readonly value="<?php echo $resultText?>">
					<?php }else{?>
						<select name="scd_result" id="scd_result" class="req_ipt review_select" onChange="fnValueCount();">
							<option value="">선택</option>
							<option value="1" <?php if($result == 1){ echo "selected"; }?>>승</option>
							<option value="2" <?php if($result == 2){ echo "selected"; }?>>무</option>
							<option value="3" <?php if($result == 3){ echo "selected"; }?>>패</option>
						</select>
					<?php }?>
				<?php }?>
				<strong><?php echo getTeamName($row['at_vs_idx']);?></strong>
			</p>
		</div>	

		<div class="mr_score">
			<p class="mr_score_txt">상대방의 실력은 어땠나요?</p>
			<div class="mr_score_info">
				<p class="mr_score_txt2">
					<span>평점을 선택해주세요.</span>
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
				</p>
			</div>
			<ul class="mr_score_star">
				<?php for($i=1; $i<=5; $i++){?>
				<li <?php if(((!$w || $w=="") &&$i <= 3) || ($w=="u" && $i <= $score)){?>class="on"<?php }?> <?php if(!$w || $w == ""){?>onClick="pickScore('<?php echo $i?>');"<?php }?>></li>
				<?php }?>
			</ul>
		</div>

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">
					텐션
					<button type="button" class="info_window">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
					</button>
				</p>
				<div class="regi_td">
					<?php if($w == "u"){?>
						<input type="text" name="scd_tention" id="scd_tention" class="regi_ipt req_ipt" readonly value="<?php echo $tention?>">
					<?php }else{?>
						<select name="scd_tention" id="scd_tention" class="regi_ipt req_ipt regi_select regi_select2" onChange="fnValueCount();">
							<option value="">텐션을 선택하세요.</option>
							<?php for($i=1; $i<=5; $i++){?>
							<option value="<?php echo $i; ?>" <?php if($tention == $i){ echo "selected"; }?>><?php echo $i?></option>
							<?php }?>
						</select>
					<?php }?>
				</div>
				<div class="regi_th_desc ver3">
					<p>*텐션 - 매치한 상대팀이 입력한 점수입니다.</p>
					<ul >
						<li>
							<p>텐션 1 : </p>
							<p>몸싸움, 태클 없이 즐겜해요.</p>
						</li>
						<li>
							<p>텐션 5 : </p>
							<p>대회급 열정으로 빡겜해요.</p>
						</li>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">
					메모
					<span class="max_txt ver2">(상대방에게 공개되지 않아요.)</span>
				</p>
				<div class="regi_td">
					<textarea name="scd_memo" id="scd_memo" class="regi_ipt regi_txtarea3" placeholder="메모를 작성하세요."><?php echo $memo?></textarea>
				</div>
			</li>
		</ul>

		<a href="<?php echo G5_URL?>/user/rematch_write.php?am_idx=<?php echo $am_idx?>&scd_idx=<?php echo $scd_idx?>" class="rematch_btn">리매치 신청</a>
	</div>	

	<div class="fix_btn_back"></div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn <?php if($w == "u"){ echo "on"; }?>" id="submit_button" onClick="register();">저장</button>
	</div>
</form>

<script>
function pickScore(v){
	$("#scd_score").val(v);
	$(".mr_score_star li").removeClass("on");
	for(let i=1; i<=v; i++){
		$(`.mr_score_star li:nth-child(${i})`).addClass("on");
	}
	fnValueCount();
}

$(".regi_ipt").keyup(function(e) {
	fnValueCount();
});

function fnValueCount(){
	let reqCnt = $(".req_ipt").length;
	let reqCurCnt = 0;

	$(".req_ipt").each(function(){
		if($(this).val() != ""){
			reqCurCnt++;
		}
	});

	//console.log(reqCurCnt+"//"+reqCnt);
	if(reqCurCnt >= reqCnt){
		$("#submit_button").addClass("on");
	}else{
		$("#submit_button").removeClass("on");
	}
}

function register(){
	const w = document.getElementById("w").value;
	const scd_result = document.getElementById("scd_result");	
	const scd_score = document.getElementById("scd_score");
	const scd_tention = document.getElementById("scd_tention");

	if(scd_result.value == ""){ showToast("본인 팀의 승패 결과를 선택해 주세요."); remove_active(); return false; }
	if(scd_score.value == ""){ showToast("상대팀에 대한 평점을 선택해 주세요."); remove_active(); return false; }
	if(scd_tention.value == ""){ showToast("상대팀에 대한 텐션을 선택해 주세요."); remove_active(); return false; }

	const wr_frm = $("form[name=review_frm]").serialize();
	$("#submit_button").attr("disabled", true);

	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/match_review_update.php",
		data: wr_frm, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			if(w == ""){
				//showToast("작성이 완료되었습니다.");
				location.reload();
			}else if(w == "u"){
				showToast("수정이 완료되었습니다.");
			}
			$("#submit_button").attr("disabled", false);
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>