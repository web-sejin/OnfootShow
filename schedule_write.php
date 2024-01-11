<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	$baseDate = str_replace(". ", "-",$s_datepicker);
	$expTime = explode("|", $time_pick_ipt);
	$expLast = count($expTime)-1;
	$expTime1 = $expTime[0];
	$expTime2 = $expTime[$expLast];

	$sql = " select * from a_schedule_res where scd_idx = '{$idx}' ";
	$row = sql_fetch($sql);
	
	$time_ary = array();
	$sql2 = " select * from a_schedule_res_time where scd_idx = '{$idx}' ";
	$result2 = sql_query($sql2);
	for($i=0; $row2=sql_fetch_array($result2); $i++){
		array_push($time_ary, $row2['scdt_time']);
	}

	if($stadium_idx){
		$s_stadium = $stadium_idx;	
	}

	if($w=="u" && $idx){
		$s_stadium = $row['as_idx'];
		$baseDate = $row['scd_date'];
		$expTime1 = $row['scd_start'];
		$expTime2 = $row['scd_end'];
	}

	$disabled = "";
	$addDate = $baseDate." ".sprintf('%02d',$expTime1).":00:00";
	$addDate2 = $baseDate." ".sprintf('%02d',$expTime2+1).":00:00";
	if($w=="u" && $addDate < G5_TIME_YMDHIS){
		$disabled = "disabled";
	}
?>

