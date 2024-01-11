<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");
	
	$nowYmd = date("Y. m. d", strtotime(G5_TIME_YMD));
	$now = $nowYmd." (".getYoil(G5_TIME_YMD).")";

	$sql = " select * from a_match where am_idx = '{$am_idx}' ";
	$match = sql_fetch($sql);

	if($member['mb_no'] == $match['mb_idx']){
		$my_at_idx = $match['at_idx'];
		$other_at_idx = $match['at_vs_idx'];
	}else{
		$my_at_idx = $match['at_vs_idx'];
		$other_at_idx = $match['at_idx'];
	}

	if($w == "u" && $row['am_date']){
		$nowYmd = date("Y. m. d", strtotime($row['am_date']));
		$now = $nowYmd." (".getYoil($row['am_date']).")";

		$sql = " select * from rb_sido where sd_idx = '{$row['sd_idx']}' ";
		$sido = sql_fetch($sql);
		$sido = $sido['sd_name'];

		$sql = " select * from rb_sigungu where si_idx = '{$row['si_idx']}' ";
		$sigugn = sql_fetch($sql);
		if($si_idx == "all"){
			$sigugn = "전체";
			$dong = "";
		}else{
			$sigugn = $sigugn['si_name'];	
		}
			
		if($si_idx != "all"){
			$sql = " select * from rb_dongli where do_idx = '{$row['do_idx']}' ";
			$dong = sql_fetch($sql);
			if($do_idx == "all"){
				$dong = "전체";		
			}else{
				$dong = $dong['do_name'];
			}
		}
	}
?>

