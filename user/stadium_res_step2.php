<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	$expTime = explode("|", $v4);
	$expLast = count($expTime)-1;
	$expTime1 = $expTime[0];
	$expTime2 = $expTime[$expLast];

	$sql = " select * from g5_member where mb_no = '{$v1}' ";
	$mb =sql_fetch($sql);

	$sql2 = " select * from a_stadium where as_idx = '{$v3}' ";
	$row2 = sql_fetch($sql2);
?>

<form name="step2_frm" method="post">
	<input type="hidden" name="v1" value="<?php echo $v1?>">
	<input type="hidden" name="fs_name" value="<?php echo $mb['mb_fs_name']?>">
	<input type="hidden" name="as_idx" value="<?php echo $v3?>">
	<input type="hidden" name="scd_res_type" value="2">
	<input type="hidden" name="scd_date" value="<?php echo $v2?>">		
	<input type="hidden" name="scd_start" value="<?php echo $expTime1?>">
	<input type="hidden" name="scd_end" value="<?php echo $expTime2?>">
	<input type="hidden" name="scd_match_sort" value="1">
	<input type="hidden" name="scd_price" value="<?php echo $row2['as_price']?>">

	<div class="res_step2 cm_padd4">
		<ul class="res_step2_info">
			<li>
				<p>구장명</p>
				<div><?php echo $mb['mb_fs_name']?></div>
			</li>
			<li>
				<p>날짜</p>
				<div><?php echo str_replace('-', '. ', $v2)?> (<?php echo getYoil($v2)?>)</div>
			</li>
			<li>
				<p>예약 시간</p>
				<div><?php echo sprintf('%02d', $expTime1)?>:00 ~ <?php echo sprintf('%02d', $expTime2)?>:00</div>
			</li>
		</ul>
		<ul class="st_frm_chk ver2">
			<li>
				<input type="radio" name="scd_match_type" id="scd_match_type1" value="1" checked onChange="gameType('1');">
				<label for="scd_match_type1">자체 경기에요</label>
			</li>
			<li>
				<input type="radio" name="scd_match_type" id="scd_match_type2" value="2" onChange="gameType('2');">
				<label for="scd_match_type2">매치 경기에요</label>
			</li>
		</ul>
		<ul class="res_step2_alert">
			<li>해당 구장 관리자가 예약 신청 승인 시 예약 확정됩니다.</li>
			<li>매치 경기에요 선택 시 작성한 내용을 토대로 매치 리스트에 매치 등록됩니다.</li>
			<li>매치 등록 내역은 매치 -> 매치 내역 -> 매치 신청/등록에서 확인 가능합니다.</li>
		</ul>

		<div class="match_ver_box">
			<ul class="regi_ul">
				<li class="regi_li">
					<p class="regi_th">팀<span>*</span></p>
					<div class="regi_td">
						<select name="match_team" id="match_team" class="regi_ipt regi_select2" <?php echo $disabled?>>
							<?php for($t=1; $t<=3; $t++){?>
								<?php if($member['mb_user_team'.$t]){ ?>
								<option value="<?php echo $member['mb_user_team'.$t]."||".getTeamName($member['mb_user_team'.$t])?>">
									<?php echo getTeamName($member['mb_user_team'.$t])?>
								</option>
								<?php }?>
							<?php }?>
						</select>
					</div>
				</li>				
				<li class="regi_li">
					<p class="regi_th">매치 레벨<span>*</span></p>
					<div class="regi_td">
						<select name="scd_match_level" id="scd_match_level" class="regi_ipt regi_select2" <?php echo $disabled?>>
							<?php for($i=0; $i<count($_cfg['match']['level']); $i++){?>
							<option value="<?php echo $_cfg['match']['level'][$i]['val']?>" <?php if($row['scd_match_level'] == $_cfg['match']['level'][$i]['val']){ echo "selected";  }?>>
								<?php echo $_cfg['match']['level'][$i]['txt']?>
							</option>
							<?php }?>
						</select>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">인원<span>*</span></p>
					<div class="regi_td">
						<ul class="st_frm_chk">
							<?php for($t=0; $t<count($_cfg['stadium']['to']); $t++){?>
							<li>							
								<input type="radio" name="scd_match_to" id="scd_match_to<?php echo $t?>" value="<?php echo $_cfg['stadium']['to'][$t]['val']?>" <?php if($t == 0){ echo "checked";  }?>>
								<label for="scd_match_to<?php echo $t?>"><?php echo $_cfg['stadium']['to'][$t]['txt']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">내기<span>*</span></p>
					<div class="regi_td">
						<select name="scd_match_bet" id="scd_match_bet" class="regi_ipt regi_select2" <?php echo $disabled?>>
							<option value="1" <?php if($row['scd_match_bet'] == 1){ echo "selected";  }?>>구장비</option>
							<option value="2" <?php if($row['scd_match_bet'] == 2){ echo "selected";  }?>>음료수</option>
							<option value="0" <?php if($row['scd_match_bet'] == 0){ echo "selected";  }?>>선택 안함</option>
						</select>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">연령대<span>*</span></p>
					<div class="regi_td">
						<select name="scd_match_age" id="scd_match_age" class="regi_ipt regi_select2" <?php echo $disabled?>>
							<?php for($i=0; $i<count($_cfg['match']['age']); $i++){?>
							<option value="<?php echo $_cfg['match']['age'][$i]['val']?>" <?php if($row['scd_match_age'] == $_cfg['match']['age'][$i]['val']){ echo "selected";  }?>>
								<?php echo $_cfg['match']['age'][$i]['txt']?>
							</option>
							<?php }?>
						</select>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">성별<span>*</span></p>
					<div class="regi_td">
						<select name="scd_match_gender" id="scd_match_gender" class="regi_ipt regi_select2" <?php echo $disabled?>>
						<?php for($i=0; $i<count($_cfg['match']['gender']); $i++){?>
						<option value="<?php echo $_cfg['match']['gender'][$i]['val']?>" <?php if($row['scd_match_gender'] == $_cfg['match']['gender'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['gender'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="fix_btn_back"></div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn on" id="submit_button" onClick="insertRes();">예약 신청하기</button>
	</div>
</form>

<script>
	function gameType(v){
		if(v == "2"){
			$(".match_ver_box").show();
		}else{
			$(".match_ver_box").hide();
		}
	}

	function insertRes(){
		const string = $("form[name=step2_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/user_insert_res.php",
			data: string, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(data == "0000"){
					showToast("이미 예약되었거나 예약대기중입니다.<br>처음부터 다시 진행해 주세요.");
				}else{
					location.replace("<?php echo G5_URL?>/user/member/user_res_list.php");
				}
			}
		});
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>