<div class="scd_write regi_area cm_padd4">
	<form name="wrt_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="w" id="w" value="<?php echo $w?>">
		<input type="hidden" name="scd_idx" id="scd_idx" value="<?php echo $idx?>">
		<input type="hidden" name="scd_res_type" value="1">
		<input type="hidden" name="other_idx" id="other_idx">
		<input type="hidden" name="other_mb_idx" id="other_mb_idx">
		<input type="hidden" name="other_at_idx" id="other_at_idx">
		<input type="hidden" name="scd_state" value="<?php echo $row['scd_state']?>">
		<input type="hidden" id="url" value="<?php echo $url?>">
		<input type="hidden" name="disabled" value="<?php echo $disabled?>">
		<input type="hidden" name="scd_vs_team_idx" value="<?php echo $row['scd_vs_team_idx']?>">

		<p class="scd_date">
			<input type="text" name="scd_date" id="scd_date" class="scd_date_ipt" readonly value="<?php echo str_replace("-", ". ",$baseDate); ?>" <?php echo $disabled?>>
		</p>
		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">구장<span>*</span></p>
				<div class="regi_td">
					<select name="as_idx" id="as_idx" class="regi_ipt regi_select2 req_ipt" onChange="getResinfo();" <?php echo $disabled?>>
						<?php
							$sql_st = " select * from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 order by as_datetime asc ";
							$result_st = sql_query($sql_st);
							for($i=0; $stadium=sql_fetch_array($result_st); $i++){
						?>
						<option value="<?php echo $stadium['as_idx']?>" <?php if($s_stadium == $stadium['as_idx']){ echo "selected";  }?>><?php echo $stadium['as_name']?></option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li" id="change_time_li">
				<p class="regi_th">시간대<span>*</span></p>
				<div class="regi_td frm_btn_flex regi_td_time">
					<?php if(!$w || $addDate >= G5_TIME_YMDHIS){?>
						<select name="scd_start" id="mb_fs_start" class="regi_ipt req_ipt regi_select2 regi_time ver3" onChange="getEndTimeList(this.value); fnValueCount();">
							<?php 							
								for($i=$member['mb_fs_start']; $i<($member['mb_fs_end']-1); $i++){
									$use = false;
									$dateReplace = str_replace(". ", "-", $baseDate);
									if($w=="u" && $idx){
										for($j=0; $j<count($time_ary); $j++){
											if($time_ary[$j] == $i){
												$use = true;
											}
										}
									}

									$baseResultDate = $dateReplace." ".sprintf('%02d', $i).":00:00";
									if(G5_TIME_YMDHIS < $baseResultDate){										

									$sql_dt = " select count(*) cnt from a_schedule_res_time A,  a_schedule_res B where A.scdt_date = '{$dateReplace}' and A.scdt_time = '{$i}' and A.as_idx = '{$s_stadium}' and A.scd_idx = B.scd_idx and B.delete_state = 1 ";
									$row_dt = sql_fetch($sql_dt);				
									if($use || $row_dt['cnt'] < 1){
							?>
							<option value="<?php echo $i?>" <?php if($expTime1 == $i){ echo "selected";  }?>><?php echo sprintf('%02d', $i)?>:00</option>
							<?php }}}?>
						</select>
						<span>~</span>
						<select name="scd_end" id="mb_fs_end" class="regi_ipt req_ipt regi_select2 regi_time ver3" onChange="fnValueCount();"></select>
					<?php }else{?>
						<select name="scd_start1 id="mb_fs_start" class="regi_ipt req_ipt regi_select2 regi_time ver3" <?php echo $disabled?>>
							<option value="<?php echo $row['scd_start']?>"><?php echo sprintf('%02d', $row['scd_start'])?>:00</option>
						</select>
						<span>~</span>
						<select name="scd_end" id="mb_fs_end" class="regi_ipt req_ipt regi_select2 regi_time ver3" <?php echo $disabled?>>
							<option value="<?php echo $row['scd_end']?>"><?php echo sprintf('%02d', $row['scd_end']+1)?>:00</option>
						</select>
					<?php }?>
				</div>
				
				<div class="add_match_time_box">
					<?php
						$sql_sub = " select * from a_schedule_res_time where scd_idx = '{$idx}' and as_idx = '{$s_stadium}' and scdt_date = '{$baseDate}' and scdt_type != 0 ";
						$result_sub = sql_query($sql_sub);
						for($i=0; $row_sub=sql_fetch_array($result_sub); $i++){
					?>
					<div class="frm_btn_flex regi_td_time">
						<input class="regi_ipt req_ipt regi_time ver3" value="<?php echo sprintf('%02d', $row_sub['scdt_time'])?>:00" disabled>
						<span>~</span>
						<input class="regi_ipt req_ipt regi_time ver3" value="<?php echo sprintf('%02d', $row_sub['scdt_time']+1)?>:00" disabled>
					</div>
					<?php }?>
				</div>
				<?php if(G5_TIME_YMDHIS >= $addDate && G5_TIME_YMDHIS <= $addDate2){ ?>
				<button type="button" class="regi_td_btn ver2 regi_tiem_add_btn" onClick="matchTimeAdd();">시간 추가</button>
				<?php }?>
				<!--button type="button" class="regi_td_btn ver2" onClick="matchTimeAdd();">시간 추가</button-->
			</li>
			<li class="regi_li">
				<p class="regi_th">매치 유형<span>*</span></p>
				<div class="regi_td">
					<select name="scd_match_type" id="scd_match_type" class="regi_ipt regi_select2 req_ipt" onChange="change1(this.value); fnValueCount();" <?php echo $disabled?>>
						<option value="1" <?php if($row['scd_match_type'] == 1){ echo "selected";  }?>>자체</option>
						<option value="2" <?php if($row['scd_match_type'] == 2){ echo "selected";  }?>>매치</option>
						<option value="3" <?php if($row['scd_match_type'] == 3){ echo "selected";  }?>>임의예약</option>
						<!--option value="4" <?php if($row['scd_match_type'] == 4){ echo "selected";  }?>>등록대기</option-->
					</select>
				</div>
			</li>
			<li class="regi_li only_match if_short_res" <?php if($row['scd_match_type'] == 2){?>style="display:block;"<?php }?>>
				<p class="regi_th">내기 유형<span>*</span></p>
				<div class="regi_td">
					<select name="scd_match_bet" id="scd_match_bet" class="regi_ipt regi_select2" onChange="fnBetChk(this.value); fnValueCount();" <?php echo $disabled?>>
						<option value="1" <?php if($row['scd_match_bet'] == 1){ echo "selected";  }?>>구장비</option>
						<option value="2" <?php if($row['scd_match_bet'] == 2){ echo "selected";  }?>>음료수</option>
						<option value="0" <?php if($row['scd_match_bet'] == 0){ echo "selected";  }?>>선택 안함</option>
					</select>
				</div>
			</li>
			<li class="regi_li if_short_res" <?php if($row['scd_match_type'] == 3){?>style="display:none;"<?php }?>>
				<p class="regi_th">분류<span>*</span></p>
				<div class="regi_td regi_td_sort <?php if($row['scd_match_sort'] == 2){ echo "on";  }?>">
					<div class="regi_box regi_td_flex">
						<select name="scd_match_sort" id="scd_match_sort" class="regi_ipt regi_select regi_select2 req_ipt" onChange="change2(this.value); fnValueCount();" style="color:#000;" <?php echo $disabled?>>
							<!--option value="">선택</option-->
							<option value="1" <?php if(!$w || $row['scd_match_sort'] == 1){ echo "selected";  }?>>일반</option>
							<option value="2" <?php if($row['scd_match_sort'] == 2){ echo "selected";  }?>>고정</option>
						</select>
						<select name="scd_match_team_idx" id="scd_match_team_idx" class="regi_ipt regi_select regi_select2" onChange="change3(this.value); fnValueCount();" <?php if($row['scd_match_sort'] == 2){?>style="color:#000;"<?php }?> <?php echo $disabled?>>
							<option value="">선택</option>
							<?php
								$sql_fix = " select * from a_team_fix where mb_idx = '{$member['mb_no']}' order by atf_team_name asc ";
								$result_fix = sql_query($sql_fix);
								for($f=0; $fix=sql_fetch_array($result_fix); $f++){
							?>
							<option value="<?php echo $fix['atf_idx']?>" <?php if($row['atf_idx'] == $fix['atf_idx']){ echo "selected";  }?>>
								<?php echo $fix['atf_team_name']?>
							</option>
							<?php }?>
						</select>
					</div>
					<div class="regi_box">
						<button type="button" class="regi_td_btn ver2" onClick="cmPopOn('search_pop');" <?php echo $disabled?>>예약자 검색</button>
					</div>
				</div>
			</li>
			<li class="regi_li if_short_res" <?php if($row['scd_match_type'] == 3){?>style="display:none;"<?php }?>>
				<p class="regi_th">예약자<span>*</span></p>
				<div class="regi_td">
					<input type="text" name="scd_name" id="scd_name" class="regi_ipt req_ipt" placeholder="예약자 이름을 입력해주세요." value="<?php echo $row['scd_name']?>" <?php echo $disabled?>>
				</div>
			</li>
			<li class="regi_li if_short_res" <?php if($row['scd_match_type'] == 3){?>style="display:none;"<?php }?>>
				<p class="regi_th">전화번호<span>*</span></p>
				<div class="regi_td">
					<input type="tel" name="scd_hp" id="scd_hp" class="regi_ipt req_ipt phone" placeholder="'-'없이 입력해주세요." maxlength="13" value="<?php echo $row['scd_hp']?>" <?php echo $disabled?>>
				</div>
			</li>
			<li class="regi_li if_short_res" <?php if($row['scd_match_type'] == 3){?>style="display:none;"<?php }?>>
				<p class="regi_th regi_th_team_name">팀명</p>
				<div class="regi_td">
					<input type="text" name="scd_team_name" id="scd_team_name" class="regi_ipt" placeholder="팀명을 입력해주세요." value="<?php echo $row['scd_team_name']?>" <?php echo $disabled?>>
				</div>
			</li>
			<li class="regi_li only_match if_short_res" <?php if($row['scd_match_type'] == 2){?>style="display:block;"<?php }?>>
				<p class="regi_th">매치 레벨<span>*</span></p>
				<div class="regi_td">
					<select name="scd_match_level" id="scd_match_level" class="regi_ipt regi_select2" onChange="fnValueCount();" <?php echo $disabled?>>
						<?php for($i=0; $i<count($_cfg['match']['level']); $i++){?>
						<option value="<?php echo $_cfg['match']['level'][$i]['val']?>" <?php if($row['scd_match_level'] == $_cfg['match']['level'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['level'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li only_match if_short_res">
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
			<li class="regi_li only_match if_short_res" <?php if($row['scd_match_type'] == 2){?>style="display:block;"<?php }?>>
				<p class="regi_th">연령대<span>*</span></p>
				<div class="regi_td">
					<select name="scd_match_age" id="scd_match_age" class="regi_ipt regi_select2" onChange="fnValueCount();" <?php echo $disabled?>>
						<?php for($i=0; $i<count($_cfg['match']['age']); $i++){?>
						<option value="<?php echo $_cfg['match']['age'][$i]['val']?>" <?php if($row['scd_match_age'] == $_cfg['match']['age'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['age'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li only_match if_short_res" <?php if($row['scd_match_type'] == 2){?>style="display:block;"<?php }?>>
				<p class="regi_th">성별<span>*</span></p>
				<div class="regi_td">
					<select name="scd_match_gender" id="scd_match_gender" class="regi_ipt regi_select2" onChange="fnValueCount();" <?php echo $disabled?>>
						<?php for($i=0; $i<count($_cfg['match']['gender']); $i++){?>
						<option value="<?php echo $_cfg['match']['gender'][$i]['val']?>" <?php if($row['scd_match_gender'] == $_cfg['match']['gender'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['gender'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li if_short_res" <?php if($row['scd_match_type'] == 3){?>style="display:none;"<?php }?>>
				<p class="regi_th">메모</p>
				<div class="regi_td">
					<textarea name="scd_memo" id="scd_memo" class="regi_ipt regi_txtarea" placeholder="메모를 입력해주세요."><?php echo $row['scd_memo']?></textarea>
				</div>
			</li>
		</ul>
		<?php if($row['scd_match_type'] == 2 && $row['scd_state'] == 0){?>
		<div class="match_tag">
			<button type="button" class="regi_td_btn ver2 ver3" onClick="cmPopOn('match_pop');">매치 합치기</button>
		</div>
		<p class="match_complete_team"></p>
		<?php }?>

		<?php if($row['scd_state']  == 1){?>
		<div class="other_team_info">
			<p class="match_vs_line">vs</p>
			<ul class="regi_ul">
				<li class="regi_li">
					<p class="regi_th">분류</p>
					<div class="regi_td">
						<?php if($row['scd_vs_team_idx']){?>
						<input type="text" class="regi_ipt" value="<?php echo getOtherTeam($row['scd_vs_team_idx'], 'other_sort') ?>" disabled>
						<?php }else{?>
						<input type="text" class="regi_ipt" value="일반" disabled>
						<?php }?>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">예약자</p>
					<div class="regi_td">
						<?php if($row['scd_vs_team_idx']){?>
						<input type="text" class="regi_ipt" value="<?php echo getOtherTeam($row['scd_vs_team_idx'], 'other_name') ?>" disabled>
						<?php }else{?>
						<input type="text" class="regi_ipt" value="<?php echo getMemberinfo($row['scd_vs_team_mb_idx'], 'mb_name')?>" disabled>
						<?php }?>				
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">전화번호</p>
					<div class="regi_td">
						<?php if($row['scd_vs_team_idx']){?>
						<input type="text" class="regi_ipt" value="<?php echo getOtherTeam($row['scd_vs_team_idx'], 'other_hp') ?>" disabled>
						<?php }else{?>
						<input type="text" class="regi_ipt" value="<?php echo getMemberinfo($row['scd_vs_team_mb_idx'], 'mb_hp')?>" disabled>
						<?php }?>						
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">팀명</p>
					<div class="regi_td">
						<?php if($row['scd_vs_team_idx']){?>
						<input type="text" class="regi_ipt" value="<?php echo getOtherTeam($row['scd_vs_team_idx'], 'other_team_name') ?>" disabled>
						<?php }else{?>
						<input type="text" class="regi_ipt" value="<?php echo getTeamName($row['scd_vs_team_at_idx'])?>" disabled>
						<?php }?>								
					</div>
				</li>
				<?php if($row['scd_vs_team_idx']){?>
				<li class="regi_li">
					<p class="regi_th">레벨</p>
					<div class="regi_td">
						<input type="text" class="regi_ipt" value="<?php echo $_cfg['match']['level'][getOtherTeam($row['scd_vs_team_idx'], 'other_level')-1]['txt'] ?>" disabled>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">연령대</p>
					<div class="regi_td">
						<input type="text" class="regi_ipt" value="<?php echo $_cfg['match']['age'][getOtherTeam($row['scd_vs_team_idx'], 'other_age')-1]['txt'] ?>" disabled>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">성별</p>
					<div class="regi_td">
						<input type="text" class="regi_ipt" value="<?php echo $_cfg['match']['gender'][getOtherTeam($row['scd_vs_team_idx'], 'other_gender')-1]['txt'] ?>" disabled>
					</div>
				</li>
				<?php }?>
				<li class="regi_li if_short_res">
					<p class="regi_th">메모</p>
					<div class="regi_td">
						<textarea name="other_memo" class="regi_ipt regi_txtarea" placeholder="메모를 입력해주세요."><?php echo getOtherTeam($row['scd_vs_team_idx'], 'other_memo') ?></textarea>
					</div>
				</li>
			</ul>
		</div>
		<?php }?>

		<div class="fix_btn_back"></div>
		<div class="fix_btn_box <?php if($w == "u"){?>fix_btn_box_flex<?php }?>">
			<?php if($w == "u"){?>
				<button type="button" class="fix_btn" onClick="cmPopOn('complete_pop');" <?php echo $disabled?>>삭제</button>
				<button type="button" class="fix_btn on <?php if($disabled == "disabled"){ echo "on2";  }?>" id="submit_button" onClick="scheduleUpdate();">저장</button>
			<?php }else{?>
				<button type="button" class="fix_btn" id="submit_button" onClick="scheduleUpdate();">등록</button>
			<?php }?>
		</div>
	</form>
</div>

<div class="cm_pop" id="search_pop">
	<div class="header">
		<button type="button" class="back_btn" onClick="closeSch('search_pop');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
		<p class="sub_title">예약자 검색</p>
	</div>
	<div class="cm_pop_cont ver2">
		<div class="people_sch_box">
			<input type="text" id="peo_sch_val" placeholder="이름 또는 전화번호 검색">
			<button type="button" onClick="getResPeople();">
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
			</button>
		</div>
		<ul class="people_list" id="people_list">
		</ul>
	</div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn" id="submit_button2" onClick="peopleSelect();">등록</button>
	</div>
</div>

<?php if($w == "u"){?>
<div class="cm_pop" id="match_pop">
	<div class="header">
		<button type="button" class="back_btn" onClick="resetOther();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
		<p class="sub_title">매치 합치기</p>
	</div>
	<div class="cm_pop_cont">		
		<input type="hidden" id="other_match">
		<input type="hidden" id="other_team_name">		
		<input type="hidden" id="other_mb_no">
		<input type="hidden" id="other_at_no">
		<div class="other_match_box">
			<input type="text" class="scd_date_ipt2" readonly value="<?php echo str_replace("-", ". ",$baseDate); ?>">
			<ul class="other_match_list">
				<?php
					$currHour = date("H");
					$currCnt = 0;
					$sql2 = " select * from a_schedule_res 
									where 1
										and scd_idx != '{$idx}' 
										and as_idx = '{$row['as_idx']}'
										and scd_date = '{$baseDate}'
										and scd_start > '{$currHour}'
										and scd_match_type = 2
										and scd_state = 0
										and delete_state = 1
									order by scd_start asc
									";
					$result2 = sql_query($sql2);
					for($i=0; $row2=sql_fetch_array($result2); $i++){
						$currCnt++;
				?>
				<li class="oml_li oml_<?php echo $row2['scd_idx']?>" onClick="otherMatch('<?php echo $row2['scd_idx']?>', '<?php echo $row2['scd_team_name']?>', '<?php if($row2['mb_idx'] != $member['mb_no']){ echo $row2['mb_idx']; }?>', '<?php if($row2['mb_idx'] != $member['mb_no']){ echo $row2['scd_team_idx']; }?>');">
					<span><?php echo sprintf('%02d',$row2['scd_start'])?>:00 ~ <?php echo sprintf('%02d',$row2['scd_end'])?>:00</span>
					<strong><?php echo $row2['scd_team_name']?> &nbsp;vs&nbsp; -</strong>
				</li>
				<?php }?>
				<?php if($currCnt < 1){?>
				<li class="oml_li oml_not">매치 정보가 없습니다.</li>
				<?php }?>
			</ul>
		</div>

		<div class="other_match_box">
			<?php
				$nextDate = date("Y-m-d", strtotime($baseDate." +1 day"));
			?>
			<input type="text" class="scd_date_ipt2" readonly value="<?php echo str_replace("-", ". ",$nextDate); ?>">
			<ul class="other_match_list">
				<?php
					$currCnt = 0;
					$sql2 = " select * from a_schedule_res 
									where 1
										and scd_idx != '{$idx}' 
										and as_idx = '{$row['as_idx']}'
										and scd_date = '{$nextDate}'
										and scd_match_type = 2
										and scd_state = 0
										and delete_state = 1
									order by scd_start asc
									";
					$result2 = sql_query($sql2);
					for($i=0; $row2=sql_fetch_array($result2); $i++){
						$currCnt++;
				?>
				<li class="oml_li oml_<?php echo $row2['scd_idx']?>" onClick="otherMatch('<?php echo $row2['scd_idx']?>', '<?php echo $row2['scd_team_name']?>',  '<?php if($row2['mb_idx'] != $member['mb_no']){ echo $row2['mb_idx']; }?>', '<?php if($row2['mb_idx'] != $member['mb_no']){ echo $row2['scd_team_idx']; }?>');">
					<span><?php echo sprintf('%02d',$row2['scd_start'])?>:00 ~ <?php echo sprintf('%02d',$row2['scd_end'])?>:00</span>
					<strong><?php echo $row2['scd_team_name']?> &nbsp;vs&nbsp; -</strong>
				</li>
				<?php }?>
				<?php if($currCnt < 1){?>
				<li class="oml_li oml_not">매치 정보가 없습니다.</li>
				<?php }?>
			</ul>
		</div>
	</div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn on" onClick="otherTeamSelect();">매치 합치기</button>
	</div>
</div>

<form name="time_add_frm" method="post">
	<input type="hidden" name="scd_idx" value="<?php echo $idx?>">
	<input type="hidden" id="next_ipt">
	<div class="cm_pop" id="time_pop">
		<div class="header">
			<button type="button" class="back_btn" onClick="resetTime();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
			<p class="sub_title">시간 추가</p>
		</div>
		<div class="cm_pop_cont">			
			<ul class="time_add_list" id="time_add_list">
				
			</ul>
		</div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn on" onClick="timeAddOk();">추가</button>
		</div>
	</div>
</form>

<div id="time_confirm" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">시간이 바로 추가됩니다.<br>진행하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('time_confirm');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="timeAddOk2();">확인</button>
		</div>
	</div>
</div>

<div id="complete_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">삭제를 진행하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('complete_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="schedlueDelete('<?php echo base64_encode($idx)?>');">확인</button>
		</div>
	</div>
</div>
<?php }?>

<script>
	$(function(){
		$("#mb_fs_start").val("<?php echo $expTime1?>");
		$("#mb_fs_start").change();
		$("#mb_fs_end").val("<?php echo $expTime2?>");

		if($("#w").val() == "u"){
			//$("#scd_match_type").val("<?php echo $row['scd_match_type']?>");
			//$("#scd_match_type").change();
		}
	});

	function scheduleUpdate(){
		//#매칭일 때 추가되는 필수 = [ 내기유형 / 팀명 / 매치레벨 / 연령대 / 성별 ]
		//#고정팀일 때 추가되는 필수 = [팀 선택]
		const matchType = document.getElementById("scd_match_type");
		const matchSort = document.getElementById("scd_match_sort");
		const matchTeam = document.getElementById("scd_match_team_idx");
		const name = document.getElementById("scd_name");
		const hp = document.getElementById("scd_hp");
		const teamName = document.getElementById("scd_team_name");
		const memo = document.getElementById("scd_memo");
		const matchBet = document.getElementById("scd_match_bet");
		const matchLevel = document.getElementById("scd_match_level");
		const matchAge = document.getElementById("scd_match_age");
		const matchGender = document.getElementById("scd_match_gender");

		//console.log("matchType :::: ", matchType.value);
		//console.log("matchSort :::: ", matchSort.value);
			
		if(matchType.value != "3"){					
			if(matchSort.value == ""){ showToast("분류를 선택해 주세요."); remove_active(); return false; }
			if(matchSort.value == "2"){
				if(matchTeam.value == ""){ showToast("고정팀을 선택해 주세요."); remove_active(); return false; }
			}
			if(name.value == ""){ showToast("예약자명을 입력해 주세요."); remove_active(); return false; }
			if(hp.value == ""){ showToast("전화번호를 입력해 주세요."); remove_active(); return false; }
			
			if(matchSort.value == "2"){
				if(teamName.value == ""){ showToast("팀명을 입력해 주세요."); remove_active(); return false; }
			}
		}
		
		var string = $("form[name=wrt_frm]").serialize();
		$("#submit_button").attr("disabled", true);
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/schedule_write_update.php",
			data: string,
			dataType : "json",
			cache: false,
			async: false,
			//contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(data.result_code == "0000"){
					showToast(data.message);
				}else{
					if(data.result_code == "1111"){
						if($("#url").val() == "1"){
							location.href = "<?php echo G5_URL?>";
						}else if($("#url").val() == "2"){
							location.href = "<?php echo G5_URL?>/schedule.php";
						}
					}else if(data.result_code == "1112"){
						showToast("수정이 완료되었습니다.");
					}
				}
				$("#submit_button").attr("disabled", false);
			}
		});
	}

	function change1(v){
		if(v == "1"){
			$(".if_short_res").show();
			$(".only_match").hide();
			$(".regi_th_team_name").html("팀명");
		}else if(v == "3"){
			$(".if_short_res").hide();
			$(".regi_th_team_name").html("팀명");
			$("#scd_match_sort").val("1");
		}else{
			$(".if_short_res").show();
			$(".only_match").show();			
			$(".regi_th_team_name").html("팀명<span>*</span>");
		}
		$("#scd_match_sort").change();
	}

	function change2(v){
		if(v == "2"){
			$(".regi_td_sort").addClass("on");			
			$(".regi_th_team_name").html("팀명<span>*</span>");
			if($("#scd_match_bet").val() == "1"){
				showToast("고정팀은 구장비 내기를 할 수 없습니다.");
				$("#scd_match_bet").val('0');
			}
		}else{
			$(".regi_td_sort").removeClass("on");
			$("#scd_match_team_idx").val("");
			$("#scd_name").val("");
			$("#scd_hp").val("");
			$("#scd_team_name").val("");
		}
	}

	function change3(v){
		//팀 선택에 따른 정보들 ajax로 호출
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/get_fix_team.php",
			data: {idx : v},
			dataType : "json",
			cache: false,
			async: false,
			//contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#scd_name").val(data.name);
				$("#scd_hp").val(data.hp);
				$("#scd_team_name").val(data.team);
				//fnValueCount();
			}
		});
	}

	$('.scd_date_ipt').datepicker({
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-5:c+5', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy. mm. dd", // 날짜의 형식
		showAnim: "fade", //애니메이션을 적용한다. 
		showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
		monthNames : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'], 
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		minDate: 0,
		beforeShow:function(){
			$(".datepicker_back").fadeIn();
		},
		onSelect:function(dateText, inst){
			$(".datepicker_back").fadeOut();
			getResinfo();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});
	
	function peopleRadioChg(){
		const chked = $("input[name=people]").is(":checked");
		if(chked){
			$("#submit_button2").addClass("on");
		}else{
			$("#submit_button2").removeClass("on");
		}
	}

	function peopleSelect(){
		const chked = $("input[name=people]").is(":checked");
		if(!chked){
			showToast("예약자를 선택해 주세요.");
		}else{
			const chkedVal = $("input[name=people]:checked").val();
			const chkedValSplt = chkedVal.split("||");
			$("#scd_name").val(chkedValSplt[0]);
			$("#scd_hp").val(chkedValSplt[1]);
			closeSch();
			fnValueCount();
		}
	}
	
	function closeSch(){		
		cmPopOff('search_pop');
		$("#peo_sch_val").val("");
		$("#people_list").empty();
	}

	function getResPeople(){
		if($("#peo_sch_val").val() == ""){
			showToast("이름 또는 전화번호를 입력해 주세요.");			
			return false;
		}

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getResPeople.php",
			data: {as_idx:$("#as_idx").val(), v:$("#peo_sch_val").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log("data : "+data);
				$("#people_list").empty().append(data);
			}
		});
	}

	function getEndTimeList(v){
		//alert("추후에 예약된 시간들 제외하고 연결되는 시간으로 바꿔야 함!");
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getEndTimeList.php",
			data: {startTime : v, scdDate : $("#scd_date").val(), w : $("#w").val(), idx : "<?php echo $idx?>", as_idx : $("#as_idx").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				console.log(data);
				$("#mb_fs_end").empty().append(data);
				$("#mb_fs_end").change();
			}
		});
	}

	function fnValueCount(){
		const matchType = $("#scd_match_type").val();
		const matchSort = $("#scd_match_sort").val();
		let reqCnt = 0;
		if(matchType == "1"){
			reqCnt = 7;
			if(matchSort == "2"){ reqCnt = 8; }
		}else if(matchType == "2"){
			reqCnt = 12;
		}else if(matchType == "3"){
			reqCnt = 5;
		}

		if(matchSort == "2"){
			reqCnt = reqCnt+1;			
		}

		let reqCurCnt = 0;
		$(".req_ipt").each(function(){
			if($(this).val() != ""){
				reqCurCnt++;
			}
		});

		if(matchType == "1" && matchSort == "2"){
			if($("#scd_team_name").val() != ""){ reqCurCnt++; }
		}
		
		if(matchType == "2"){			
			if($("#scd_match_bet").val() != ""){ reqCurCnt++; }
			if($("#scd_team_name").val() != ""){ reqCurCnt++; }
			if($("#scd_match_level").val() != ""){ reqCurCnt++; }
			if($("#scd_match_age").val() != ""){ reqCurCnt++; }
			if($("#scd_match_gender").val() != ""){ reqCurCnt++; }
		}

		if(matchSort == "2"){
			if($("#scd_match_team_idx").val() != ""){ reqCurCnt++; }
		}

		//console.log(reqCurCnt+"//"+reqCnt);

		if(reqCurCnt === reqCnt){
			$("#submit_button").addClass("on");
		}else{
			$("#submit_button").removeClass("on");
		}
	}

	$(".regi_ipt").keyup(function(e) {
		fnValueCount();
	});

	function getResinfo(){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getResTemplet.php",
			data: {date:$("#scd_date").val(), idx:"<?php echo $idx?>", as_idx:$("#as_idx").val(), w : $("#w").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#change_time_li").empty().append(data);
				$("#mb_fs_start").change()
			}
		});
	}

	function schedlueDelete(idx){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/schedlueDelete.php",
			data: {idx:idx}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				cmPopOff("complete_pop");
				if($("#url").val() == "1"){
					location.href = "<?php echo G5_URL?>";
				}else if($("#url").val() == "2"){
					location.href = "<?php echo G5_URL?>/schedule.php?as_idx=<?php echo $row['as_idx']?>";
				}
			}
		});		
	}

	function otherMatch(v, name, mb_idx, at_idx){		
		if($(".oml_"+v).hasClass("on") === true){
			$("#other_match").val('');
			$("#other_team_name").val('');
			$("#other_mb_no").val('');
			$("#other_at_no").val('');
			$(".oml_"+v).removeClass("on");
		}else{
			$("#other_match").val(v);
			$("#other_team_name").val(name);
			$("#other_mb_no").val(mb_idx);
			$("#other_at_no").val(at_idx);
			$(".oml_li").removeClass("on");
			$(".oml_"+v).addClass("on");
		}
	}

	function otherTeamSelect(){
		if($("#other_match").val() == ""){
			showToast("매치를 합칠 팀을 선택해 주세요.");
			return false;
		}
		
		$("#other_idx").val($("#other_match").val());
		$("#other_mb_idx").val($("#other_mb_no").val());
		$("#other_at_idx").val($("#other_at_no").val());
		$(".match_complete_team").html("<span>"+$("#other_team_name").val()+"</span>");
		cmPopOff('match_pop');
	}

	function resetOther(){
		$("#other_match").val("");
		$("#other_team_name").val("");
		$("#other_idx").val("");
		$(".oml_li").removeClass("on");
		$(".match_complete_team").empty();
		cmPopOff('match_pop');
	}

	function fnBetChk(v){
		if($("#scd_match_sort").val() == "2" && v == "1"){
			showToast("고정팀은 구장비 내기를 할 수 없습니다.");
			$("#scd_match_bet").val('0');
		}
	}

	function matchTimeAdd(){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/matchTimeAdd.php",
			data: {scdEnd:"<?php echo $row['scd_end']?>", baseDate:"<?php echo $baseDate?>", as_idx:"<?php echo $row['as_idx']?>"}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#time_add_list").empty().append(data);
				cmPopOn('time_pop');
			}
		});		
	}

	function resetTime(){
		cmPopOff('time_pop');
	}

	function prevChk(v, z){
		const timeVal = parseInt(z);
		const next_ipt = $("#next_ipt");
		const next_ipt_val = parseInt(next_ipt.val());
		
		if(!next_ipt_val || timeVal == next_ipt_val){
			next_ipt.val(timeVal+1);
		}else{
			const chked = $("input[id=add_time_"+z+"]").is(':checked');
			if(chked){
				showToast("처음 선택한 시간을 기준으로 연속된 시간만 선택할 수 있습니다.");
				$("input[id=add_time_"+z+"]").prop("checked", false);
				return false;
			}else{
				if(timeVal+1 == next_ipt_val){
					next_ipt.val(z);
				}else{
					for(var i=0; i<parseInt(v); i++){
						const prevChked = $("#time_add_list li").eq(i).children("input[type=checkbox]").prop("checked", false);
					}
				}
			}
		}

		const chkCnt = $("input[name='add_time[]']:checked").length;
		if(chkCnt >= 2){
		}else{
			if(chkCnt < 1){ next_ipt.val(''); }
		}
	}

	function timeAddOk(){
		const cnt = $("input[name='add_time[]']:checked").length;
		if(cnt < 1){
			showToast("시간을 선택해 주세요.");
			return false;
		}
		cmPopOn('time_confirm');
	}

	function timeAddOk2(){
		var string = $("form[name=time_add_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/timeAddOk2.php",
			data: string, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				cmPopOff('time_confirm');
				location.reload();
			}
		});		
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>