<div class="match_write cm_padd4">
	<form name="wr_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
		<input type="hidden" name="am_idx" value="<?php echo $idx?>" readonly>
		<input type="hidden" name="rematch" value="1" readonly>
		<input type="hidden" name="rematch_am_idx" value="<?php echo $am_idx?>" readonly>
		<input type="hidden" name="pop_size" id="pop_size" class="hidden_ipt" value="<?php echo $row['pop_size']?>">
		<input type="hidden" name="pop_sort" id="pop_sort" class="hidden_ipt" value="<?php echo $row['pop_sort']?>">
		<input type="hidden" name="pop_use1" id="pop_use1" class="hidden_ipt" value="<?php echo $row['pop_use1']?>">
		<input type="hidden" name="pop_use2" id="pop_use2" class="hidden_ipt" value="<?php echo $row['pop_use2']?>">
		<input type="hidden" name="pop_use3" id="pop_use3" class="hidden_ipt" value="<?php echo $row['pop_use3']?>">
		
		<input type="hidden" name="at_idx" id="at_idx" class="req_ipt" value="<?php echo $my_at_idx?>">
		<input type="hidden" name="other_at_idx" id="other_at_idx" class="req_ipt" value="<?php echo $other_at_idx?>">

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">팀<span>*</span></p>
				<div class="regi_td">					
					<input type="text" class="regi_ipt" readonly value="<?php echo getTeamName($my_at_idx)?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">리매치 팀<span>*</span></p>
				<div class="regi_td">					
					<input type="text" class="regi_ipt" readonly value="<?php echo getTeamName($other_at_idx)?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">
					매치 레벨<span>*</span>
					<button type="button" class="info_window">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
					</button>
				</p>
				<div class="regi_td">
					<select name="am_level" id="am_level" class="regi_ipt req_ipt regi_select regi_select2" onChange="fnValueCount();">
						<option value="">매치 레벨을 선택하세요.</option>
						<?php for($i=0; $i<count($_cfg['match']['level']); $i++){?>
						<option value="<?php echo $_cfg['match']['level'][$i]['val']?>" <?php if($row['am_level'] == $_cfg['match']['level'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['level'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
				<div class="regi_th_desc">
					<p>*매치 레벨</p>
					<ul>
						<li>
							<p>최하 : </p>
							<p>풋살에 입문하거나 공을 다루는데 서투신 분들이 과반수로 포함된 팀</p>
						</li>
						<li>
							<p>하 : </p>
							<p>평범한 실력을 가진 동호인이 과반수로 포함된 팀 (선출급 미포함)</p>
						</li>
						<li>
							<p>중 : </p>
							<p>뛰어난 실력을 가진 동호인들로 구성된 팀 (선출급 2명 이하)</p>
						</li>
						<li>
							<p>상 : </p>
							<p>선출급(선출포함) 동호인이 과반수로 포함된 팀</p>
						</li>
						<li>
							<p>최상 : </p>
							<p>모든 멤버가 선출급(선출포함) 실력을 가진 잘 조직된 팀</p>
						</li>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">구장<span>*</span></p>
				<div class="regi_td">
					<div class="regi_use">
						<p class="regi_use_tit">지역</p>
						<ul class="regi_radio_ul ver2">
							<?php if($row['res_st'] == 2){?>
							<li>					
								<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select regi_select2 readonly">
									<?php
										$sqlS = " select * from rb_sido where sd_idx = '{$row['sd_idx']}' ";
										$sido = sql_fetch($sqlS);
									?>
									<option value="<?php echo $row['sd_idx']?>"><?php echo $sido['sd_name']?></option>
								</select>
							</li>
							<li>
								<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select regi_select2 readonly">
									<?php
										$sqlS = " select * from rb_sigungu where si_idx = '{$row['si_idx']}' ";
										$sigugun = sql_fetch($sqlS);
									?>
									<option value="<?php echo $row['si_idx']?>"><?php echo $sigugun['si_name']?></option>
								</select>
							</li>
							<?php }else{?>
							<li>					
								<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select regi_select2" onchange="chgSido(this.value); fnValueCount();">
									<option value="">시/도</option>
									<?php
									$sqlS = " select * from rb_sido where 1 order by sd_idx asc ";
									$resultS = sql_query($sqlS);
									$sido_selected = "";
									for($i=0; $sido=sql_fetch_array($resultS); $i++){
									?>
									<option value="<?php echo $sido['sd_idx']?>"><?php echo $sido['sd_name']?></option>
									<?php }?>
								</select>
							</li>
							<li>
								<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select regi_select2" onChange="chgSigugun(this.value); fnValueCount();">
									<option value="">시/구/군</option>
								</select>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="regi_use">
						<p class="regi_use_tit">일시</p>
						<ul class="regi_radio_ul ver2">
							<?php if($row['res_st'] == 2){?>
							<li>
								<input type="hidden" name="am_date" id="am_date" class="req_ipt" value="<?php if($w == "u"){ echo $row['am_date']; }else{ echo G5_TIME_YMD; }?>">
								<input type="text" class="regi_ipt readonly" readonly value="<?php echo $now?>">
							</li>
							<li>
								<select name="am_time" id="am_time" class="regi_ipt req_ipt show_clock readonly">
									<option value="<?php echo $row['am_time']?>"><?php echo sprintf('%02d', $row['am_time'])?>:00</option>
								</select>
							</li>
							<?php }else{?>
							<li>
								<input type="hidden" name="am_date" id="am_date" class="req_ipt" value="<?php if($w == "u"){ echo $row['am_date']; }else{ echo G5_TIME_YMD; }?>">
								<input type="text" id="show_date" class="regi_ipt" readonly value="<?php echo $now?>">
							</li>
							<li>
								<select name="am_time" id="am_time" class="regi_ipt req_ipt show_clock" onChange="fnValueCount();">
									<?php for($i=0; $i<25; $i++){?>
									<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
									<?php }?>
								</select>
							</li>
							<?php }?>
						</ul>
					</div>					
					<div class="regi_use <?php if($w=="u" && $row['res_st'] != 1){?>none<?php }?>">
						<p class="regi_use_tit">매치장소</p>
						<ul class="regi_radio_ul ver2">
							<li>
								<input type="radio" name="am_area" id="am_area1" value="1" <?php if(!$w || $row['am_area'] == 1){ echo "checked";  }?> onChange="chgArea(this.value); fnValueCount();">
								<label for="am_area1">지역</label>
							</li>
							<li>
								<input type="radio" name="am_area" id="am_area2" value="2" <?php if($row['am_area'] == 2){ echo "checked";  }?> onChange="chgArea(this.value); fnValueCount();">
								<label for="am_area2">구장</label>
							</li>
						</ul>						
					</div>					
					<button type="button" class="mid_btn <?php if($w=="u" && $row['res_st'] != 1){?>none<?php }?>" onClick="search_stadium();">검색</button>
					<ul class="picked_stadium <?php if($w=="u" && $row['res_st'] != 1){?>none<?php }?>">
						<?php if($row['am_area'] == 1){?>
						<li id="local_sort_li">
							<h3 class="ust_name"><?php echo $sido?> <?php echo $sigugn?> <?php echo $dong?></h3>
							<ul class="ust_sub_info2">
								<?php
									if($row['pop_size']){
									$exp1 = explode("|", $row['pop_size']);
									for($e=0; $e<count($exp1); $e++){
										$key = array_search($exp1[$e], array_column($_cfg['stadium']['to'], 'val'));
								?>
								<li><?php echo $_cfg['stadium']['to'][$key]['txt'];?></li>
								<?php }}?>

								<?php
									if($row['pop_sort']){
									$exp2 = explode("|", $row['pop_sort']);
									for($e=0; $e<count($exp2); $e++){
										$key = array_search($exp2[$e], array_column($_cfg['stadium']['sort'], 'val'));
								?>
								<li><?php echo $_cfg['stadium']['sort'][$key]['txt'];?></li>
								<?php }}?>

								<?php
									if($row['pop_use1']){
									$exp3 = explode("|", $row['pop_use1']);
									for($e=0; $e<count($exp3); $e++){
										$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp3[$e]}' ";
										$use1 = sql_fetch($sql_use);
								?>
								<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
								<?php }}?>

								<?php
									if($row['pop_use2']){
									$exp4 = explode("|", $row['pop_use2']);
									for($e=0; $e<count($exp4); $e++){
										$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp4[$e]}' ";
										$use2 = sql_fetch($sql_use);
								?>
								<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
								<?php }}?>

								<?php
									if($row['pop_use3']){
									$exp5 = explode("|", $row['pop_use3']);
									for($e=0; $e<count($exp5); $e++){
										$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp5[$e]}' ";
										$use3 = sql_fetch($sql_use);
								?>
								<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
								<?php }}?>						
							</ul>
							<button type="button" class="picked_delete" onClick="delete_li('local_sort_li');">
								<img src="<?php echo G5_THEME_IMG_URL?>/ic_trash.svg" alt="">
								<span>삭제</span>
							</button>
						</li>
						<?php }else if($row['am_area'] == 2){?>
							<?php 
								for($z=1; $z<=3; $z++){
								$fsMbIdx = $row['fs_mb_idx'.$z];
								if($fsMbIdx){
									$row5 = sql_fetch(" select * from g5_member where mb_no = '{$fsMbIdx}' ");
									$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row5['mb_fs_use1']}' ";
									$use1 = sql_fetch($sql_use);
									
									$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row5['mb_fs_use2']}' ";
									$use2 = sql_fetch($sql_use);

									$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row5['mb_fs_use3']}' ";
									$use3 = sql_fetch($sql_use);
									
									$ary1 = array();
									$ary2 = array();
									$sql_stadium = " select * from a_stadium where mb_idx = '{$row5['mb_no']}' and as_delete_st = 1 ";
									$result_stadium = sql_query($sql_stadium);
									for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
										$exp = explode("|", $stadium['as_to']);
										for($x=0; $x<count($exp); $x++){
											array_push($ary1, $exp[$x]);	
										}

										array_push($ary2, $stadium['as_sort']);
									}

									$ary1 = array_values(array_unique($ary1));
									$ary2 = (array_values(array_unique($ary2)));
									
									sort($ary1);
									sort($ary2);
							?>
							<li id="pick_list_<?php echo $row5['mb_no']?>">
								<input type="hidden" name="fs_idx[]" value="<?php echo $row5['mb_no']?>">
								<input type="hidden" name="fs_idx_name[]" value="<?php echo $row5['mb_fs_name']?>">
								<h3 class="ust_name"><?php echo $row['fs_mb_name'.$z]?></h3>
								<ul class="ust_sub_info">
									<li>
										<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
										<span><?php echo $row5['mb_fs_tel']?></span>
									</li>
									<li>
										<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
										<span>[<?php echo $row5['mb_fs_zip']?>] <?php echo $row5['mb_fs_addr1']?> <?php echo $row5['mb_fs_addr2']?></span>
									</li>
								</ul>
								<ul class="ust_sub_info2">
									<?php 
										for($s=0; $s<count($ary1); $s++){
											$toKey = array_search($ary1[$s], array_column($_cfg['stadium']['to'], 'val'));
									?>
									<li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li>
									<?php }?>
									<?php 
										for($s=0; $s<count($ary2); $s++){
											$sortKey = array_search($ary2[$s], array_column($_cfg['stadium']['sort'], 'val'));
									?>
									<li><?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?></li>
									<?php }?>
									<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
									<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
									<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
								</ul>
								<button type="button" class="picked_delete" onClick="delete_li('pick_list_<?php echo $row5['mb_no']?>');">
									<img src="<?php echo G5_THEME_IMG_URL?>/ic_trash.svg" alt="">
									<span>삭제</span>
								</button>
							</li>
							<?php }}?>
						<?php }?>
					</ul>
				</div>
			</li>
		</ul>

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">인원<span>*</span></p>
				<div class="regi_td">
					<ul class="st_frm_chk">
						<?php for($t=0; $t<count($_cfg['stadium']['to']); $t++){?>
						<li>							
							<input type="radio" name="am_to" id="am_to<?php echo $t?>" value="<?php echo $_cfg['stadium']['to'][$t]['val']?>" <?php if($t == 0 || $_cfg['stadium']['to'][$t]['val'] == $row['am_to']){ echo "checked";  }?> onChange="fnValueCount();">
							<label for="am_to<?php echo $t?>"><?php echo $_cfg['stadium']['to'][$t]['txt']?></label>
						</li>
						<?php }?>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">내기<span>*</span></p>
				<div class="regi_td">
					<select name="am_bet" id="am_bet" class="regi_ipt req_ipt regi_select2" onChange="fnValueCount();">
						<option value="1" <?php if($row['am_bet'] == 1){ echo "selected";  }?>>구장비</option>
						<option value="2" <?php if($row['am_bet'] == 2){ echo "selected";  }?>>음료수</option>
						<option value="0" <?php if($row['am_bet'] == 0){ echo "selected";  }?>>선택 안함</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">연령대<span>*</span></p>
				<div class="regi_td">
					<select name="am_age" id="am_age" class="regi_ipt req_ipt regi_select regi_select2" onChange="fnValueCount();">
						<option value="">연령대를 선택하세요.</option>
						<?php for($i=0; $i<count($_cfg['match']['age']); $i++){?>
						<option value="<?php echo $_cfg['match']['age'][$i]['val']?>" <?php if($row['am_age'] == $_cfg['match']['age'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['age'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">성별<span>*</span></p>
				<div class="regi_td">
					<select name="am_gender" id="am_gender" class="regi_ipt req_ipt regi_select regi_select2" onChange="fnValueCount();">
						<option value="">성별을 선택하세요.</option>
						<?php for($i=0; $i<count($_cfg['match']['gender']); $i++){?>
						<option value="<?php echo $_cfg['match']['gender'][$i]['val']?>" <?php if($row['am_gender'] == $_cfg['match']['gender'][$i]['val']){ echo "selected";  }?>>
							<?php echo $_cfg['match']['gender'][$i]['txt']?>
						</option>
						<?php }?>
					</select>
				</div>
			</li>
		</ul>

		<div class="fix_btn_back"></div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn fix_red" id="submit_button" onClick="register();">
				<?php if($w == "u"){ echo "리매치 수정"; }else{ echo "리매치 신청";  }?>
			</button>
		</div>

		<div id="filter_pop" class="cm_pop">
			<p class="cm_pop_back"></p>
			<div class="filter_pop_cont ver3">
				<p class="filter_pop_tit"></p>				
				<button type="button" class="filter_pop_off" onClick="cmPopOff('filter_pop');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>

				<ul class="regi_ul">
					<li class="regi_li">
						<p class="regi_th">하위 주소</p>
						<div class="regi_td">
							<select name="do_idx" id="do_idx" class="regi_ipt regi_select regi_select2 black">
								<option value="">하위 주소를 선택하세요.</option>
							</select>
						</div>
					</li>
					<li class="regi_li">
						<p class="regi_th">구장 크기</p>
						<div class="regi_td">
							<ul class="st_frm_chk">
								<?php 
									for($i=0; $i<count($_cfg['stadium']['to']); $i++){
										$chked = "";
										if($w == "u" && $row['pop_size']){											
											$exp = explode("|", $row['pop_size']);
											for($j=0; $j<count($exp); $j++){
												if($exp[$j] == $_cfg['stadium']['to'][$i]['val']){
													$chked = "checked";
													break;
												}
											}
										}
								?>
								<li>							
									<input type="checkbox" name="st_to[]" id="st_to_<?php echo $i?>" value="<?php echo $_cfg['stadium']['to'][$i]['val']?>" <?php echo $chked?>>
									<label for="st_to_<?php echo $i?>"><?php echo $_cfg['stadium']['to'][$i]['txt']?></label>
								</li>
								<?php }?>
							</ul>
						</div>
					</li>
					<li class="regi_li">
						<p class="regi_th">구장종류</p>
						<div class="regi_td">
							<ul class="st_frm_chk ver2">
								<?php
									for($i=0; $i<count($_cfg['stadium']['sort']); $i++){
										$chked = "";
										if($w == "u" && $row['pop_sort']){											
											$exp = explode("|", $row['pop_sort']);
											for($j=0; $j<count($exp); $j++){
												if($exp[$j] == $_cfg['stadium']['sort'][$i]['val']){
													$chked = "checked";
													break;
												}
											}
										}
								?>
								<li>							
									<input type="checkbox" name="st_sort[]" id="st_sort_<?php echo $i?>" value="<?php echo $_cfg['stadium']['sort'][$i]['val']?>" <?php echo $chked?>>
									<label for="st_sort_<?php echo $i?>"><?php echo $_cfg['stadium']['sort'][$i]['txt']?></label>
								</li>
								<?php }?>
							</ul>
						</div>
					</li>
					<li class="regi_li">
						<p class="regi_th">편의시설</p>
						<div class="regi_td">
							<ul class="st_frm_chk ver2">
								<?php 
									$sql_use = " select * from a_futsal_use where afu_type = 1 and afu_subject != '없음' ";
									$result_use = sql_query($sql_use);
									for($i=0; $use=sql_fetch_array($result_use); $i++){
										$chked = "";
										if($w == "u" && $row['pop_use1']){											
											$exp = explode("|", $row['pop_use1']);
											for($j=0; $j<count($exp); $j++){
												if($exp[$j] == $use['afu_idx']){
													$chked = "checked";
													break;
												}
											}
										}
								?>
								<li>
									<input type="checkbox" name="mb_fs_use1[]" id="mb_fs_use1_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php echo $chked?>>
									<label for="mb_fs_use1_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
								</li>
								<?php }?>
							</ul>
							<ul class="st_frm_chk ver2">
								<?php 
										$sql_use = " select * from a_futsal_use where afu_type = 2 and afu_subject != '없음' ";
										$result_use = sql_query($sql_use);
										for($i=0; $use=sql_fetch_array($result_use); $i++){
											$chked = "";
											if($w == "u" && $row['pop_use2']){												
												$exp = explode("|", $row['pop_use2']);
												for($j=0; $j<count($exp); $j++){
													if($exp[$j] == $use['afu_idx']){
														$chked = "checked";
														break;
													}
												}
											}
									?>
									<li>
										<input type="checkbox" name="mb_fs_use2[]" id="mb_fs_use2_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php echo $chked?>>
										<label for="mb_fs_use2_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
									</li>
									<?php }?>
							</ul>
							<ul class="st_frm_chk ver2">
								<?php 
										$sql_use = " select * from a_futsal_use where afu_type = 3 and afu_subject != '없음' ";
										$result_use = sql_query($sql_use);
										for($i=0; $use=sql_fetch_array($result_use); $i++){
											$chked = "";
											if($w == "u" && $row['pop_use3']){												
												$exp = explode("|", $row['pop_use3']);
												for($j=0; $j<count($exp); $j++){
													if($exp[$j] == $use['afu_idx']){
														$chked = "checked";
														break;
													}
												}
											}
									?>
									<li>
										<input type="checkbox" name="mb_fs_use3[]" id="mb_fs_use3_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php echo $chked?>>
										<label for="mb_fs_use3_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
									</li>
									<?php }?>
							</ul>
						</div>
					</li>
				</ul>
				<div class="fix_btn_box not_fix">
					<button type="button" class="fix_btn on" onClick="insertType();">적용</button>
				</div>
			</div>
		</div>
	</form>	
</div>

<form name="pick_frm" method="post">
<div class="cm_pop ver2" id="stadium_pop">
	<div class="header">
		<button type="button" class="back_btn" onClick="cmPopOff('stadium_pop');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_back.svg" alt=""></button>
		<p class="sub_title">검색결과</p>
	</div>
	<div class="cm_pop_cont ver3">
		<ul class="pop_sch_stadium"></ul>
	</div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn on" onClick="only3Pick();">적용</button>
	</div>
</div>
</form>

<script>
	$(function(){
		if($("#w").val() == "u"){
			$("#am_level").val("<?php echo $row['am_level']?>");
			$("#am_level").change();
			$("#sd_idx").val("<?php echo $row['sd_idx']?>");
			$("#sd_idx").change();
			$("#si_idx").val("<?php echo $row['si_idx']?>");
			$("#si_idx").change();
			$("#do_idx").val("<?php echo $row['do_idx']?>");
			$("#do_idx").change();
			$("#am_time").val("<?php echo $row['am_time']?>");
			$("#am_time").change();
			$("#am_bet").val("<?php echo $row['am_bet']?>");
			$("#am_bet").change();
			$("#am_age").val("<?php echo $row['am_age']?>");
			$("#am_age").change();
			$("#am_gender").val("<?php echo $row['am_gender']?>");
			$("#am_gender").change();
			fnValueCount();
		}
	})

	function chgSido(v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/sigugun_list.php",
			data: {sd_idx:v},
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$(".picked_stadium ").empty();
				$("#si_idx").empty().append(data);
				if(v == "36"){			
					$("#do_idx").empty().append(data);
				}
			}
		});
	}

	function chgSigugun(v){
		const sido_val = $("#sd_idx").val();
		if(sido_val != "36" && v){
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/dong_list.php",
				data: {si_idx:v},
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					$(".picked_stadium ").empty();
					$("#do_idx").empty().append(data);
				}
			});
		}
	}

	$('#show_date').datepicker({
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-20:c+20', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy-mm-dd", // 날짜의 형식
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
			$("#am_date").val(dateText);
			getYoil(dateText);
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});

	function getYoil(dateText){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getYoil.php",
			data: {dateText:dateText}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$('#show_date').val(data);
			}
		});
	}	

	function search_stadium(){
		if($("#sd_idx").val() == ""){
			showToast("시/도를 먼저 선택해 주세요.");
			return false;
		}

		if($("#sd_idx").val() != "36" && $("#si_idx").val() == ""){
			showToast("시/구/군을 먼저 선택해 주세요.");
			return false;
		}

		const area = $("input[name=am_area]:checked").val();
		if(area == "1"){
			$(".filter_pop_tit").text("지역 단위");
		}else if(area == "2"){
			$(".filter_pop_tit").text("구장 단위");
		}
		cmPopOn('filter_pop');
	}

	function insertType(){
		const area = $("input[name=am_area]:checked").val();
		let sizeKey = [];
		$("input[name='st_to[]']:checked").each(function(){ sizeKey.push($(this).val()); });
		$("#pop_size").val(sizeKey.join("|"));

		let sortKey = [];
		$("input[name='st_sort[]']:checked").each(function(){ sortKey.push($(this).val()); });
		$("#pop_sort").val(sortKey.join("|"));

		let use1Key = [];
		$("input[name='mb_fs_use1[]']:checked").each(function(){ use1Key.push($(this).val()); });
		$("#pop_use1").val(use1Key.join("|"));

		let use2Key = [];
		$("input[name='mb_fs_use2[]']:checked").each(function(){ use2Key.push($(this).val()); });
		$("#pop_use2").val(use2Key.join("|"));

		let use3Key = [];
		$("input[name='mb_fs_use3[]']:checked").each(function(){ use3Key.push($(this).val()); });
		$("#pop_use3").val(use3Key.join("|"));
		
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/match_stadium_list.php",
			data: {
				area: area,
				sd_idx : $("#sd_idx").val(),
				si_idx : $("#si_idx").val(),
				do_idx : $("#do_idx").val(),
				pop_size : $("#pop_size").val(),
				pop_sort : $("#pop_sort").val(),
				pop_use1 : $("#pop_use1").val(),
				pop_use2 : $("#pop_use2").val(),
				pop_use3 : $("#pop_use3").val(),
			}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(area == "1"){
					$(".picked_stadium").empty().append(data);
					fnValueCount();
				}else if(area == "2"){
					$(".pop_sch_stadium").empty().append(data);
					cmPopOn('stadium_pop');
				}
				cmPopOff('filter_pop');
			}
		});				
	}

	function pick_len(v){
		const pickLen = $("input[name='pick_chk[]']:checked").length;
		if(pickLen > 3){
			showToast("최대 3개까지만 선택할 수 있습니다.");
			$(`#pick_chk_${v}`).prop("checked", false);
			return false;
		}
	}

	function only3Pick(){
		const string = $("form[name=pick_frm]").serialize();
		const pickLen = $("input[name='pick_chk[]']:checked").length;	

		if(pickLen < 1){
			showToast("1~3개의 풋살장을 선택해 주세요.");
			return false;
		}else{
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/only3Pick.php",
				data: string, 
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					//console.log(data);
					$(".picked_stadium").empty().append(data);
					cmPopOff("stadium_pop");
					fnValueCount();
				}
			});
		}
	}

	function delete_li(id){
		const area = $("input[name=am_area]:checked").val();
		$("#"+id).remove();
		if(area == "1"){
			$(".hidden_ipt").val("");
		}else if(area == "2"){
			if($(".picked_stadium li").length < 1){
				$(".hidden_ipt").val("");
			}
		}
		fnValueCount();
	}

	function chgArea(v){
		$(".picked_stadium").empty();
		$(".hidden_ipt").val("");
	}

	function register(){
		const w = document.getElementById("w").value;
		const at_idx = document.getElementById("at_idx");	
		const am_level = document.getElementById("am_level");
		const sd_idx = document.getElementById("sd_idx");	
		const si_idx = document.getElementById("si_idx");
		const am_date = document.getElementById("am_date");
		const am_time = document.getElementById("am_time");
		const am_area = $("input[name=am_area]:checked").val();
		const am_to = $("input[name=am_to]:checked").val();
		const am_bet = document.getElementById("am_bet");
		const am_age = document.getElementById("am_age");
		const am_gender = document.getElementById("am_gender");

		if(at_idx.value == ""){ showToast("팀을 선택해 주세요."); remove_active(); return false; }
		if(am_level.value == ""){ showToast("매치 레벨을 선택해 주세요."); remove_active(); return false; }
		if(sd_idx.value == ""){ showToast("지역(시/도)을 선택해 주세요."); remove_active(); return false; }
		if(sd_idx.value == "36"){ 
		}else{
			if(si_idx.value == ""){ showToast("풋살장 지역(시/구/군)을 선택해 주세요."); remove_active(); return false; }
		}
		if(am_date.value == ""){ showToast("일시를 선택해 주세요."); remove_active(); return false; }
		if(am_time.value == ""){ showToast("일시를 선택해 주세요."); remove_active(); return false; }
		if(am_area == ""){ showToast("매치장소를 선택해 주세요."); remove_active(); return false; }
		if($(".picked_stadium li").length < 1){ showToast("매치장소를 검색해 주세요."); remove_active(); return false; }
		if(am_to == ""){ showToast("인원을 선택해 주세요."); remove_active(); return false; }
		if(am_bet.value == ""){ showToast("내기를 선택해 주세요."); remove_active(); return false; }
		if(am_age.value == ""){ showToast("연령대를 선택해 주세요."); remove_active(); return false; }
		if(am_gender.value == ""){ showToast("성별을 선택해 주세요."); remove_active(); return false; }

		const wr_frm = $("form[name=wr_frm]").serialize();
		$("#submit_button").attr("disabled", true);

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/match_write_update.php",
			data: wr_frm, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				console.log(data);
				if(data == "1111"){
					location.href = "<?php echo G5_URL?>/user/";
				}else if(data == "1112"){
					showToast("수정이 완료되었습니다.");
				}
				$("#submit_button").attr("disabled", false);
			}
		});
	}

	$(".req_ipt").keyup(function(e) {
		fnValueCount();
	});

	function fnValueCount(){
		let reqCnt = ($(".req_ipt").length)+3;
		let reqCurCnt = 0;
		$(".req_ipt").each(function(){
			if($(this).val() != ""){
				reqCurCnt++;
			}
		});

		if($("#sd_idx").val() == "36"){
			reqCnt = ($(".req_ipt").length)+2;
		}

		if($('input:radio[name=am_area]').is(':checked') !== false){ reqCurCnt++; }
		if($('input:radio[name=am_to]').is(':checked') !== false){ reqCurCnt++; }
		if($('.picked_stadium li').length > 0){ reqCurCnt++; }

		//console.log(reqCurCnt+"//"+reqCnt);
		if(reqCurCnt >= reqCnt){
			$("#submit_button").addClass("on");
		}else{
			$("#submit_button").removeClass("on");
		}
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>