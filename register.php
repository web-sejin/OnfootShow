<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	if($is_member){
		$lat = $member['mb_lat'];
		$lng = $member['mb_lng'];
	}else{
		$lat = $_SESSION['lat'];
		$lng = $_SESSION['lng'];
	}
?>

<div class="regi_area cm_padd2">
	<form name="regi_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<!--input type="text" name="mb_lat" id="mb_lat" value="<?php echo $lat?>">
		<input type="text" name="mb_lng" id="mb_lng" value="<?php echo $lng?>"-->
		<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
		<input type="hidden" name="app_chk" id="app_chk" value="<?php echo $_SESSION['appChk']?>" readonly>
		<input type="hidden" name="mb_token" id="mb_token" value="<?php echo $_SESSION['appToken']?>" readonly>
		<input type="hidden" id="chk_id" value="<?php if($is_member){?>1<?php }else{?>0<?php }?>" readonly>
		<input type="hidden" name="mb_fs_lat" id="mb_fs_lat" value="<?php echo $member['mb_fs_lat']?>" readonly>
		<input type="hidden" name="mb_fs_lng" id="mb_fs_lng" value="<?php echo $member['mb_fs_lng']?>" readonly>
		<input type="hidden" name="mb_type" id="mb_type" value="2" readonly>

		<?php if($is_member){?>
		<input type="hidden" id="base_hp" value="<?php echo $member['mb_hp']?>">
		<input type="hidden" id="base_email" value="<?php echo $member['mb_email']?>">
		<input type="hidden" id="first_img" value="1">
		<input type="hidden" id="bs_img" value="1">
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
				<p class="regi_th">아이디<span>*</span></p>
				<?php if($is_member){?>
				<div class="regi_td">
					<input type="text" name="mb_id" id="mb_id" class="regi_ipt" value="<?php echo $member['mb_id']?>" readonly>
				</div>
				<?php }else{?>
				<div class="regi_td regi_td_flex">
					<input type="text" name="mb_id" id="mb_id" class="regi_ipt ver2 req_ipt" placeholder="아이디를 입력해 주세요." value="<?php echo $member['mb_id']?>">
					<button type="button" class="regi_td_btn" onClick="check_id()">중복확인</button>
				</div>
				<?php }?>
			</li>
			<li class="regi_li">
				<p class="regi_th">비밀번호<span>*</span></p>
				<div class="regi_td">
					<div class="regi_box">
						<input type="password" name="mb_password" id="mb_password" class="regi_ipt req_ipt" placeholder="영문/숫자 조합 8자 이상">
					</div>
					<div class="regi_box">
						<input type="password" name="mb_password_re" id="mb_password_re" class="regi_ipt req_ipt" placeholder="비밀번호 재입력">
					</div>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">이메일<span>*</span></p>
				<div class="regi_td">
					<input type="text" name="mb_email" id="mb_email" class="regi_ipt req_ipt" placeholder="이메일을 입력해 주세요." value="<?php echo $member['mb_email']?>">	
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 명<span>*</span></p>
				<div class="regi_td">
					<input type="text" name="mb_fs_name" id="mb_fs_name" class="regi_ipt req_ipt" placeholder="풋살장 명을 입력해 주세요." value="<?php echo $member['mb_fs_name']?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 전화번호<span>*</span></p>
				<div class="regi_td">
					<input type="text" name="mb_fs_tel" id="mb_fs_tel" class="regi_ipt req_ipt" placeholder="풋살장 전화번호를 입력해 주세요." value="<?php echo $member['mb_fs_tel']?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 지역<span>*</span></p>
				<div class="regi_td regi_td_flex">
					<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select ver6" onchange="chgSido(this.value); fnValueCount();">
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
					<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select ver6" onChange="chgSigugun(this.value); fnValueCount();">
						<option value="">시/구/군</option>
					</select>
					<select name="do_idx" id="do_idx" class="regi_ipt req_ipt regi_select ver6" onChange="fnValueCount();">
						<option value="">읍/면/동</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 주소<span>*</span></p>
				<div class="regi_td">
					<div class="regi_box regi_td_flex">
						<input type="text" name="mb_fs_zip" id="mb_fs_zip" class="regi_ipt ver2 req_ipt" placeholder="우편번호" readonly value="<?php echo $member['mb_fs_zip']?>" onClick="sample3_execDaumPostcode()">
						<button type="button" class="regi_td_btn" onClick="sample3_execDaumPostcode()">검색</button>						
					</div>					
					<div class="regi_box">
						<input type="text" name="mb_fs_addr1" id="mb_fs_addr1" class="regi_ipt req_ipt readonly" readonly placeholder="기본주소" value="<?php echo $member['mb_fs_addr1']?>" onClick="sample3_execDaumPostcode()">

						<div id="wrap" style="display:none;border:1px solid #000;width:100%;height:300px;margin:10px 0 0;position:relative">
							<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode()" alt="접기 버튼">
						</div>
					</div>
					<div class="regi_box">
						<input type="text" name="mb_fs_addr2" id="mb_fs_addr2" class="regi_ipt req_ipt" placeholder="상세주소" value="<?php echo $member['mb_fs_addr2']?>">
					</div>
					<div class="regi_box">
						<input type="text" name="mb_fs_addr3" id="mb_fs_addr3" class="regi_ipt" placeholder="참고항목" value="<?php echo $member['mb_fs_addr3']?>">
					</div>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">편의 시설 <span>*</span></p>
				<div class="regi_td">
					<div class="regi_use">
						<p class="regi_use_tit">화장실</p>
						<ul class="regi_radio_ul">
							<?php 
								$sql_use = " select * from a_futsal_use where afu_type = 1 ";
								$result_use = sql_query($sql_use);
								for($i=0; $use=sql_fetch_array($result_use); $i++){
							?>
							<li>
								<input type="radio" name="mb_fs_use1" id="mb_fs_use1_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php if((!$w && $i==0) || $member['mb_fs_use1'] == $use['afu_idx']){ echo "checked";  }?>>
								<label for="mb_fs_use1_<?php echo $i?>"><?php echo $use['afu_subject']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="regi_use">
						<p class="regi_use_tit">샤워실</p>
						<ul class="regi_radio_ul">
							<?php 
								$sql_use = " select * from a_futsal_use where afu_type = 2 ";
								$result_use = sql_query($sql_use);
								for($i=0; $use=sql_fetch_array($result_use); $i++){
							?>
							<li>
								<input type="radio" name="mb_fs_use2" id="mb_fs_use2_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php if((!$w && $i==0) || $member['mb_fs_use2'] == $use['afu_idx']){ echo "checked";  }?>>
								<label for="mb_fs_use2_<?php echo $i?>"><?php echo $use['afu_subject']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="regi_use">
						<p class="regi_use_tit">주차장</p>
						<ul class="regi_radio_ul">
							<?php 
								$sql_use = " select * from a_futsal_use where afu_type = 3 ";
								$result_use = sql_query($sql_use);
								for($i=0; $use=sql_fetch_array($result_use); $i++){
							?>
							<li>
								<input type="radio" name="mb_fs_use3" id="mb_fs_use3_<?php echo $i?>" value="<?php echo $use['afu_idx']?>" <?php if((!$w && $i==0) || $member['mb_fs_use3'] == $use['afu_idx']){ echo "checked";  }?>>
								<label for="mb_fs_use3_<?php echo $i?>"><?php echo $use['afu_subject']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">운영시간<span>*</span></p>
				<div class="regi_td frm_btn_flex regi_td_time">
					<select name="mb_fs_start" id="mb_fs_start" class="regi_ipt req_ipt regi_select regi_time ver3" onChange="fnValueCount();">
						<option value="">시작 시간</option>
						<?php for($i=0; $i<25; $i++){?>
						<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
						<?php }?>
					</select>
					<span>~</span>
					<select name="mb_fs_end" id="mb_fs_end" class="regi_ipt req_ipt regi_select regi_time ver3" onChange="fnValueCount();">
						<option value="">종료 시간</option>
						<?php for($i=1; $i<26; $i++){?>
						<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
						<?php }?>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">
					구장<span>*</span>
					<strong>* 등록 순으로 배치됩니다.</strong>
				</p>
				<div class="regi_td">
					<button type="button" class="frm_btn ver1 ver3" onClick="cmPopOn('stadium_pop');">구장 등록</button>
					<ul class="stadium_list" id="stadium_list">
						<?php
							$sql_st = " select count(*) cnt from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 ";
							$row_st = sql_fetch($sql_st);
							
							$sql_st = " select * from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 order by as_idx asc ";
							$result_st = sql_query($sql_st);
							for($i=0; $stad=sql_fetch_array($result_st); $i++){
						?>
						<li class="stadium_li stadium_org" id="range_<?php echo $i?>" data-idx="<?php echo $stad['as_idx']?>">
							<span onClick="infoOpenStadium('<?php echo $i?>');"><?php echo $stad['as_name']?></span>
							<button type="button" onclick="stadiumDel('<?php echo $i?>')">
								<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
							</button>
						</li>
						<?php }?>
					</ul>
					<p class="not_stadium" <?php if($row_st['cnt'] > 0){?>style="display:none;"<?php }?>>구장을 1개 이상 등록해 주세요.</p>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 소개<span>*</span></p>
				<div class="regi_td">
					<textarea name="mb_fs_content" id="mb_fs_content" class="regi_ipt req_ipt regi_txtarea" placeholder="내용을 입력하세요."><?php echo $member['mb_fs_content']?></textarea>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">환불 정책<span>*</span></p>
				<div class="regi_td">
					<textarea name="mb_fs_refund" id="mb_fs_refund" class="regi_ipt req_ipt regi_txtarea" placeholder="내용을 입력하세요."><?php echo $member['mb_fs_refund']?></textarea>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">풋살장 사진<span>*</span></p>				
				<div class="regi_td">
					<div class="pic_box">
						<ul class="pic_list" id="pic_list">
							<?php if($is_member){?>
								<?php
									$thumCnt = 1;
									$sql_thum = " select * from a_futsal_img where mb_idx = '{$member['mb_no']}' order by img_od asc ";
									$result_thum = sql_query($sql_thum);
									for($i=0; $thum=sql_fetch_array($result_thum); $i++){
										$thumCnt++;
										$od = $thum['img_od'];
								?>
								<li id="pic_li_<?php echo $od?>" class="pic_li_org" data-idx="<?php echo $thum['img_idx']?>">									
									<input type="file" name="pic_img_<?php echo $od?>" id="pic_img_<?php echo $od?>" onchange="readURL(this, 'pic', '<?php echo $od?>');" accept="image/*"> 									
									<label for="pic_img_<?php echo $od?>">
										<img id="pic_<?php echo $od?>" src="<?php echo G5_DATA_URL?>/footsalFile/<?php echo $thum['img_af']?>" />
										<strong><span><?php echo $od?></span> / 5</strong>
									</label>
									<button type="button" class="pic_<?php echo $od?>_del" onClick="picDel('pic', '<?php echo $od?>');" style="display:block;">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
								<?php }?>
								<li id="pic_li_<?php echo $thumCnt?>">
									<input type="file" name="pic_img_<?php echo $thumCnt?>" id="pic_img_<?php echo $thumCnt?>" onchange="readURL(this, 'pic', '<?php echo $thumCnt?>');" accept="image/*"> 
									<label for="pic_img_<?php echo $thumCnt?>">
										<img id="pic_<?php echo $thumCnt?>" />
										<strong><span><?php echo $thumCnt?></span> / 5</strong>
									</label>
									<button type="button" class="pic_<?php echo $thumCnt?>_del" onClick="picDel('pic', '<?php echo $thumCnt?>');">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
							<?php }else{?>
								<li id="pic_li_1">
									<input type="file" name="pic_img_1" id="pic_img_1" onchange="readURL(this, 'pic', '1');" accept="image/*"> 
									<label for="pic_img_1">
										<img id="pic_1" />
										<strong><span>1</span> / 5</strong>
									</label>
									<button type="button" class="pic_1_del" onClick="picDel('pic', '1');">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
							<?php }?>
						</ul>
					</div>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">사업자 등록증<span>*</span></p>
				<div class="regi_td">
					<ul class="pic_list">
						<li>
							<input type="file" name="mb_fs_bs" id="mb_fs_bs" onchange="readURL2(this);" accept="image/*"> 
							<label for="mb_fs_bs" class="ver2">
								<?php if($member['mb_fs_bs_af'] != ""){?>
								<img id="bs_preview" src="<?php echo G5_DATA_URL?>/footsalFile/<?php echo $member['mb_fs_bs_af']?>" />
								<?php }else{?>
								<img id="bs_preview" />
								<?php }?>
							</label>
							<button type="button" class="bs_del" onClick="picDel2();" <?php if($member['mb_fs_bs_af'] != ""){?>style="display:block;"<?php }?>>
								<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
							</button>
						</li>
					</ul>
				</div>
			</li>
		</ul>	

		<?php if($is_member){?>
		<button type="button" class="leave_btn" onClick="cmPopOn('leave_pop');">회원탈퇴</button>
		<?php }?>

		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button" onClick="registerChk();">
				<?php if($is_member){ echo "정보수정"; }else{ echo "회원가입"; }?>
			</button>
		</div>
	</form>
</div>
<div class="fix_btn_back"></div>

<div class="cm_pop" id="prv_pop">
	<p class="cm_pop_back" onClick="cmPopOff('prv_pop');"></p>
	<div class="prv_pop_cont">
		<ul class="regi_prv_list">
			<li>
				<p class="regi_prv regi_prv_all">
					<input type="checkbox" id="chk_all">
					<label for="chk_all">아래 약관에 모두 동의합니다.</label>
				</p>
			</li>
			<li>
				<p class="regi_prv" onClick="prvContOn('provision', 'chk1');">
					<input type="checkbox" name="chk1" id="chk1" class="chk_box" onChange="goActiveChk();">
					<label for="chk1">이용약관에 동의합니다.</label>
				</p>
			</li>
			<li>
				<p class="regi_prv" onClick="prvContOn('privacy', 'chk2');">
					<input type="checkbox" name="chk2" id="chk2" class="chk_box" onChange="goActiveChk();">
					<label for="chk2">개인정보취급방침에 동의합니다.</label>
				</p>
			</li>
		</ul>
		<button type="button" class="fix_btn fix_next" onClick="registerProcess();">다음</button>
	</div>
</div>

<form name="stadium_frm" method="post">
	<div class="cm_pop ver2" id="stadium_pop">
		<div class="header">
			<button type="button" class="back_btn" onClick="offStadiumRes();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
			<p class="sub_title">구장등록</p>
		</div>
		<div class="cm_pop_cont">
			<ul class="regi_ul">
				<li class="regi_li">
					<p class="regi_th">구장명</p>
					<div class="regi_td">
						<input type="text" name="st_name" id="st_name" class="regi_ipt st_ipt" placeholder="구장명을 입력하세요. (최대 4글자)" maxlength="4">
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">구장크기</p>
					<div class="regi_td">
						<input type="text" name="st_size" id="st_size" class="regi_ipt st_ipt" placeholder="구장 크기를 입력하세요. (형식-40x20m)">
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">구장인원</p>
					<div class="regi_td">
						<ul class="st_frm_chk">
							<?php for($i=0; $i<count($_cfg['stadium']['to']); $i++){?>
							<li>							
								<input type="checkbox" name="st_to[]" id="st_to_<?php echo $i?>" value="<?php echo $_cfg['stadium']['to'][$i]['val']?>" onChange="fnValueCount2();">
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
							<?php for($i=0; $i<count($_cfg['stadium']['sort']); $i++){?>
							<li>							
								<input type="radio" name="st_sort" id="st_sort_<?php echo $i?>" value="<?php echo $_cfg['stadium']['sort'][$i]['val']?>" onChange="fnValueCount2();">
								<label for="st_sort_<?php echo $i?>"><?php echo $_cfg['stadium']['sort'][$i]['txt']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">바닥재</p>
					<div class="regi_td">
						<ul class="st_frm_chk ver2">
							<?php for($i=0; $i<count($_cfg['stadium']['floor']); $i++){?>
							<li>							
								<input type="radio" name="st_floor" id="st_floor_<?php echo $i?>" value="<?php echo $_cfg['stadium']['floor'][$i]['val']?>" onChange="fnValueCount2();">
								<label for="st_floor_<?php echo $i?>"><?php echo $_cfg['stadium']['floor'][$i]['txt']?></label>
							</li>
							<?php }?>
						</ul>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">구장이용료/시간</p>
					<div class="regi_td">
						<input type="tel" name="st_price" id="st_price" class="regi_ipt st_ipt ver4" onkeyup="inputNumberFormat(this)">
						<span class="regi_unit">원</span>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">고정팀 구장이용료/시간</p>
					<div class="regi_td">
						<input type="tel" name="st_price2" id="st_price2" class="regi_ipt st_ipt ver4" onkeyup="inputNumberFormat(this)">
						<span class="regi_unit">원</span>
					</div>
				</li>
			</ul>
		</div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button2" onClick="stadiumList();">등록</button>
		</div>
	</div>
</form>

<div id="complete_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">가입이 완료되었습니다.<br>가입 완료 승인 기간은 2~3일 소요됩니다.</p>
		<a href="<?php echo G5_BBS_URL?>/login.php" class="cm_pop_btn">확인</a>
	</div>
</div>

<div id="delete_confirm_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">기존에 등록한 항목은 바로 삭제됩니다.<br>삭제하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('delete_confirm_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="del_cofirm_btn" onClick="">확인</button>
		</div>
	</div>
</div>

<div id="leave_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">탈퇴하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="memberLeave();">확인</button>
		</div>
	</div>
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
	$(function(){
		if($("#w").val() == "u"){
			$("#sd_idx").val("<?php echo $member['sd_idx']?>");
			$("#sd_idx").change();
			$("#si_idx").val("<?php echo $member['si_idx']?>");
			$("#si_idx").change();
			$("#do_idx").val("<?php echo $member['do_idx']?>");
			$("#do_idx").change();
			$("#mb_fs_start").val("<?php echo $member['mb_fs_start']?>");
			$("#mb_fs_start").change();
			$("#mb_fs_end").val("<?php echo $member['mb_fs_end']?>");
			$("#mb_fs_end").change();
		}
	});
	function stadiumList(){		
		if($("#st_name").val() == ""){ showToast("구장명을 입력해 주세요."); return false; }
		if($("#st_size").val() == ""){ showToast("구장크기를 입력해 주세요."); return false; }		
		if($("input[name='st_to[]']:checked").length < 1){ showToast("구장인원을 선택해 주세요."); return false; }		
		if($('input:radio[name=st_sort]').is(':checked') === false){ showToast("구장종류를 선택해 주세요."); return false; }
		if($('input:radio[name=st_floor]').is(':checked') === false){ showToast("바닥재를 선택해 주세요."); return false; }
		if($("#st_price").val() == ""){ showToast("시간당 구장이용료를 입력해 주세요."); return false; }
		if($("#st_price2").val() == ""){ showToast("시간당  고정팀 구장이용료를 입력해 주세요."); return false; }

		const string = $("form[name=stadium_frm]").serialize();
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/regi_stadium_list.php",
			data: string, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#stadium_list").append(data);
				listRange();
				cmPopOff('stadium_pop');
				$(".st_ipt").val("");
				$(".st_frm_chk input").prop("checked", false);
				fnValueCount();
			}
		});
	}

	function deleteOk(idx, type, v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/delete_member_info.php",
			data: {idx:idx, type:type}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				if(type == "stadium"){
					$(`#range_${v}`).remove();
					listRange();
				}else if(type == "thumbnail"){
					if(v == "1"){
						document.getElementById('pic_'+v).src = "";
						$("#pic_img_"+v).val("");
						$(".pic_"+v+"_del").hide();
						if($("#w").val() == "u"){ $("#first_img").val(0); }
					}else{
						$("#pic_li_"+v).remove();
						picRefresh();
					}
				}
				cmPopOff("delete_confirm_pop");
			}
		});		
	}

	function stadiumDel(v){
		const has = $(`#range_${v}`).hasClass("stadium_org");
		if(has){
			const as_idx = $(`#range_${v}`).attr("data-idx");
			$("#del_cofirm_btn").attr("onClick", `deleteOk('${as_idx}', 'stadium', '${v}');`);
			cmPopOn("delete_confirm_pop");
		}else{
			$(`#range_${v}`).remove();
			listRange();
		}
	}

	function listRange(){
		let cnt = 0;
		$(".stadium_li").each(function(i){
			$(this).attr("id", "range_"+i);
			$(this).children("button").attr("onClick", "stadiumDel('"+i+"')");
			$(this).attr("id", "range_"+i).children("span").attr("onClick", "infoOpenStadium('"+i+"')");
			cnt ++;
		});

		if(cnt > 0){
			$(".not_stadium").hide();
		}else{
			$(".not_stadium").show();
		}
	}

	function infoOpenStadium(v){
		const has = $(`#range_${v}`).hasClass("stadium_org");
		if(has){
			const has_idx = $(`#range_${v}`).attr("data-idx");
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/infoOpenStadium.php",
				data: {v : has_idx},
				dataType : "json",
				cache: false,
				async: false,
				//contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					const v1 = $(`#range_${v}`).children("input[name='rg_st_name[]']").val();
					const v2 = $(`#range_${v}`).children("input[name='rg_st_size[]']").val();
					const v3 = $(`#range_${v}`).children("input[name='rg_st_to[]']").val();
					const v4 = $(`#range_${v}`).children("input[name='rg_st_sort[]']").val();
					const v5 = $(`#range_${v}`).children("input[name='rg_st_floor[]']").val();
					const v6 = $(`#range_${v}`).children("input[name='st_price[]']").val();
					const v7 = $(`#range_${v}`).children("input[name='st_price2[]']").val();

					$("#st_name").val(data.v1);
					$("#st_size").val(data.v2);
					
					const v3_splt = data.v3.split("|");
					for(var v=0; v<v3_splt.length; v++){
						$("input:checkbox[name='st_to[]'][value='"+v3_splt[v]+"']").prop('checked', true);
					}

					$("input:radio[name='st_sort']:radio[value='"+data.v4+"']").prop('checked', true);
					$("input:radio[name='st_floor']:radio[value='"+data.v5+"']").prop('checked', true);
					$("#st_price").val(data.v6);
					$("#st_price2").val(data.v7);
				}
			});
		}else{
			const v1 = $(`#range_${v}`).children("input[name='rg_st_name[]']").val();
			const v2 = $(`#range_${v}`).children("input[name='rg_st_size[]']").val();
			const v3 = $(`#range_${v}`).children("input[name='rg_st_to[]']").val();
			const v4 = $(`#range_${v}`).children("input[name='rg_st_sort[]']").val();
			const v5 = $(`#range_${v}`).children("input[name='rg_st_floor[]']").val();
			const v6 = $(`#range_${v}`).children("input[name='st_price[]']").val();
			const v7 = $(`#range_${v}`).children("input[name='st_price2[]']").val();

			$("#st_name").val(v1);
			$("#st_size").val(v2);
			
			const v3_splt = v3.split("|");
			for(var v=0; v<v3_splt.length; v++){
				$("input:checkbox[name='st_to[]'][value='"+v3_splt[v]+"']").prop('checked', true);
			}

			$("input:radio[name='st_sort']:radio[value='"+v4+"']").prop('checked', true);
			$("input:radio[name='st_floor']:radio[value='"+v5+"']").prop('checked', true);
			$("#st_price").val(v6);
			$("#st_price2").val(v7);
		}
		
		$("#stadium_pop .fix_btn_box").hide();
		cmPopOn("stadium_pop");
	}

	function readURL(input, type, v) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById(type+'_'+v).src = e.target.result;
				$("."+type+"_"+v+"_del").show();
				addPic((v*1)+1);
			};
			reader.readAsDataURL(input.files[0]);
			if($("#w").val() == "u"){
				if(v == "1"){ $("#first_img").val(1); }
			}
		} else {
			document.getElementById(type+'_'+v).src = "";
			$("."+type+"_"+v+"_del").hide();		
			if($("#w").val() == "u"){
				if(v == "1"){ $("#first_img").val(0); }
			}
		}
		fnValueCount();
	}

	function picDel(type, v){
		const has = $(`#pic_li_${v}`).hasClass("pic_li_org");
		if(has){
			const img_idx = $(`#pic_li_${v}`).attr("data-idx");
			$("#del_cofirm_btn").attr("onClick", `deleteOk('${img_idx}', 'thumbnail', '${v}');`);
			cmPopOn("delete_confirm_pop");
		}else{
			if(v == "1"){
				document.getElementById(type+'_'+v).src = "";
				$("#"+type+"_img_"+v).val("");
				$("."+type+"_"+v+"_del").hide();
			}else{
				$("#"+type+"_li_"+v).remove();
				picRefresh();
			}
		}
	}

	function addPic(v){
		const pic_len = $("#pic_list li").length;
		if(v > pic_len){
			let cont = "";
			cont += `<li id="pic_li_${v}">`;
			cont += `<input type="file" name="pic_img_${v}" id="pic_img_${v}" onchange="readURL(this, 'pic', '${v}');"> `;
			cont += `<label for="pic_img_${v}">`;
			cont += `<img id="pic_${v}" />`;
			cont += `<strong><span>${v}</span> / 5</strong>`;
			cont += `</label>`;
			cont += `<button type="button" class="pic_${v}_del" onClick="picDel('pic', '${v}');">`;
			cont += `<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">`;
			cont += `</button>`;
			cont += `</li>`;

			$("#pic_list").append(cont);
		}
	}

	function picRefresh(){
		$("#pic_list li").each(function(i){
			const od = i+1;
			$(this).attr("id", "pic_li_"+od);
			$(this).children("input[type=file]").attr("name", `pic_img_${od}`);
			$(this).children("input[type=file]").attr("id", `pic_img_${od}`);
			$(this).children("input[type=file]").attr("onchange", `readURL(this, 'pic', '${od}');`);
			$(this).children("label").attr("for", `pic_img_${od}`);
			$(this).children("label").children("img").attr("id", `pic_${od}`);
			$(this).children("label").children("strong").html(`<span>${od}</span> / 5`);
			$(this).children("button").attr("class", `pic_${od}_del`);
			$(this).children("button").attr("onClick", `picDel('pic', '${od}');`);
		})
	}

	function readURL2(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('bs_preview').src = e.target.result;
				$(".bs_del").show();
				if($("#w").val() == "u"){ $("#bs_img").val(1); }
			};
			reader.readAsDataURL(input.files[0]);
		} else {
			document.getElementById('bs_preview').src = "";
			$(".bs_del").hide();
			if($("#w").val() == "u"){ $("#bs_img").val(0); }
		}
		fnValueCount();
	}

	function picDel2(type, v){
		document.getElementById("bs_preview").src = "";
		$("#mb_fs_bs").val("");
		$(".bs_del").hide();
		if($("#w").val() == "u"){ $("#bs_img").val(0); }
	}

	window.addEventListener('load', function(){			
		if (navigator.geolocation) {
			// GeoLocation을 이용해서 접속 위치를 얻어옵니다
			navigator.geolocation.getCurrentPosition(function(position) {
				var lat = position.coords.latitude; // 위도
				var lon = position.coords.longitude; // 경도
				//latlng(lat, lon);
			});
		} else { // HTML5의 GeoLocation을 사용할 수 없을때 마커 표시 위치와 인포윈도우 내용을 설정합니다
			var lat = "37.56677461682319"; // 위도
			var lon = "126.97848107235482"; // 경도
			//latlng(lat, lon);
		}
	});

	function latlng(lat, lon){
		$("#mb_lat").val(lat);
		$("#mb_lng").val(lon);
	}

	$("#mb_id").keyup(function(e) { 
		$("#chk_id").val('0');
	});

	//아이디 중복검사
	function check_id(){
		const id = document.getElementById("mb_id");
		const chk_id = document.getElementById("chk_id");

		if(id.value == ""){ showToast("아이디를 입력해 주세요."); remove_active(); return false; }
		//if(id.value.length < 6 || id.value.length >10){ showToast("아이디는 영문/숫자 조합으로 6~10자리로 입력해 주세요."); remove_active();	return false; }

		var idNum = (id.value).search(/[0-9]/g);
		var idEng = (id.value).search(/[a-z]/ig);
		var idSpe = (id.value).search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);
		//if(idEng < 0 || idNum < 0 || idSpe >= 0){
		if(idSpe >= 0){
			//showToast("아이디는 영문/숫자 조합으로 6~10자리로 입력해 주세요."); remove_active();	 return false;
		}

		const idStatus = fnidChk(id.value, "mb_id");		
		if(!idStatus){ 
			showToast("이미 가입된 아이디 입니다."); 
			remove_active(); 
			chk_id.value = "0";
			return false; 
		}else{
			showToast("사용 가능한 아이디 입니다.");
			chk_id.value = "1";
			//fnValueCount();
		}
	}

	function registerChk(){
		const w = document.getElementById("w").value;
		const name = document.getElementById("mb_name");	
		const hp = document.getElementById("mb_hp");
		const id = document.getElementById("mb_id");
		const chk_id = document.getElementById("chk_id");
		const pw = document.getElementById("mb_password");
		const pw2 = document.getElementById("mb_password_re");
		const email = document.getElementById("mb_email");
		const fs_name = document.getElementById("mb_fs_name");
		const fs_tel = document.getElementById("mb_fs_tel");	
		const sd_idx = document.getElementById("sd_idx");	
		const si_idx = document.getElementById("si_idx");	
		const do_idx = document.getElementById("do_idx");
		const fs_zip = document.getElementById("mb_fs_zip");	
		const fs_addr1 = document.getElementById("mb_fs_addr1");	
		const fs_addr2 = document.getElementById("mb_fs_addr2");	
		const fs_start = document.getElementById("mb_fs_start");
		const fs_end = document.getElementById("mb_fs_end");
		const stadium_cnt = $("#stadium_list li").length;
		const fs_content = document.getElementById("mb_fs_content");
		const fs_refund = document.getElementById("mb_fs_refund");
		const pic_img_1 = document.getElementById("pic_img_1");
		const fs_bs = document.getElementById("mb_fs_bs");	

		if(name.value == ""){ showToast("이름을 입력해 주세요."); remove_active(); return false; }
	
		if(hp.value == ""){ showToast("핸드폰 번호를 입력해 주세요."); remove_active(); return false; }
		if((hp.value).length != 13){ showToast("핸드폰 번호를 정확히 입력해 주세요."); remove_active(); return false; }
		let hpStatus = fnidChk(hp.value, "mb_hp", "2");
		if(w == "u" && $("#base_hp").val() == hp.value){ hpStatus = true; }
		if(!hpStatus){ showToast("이미 사용중인 핸드폰 번호 입니다."); remove_active(); return false; }
		
		if(w != "u"){
			if(id.value == ""){ showToast("아이디를 입력해 주세요."); remove_active(); return false; }
			if(chk_id.value != "1"){ showToast("아이디 중복확인을 완료해 주세요."); remove_active();	 return false; }
			
			if(pw.value == ""){ showToast("비밀번호를 입력해 주세요."); remove_active();	 return false; }

			var num = (pw.value).search(/[0-9]/g);
			var eng = (pw.value).search(/[a-z]/ig);
			var spe = (pw.value).search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

			if(spe >= 0){
				showToast("비밀번호는 영문, 숫자만 조합해 8자리 이상 입력해 주세요."); remove_active();	return false; 
			}else{
				if(pw.value.length < 8 || num < 0 || eng < 0){
					showToast("비밀번호는 영문, 숫자를 조합해 8자리 이상 입력해 주세요."); remove_active();	 return false; 
				}
			}
			if(pw2.value == ""){ showToast("비밀번호를 다시 한 번 입력해 주세요."); remove_active();	 return false; }
			if(pw.value != pw2.value){ showToast("동일한 비밀번호를 입력해 주세요."); remove_active(); return false; }
		}else{
			if(pw.value != ""){
				var num = (pw.value).search(/[0-9]/g);
				var eng = (pw.value).search(/[a-z]/ig);
				var spe = (pw.value).search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

				if(spe >= 0){
					showToast("비밀번호는 영문, 숫자만 조합해 8자리 이상 입력해 주세요."); remove_active();	return false; 
				}else{
					if(pw.value.length < 8 || num < 0 || eng < 0){
						showToast("비밀번호는 영문, 숫자를 조합해 8자리 이상 입력해 주세요."); remove_active();	 return false; 
					}
				}
				if(pw2.value == ""){ showToast("비밀번호를 다시 한 번 입력해 주세요."); remove_active();	 return false; }
				if(pw.value != pw2.value){ showToast("동일한 비밀번호를 입력해 주세요."); remove_active(); return false; }
			}
		}

		var emailChk = /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
		if(email.value == ""){ showToast("이메일을 입력해 주세요."); remove_active(); return false; }
		if(email.value.match(emailChk) == null){ showToast("이메일을 정확하게 입력해 주세요."); remove_active(); return false; }
		let emailStatus = fnidChk(email.value, "mb_email", "2");
		if(w == "u" && $("#base_email").val() == email.value){ emailStatus = true; }
		if(!emailStatus){ showToast("이미 사용중인 이메일 입니다."); remove_active(); return false; }
		
		if(fs_name.value == ""){ showToast("풋살장 명을 입력해 주세요."); remove_active(); return false; }				
		if(fs_tel.value == ""){ showToast("풋살장 전화번호를 입력해 주세요."); remove_active(); return false; }				
		if(sd_idx.value == ""){ showToast("풋살장 지역(시/도)을 선택해 주세요."); remove_active(); return false; }
		if(sd_idx.value == "36"){ 
		}else{
			if(si_idx.value == ""){ showToast("풋살장 지역(시/구/군)을 선택해 주세요."); remove_active(); return false; }
			if(do_idx.value == ""){ showToast("풋살장 지역(읍/면/동)을 선택해 주세요."); remove_active(); return false; }
		}
		if(fs_zip.value == ""){ showToast("풋살장 우편번호를 검색해 주세요."); remove_active(); return false; }
		if(fs_addr1.value == ""){ showToast("풋살장 기본주소를 검색해 주세요."); remove_active(); return false; }
		if(fs_addr2.value == ""){ showToast("풋살장 상세주소를 입력해 주세요."); remove_active(); return false; }		
		if(fs_start.value == ""){ showToast("풋살장 시작시간을 선택해 주세요."); remove_active(); return false; }
		if(fs_end.value == ""){ showToast("풋살장 종료시간을 선택해 주세요."); remove_active(); return false; }		
		if(stadium_cnt < 1){ showToast("구장을 1개 이상 등록해 주세요."); remove_active(); return false; }				
		if(fs_content.value == ""){ showToast("풋살장 소개를 입력해 주세요."); remove_active(); return false; }				
		if(fs_content.value == ""){ showToast("풋살장 환불 정책을 입력해 주세요."); remove_active(); return false; }		
		
		if(w == "u"){
			if($("#first_img").val() == "0"){ showToast("풋살장 첫번째 사진을 등록해 주세요."); remove_active(); return false;  }
			if($("#bs_img").val() == "0"){ showToast("사업자 등록증을 등록해 주세요."); remove_active(); return false; }
			registerProcess();
		}else{
			if(pic_img_1.value == ""){ showToast("풋살장 첫번째 사진을 등록해 주세요."); remove_active(); return false; }				
			if(fs_bs.value == ""){ showToast("사업자 등록증을 등록해 주세요."); remove_active(); return false; }
			cmPopOn('prv_pop');
		}
		
	}
	
	function registerProcess(){
		if($("#w").val() == ""){
			const chk1 = document.getElementById("chk1");
			if(!chk1.checked){showToast("이용약관에 동의해 주세요."); $("#regi_btn").removeClass("on"); return false; }

			const chk2 = document.getElementById("chk2");
			if(!chk2.checked){showToast("개인정보취급방침에 동의해 주세요."); $("#regi_btn").removeClass("on"); return false; }
		}
		const name = document.getElementById("mb_name");	
		const hp = document.getElementById("mb_hp");
		const id = document.getElementById("mb_id");
		const chk_id = document.getElementById("chk_id");
		const pw = document.getElementById("mb_password");
		const pw2 = document.getElementById("mb_password_re");
		const email = document.getElementById("mb_email");
		const fs_name = document.getElementById("mb_fs_name");
		const fs_tel = document.getElementById("mb_fs_tel");	
		const sd_idx = document.getElementById("sd_idx");	
		const si_idx = document.getElementById("si_idx");
		const do_idx = document.getElementById("do_idx");
		const fs_zip = document.getElementById("mb_fs_zip");	
		const fs_addr1 = document.getElementById("mb_fs_addr1");	
		const fs_addr2 = document.getElementById("mb_fs_addr2");	
		const fs_addr3 = document.getElementById("mb_fs_addr3");	
		const fs_start = document.getElementById("mb_fs_start");
		const fs_end = document.getElementById("mb_fs_end");
		const stadium_cnt = $("#stadium_list li").length;
		const fs_content = document.getElementById("mb_fs_content");
		const fs_refund = document.getElementById("mb_fs_refund");
		const pic_img_1 = document.getElementById("pic_img_1");
		const fs_bs = document.getElementById("mb_fs_bs");
		const use1 = $("input[name=mb_fs_use1]:checked").val();
		const use2 = $("input[name=mb_fs_use2]:checked").val();
		const use3 = $("input[name=mb_fs_use3]:checked").val();

		const form = $("#regi_frm")[0];
		const formData = new FormData(form);
		
		formData.append("w", $("#w").val());
		formData.append("app_chk", $("#app_chk").val());
		formData.append("mb_token", $("#mb_token").val());
		formData.append("mb_fs_lat", $("#mb_fs_lat").val());
		formData.append("mb_fs_lng", $("#mb_fs_lng").val());
		formData.append("mb_name", name.value);
		formData.append("mb_hp", hp.value);
		formData.append("mb_id", id.value);
		formData.append("mb_password", pw.value);
		formData.append("mb_email", email.value);
		formData.append("mb_fs_name", fs_name.value);
		formData.append("mb_fs_tel", fs_tel.value);
		formData.append("mb_fs_zip", fs_zip.value);
		formData.append("sd_idx", sd_idx.value);
		formData.append("si_idx", si_idx.value);
		formData.append("do_idx", do_idx.value);
		formData.append("mb_fs_addr1", fs_addr1.value);
		formData.append("mb_fs_addr2", fs_addr2.value);		
		formData.append("mb_fs_addr3", fs_addr3.value);		
		formData.append("mb_fs_use1", use1);
		formData.append("mb_fs_use2", use2);
		formData.append("mb_fs_use3", use3);
		formData.append("mb_fs_start", fs_start.value);
		formData.append("mb_fs_end", fs_end.value);
		formData.append("mb_fs_content", fs_content.value);
		formData.append("mb_fs_refund", fs_refund.value);
		
		formData.append("mb_fs_bs", $('[name="mb_fs_bs"]')[0].files[0]);
		formData.append("pic_img_1", $('[name="pic_img_1"]')[0].files[0]);
		if ($('#pic_img_2').length) {
			formData.append("pic_img_2", $('[name="pic_img_2"]')[0].files[0]);
		}
		if ($('#pic_img_3').length) {
			formData.append("pic_img_3", $('[name="pic_img_3"]')[0].files[0]);
		}
		if ($('#pic_img_4').length) {
			formData.append("pic_img_4", $('[name="pic_img_4"]')[0].files[0]);
		}
		if ($('#pic_img_5').length) {
			formData.append("pic_img_5", $('[name="pic_img_5"]')[0].files[0]);
		}

		$("input[name='rg_st_name[]']").each(function(index){
			if($(this).val()){ formData.append("rg_st_name[]", $(this).val()); }
		});

		$("input[name='rg_st_size[]']").each(function(index){
			if($(this).val()){ formData.append("rg_st_size[]", $(this).val()); }
		});

		$("input[name='rg_st_to[]']").each(function(index){
			if($(this).val()){ formData.append("rg_st_to[]", $(this).val()); }
		});

		$("input[name='rg_st_sort[]']").each(function(index){
			if($(this).val()){ formData.append("rg_st_sort[]", $(this).val()); }
		});

		$("input[name='rg_st_floor[]']").each(function(index){
			if($(this).val()){ formData.append("rg_st_floor[]", $(this).val()); }
		});

		$("input[name='st_price[]']").each(function(index){
			if($(this).val()){ formData.append("st_price[]", $(this).val()); }
		});

		$("input[name='st_price2[]']").each(function(index){
			if($(this).val()){ formData.append("st_price2[]", $(this).val()); }
		});
		
		$.ajax({
			url: '/inc/register_update.php',
			type: "post",
			processData: false,  // file전송시 필수
			contentType: false,  // file전송시 필수
			data: formData
		})
		.done(function(data) {
			console.log(data);
			if(data == "1111"){		
				if($("#w").val() == "u"){
					showToast("정보수정이 완료되었습니다.");	
				}else{
					cmPopOn('complete_pop');
				}
			}else{
				showToast("잠시후 다시 이용해 주세요.");
			}
		});		
	}

	function fnValueCount(){
		let reqCnt = ($(".req_ipt").length)+6;
		if($("#w").val() == "u"){ reqCnt = ($(".req_ipt").length)+4; }
		let reqCurCnt = 0;
		$(".req_ipt").each(function(){
			if($(this).val() != ""){
				reqCurCnt++;
			}
		});

		if($('input:radio[name=mb_fs_use1]').is(':checked') !== false){ reqCurCnt++; }
		if($('input:radio[name=mb_fs_use2]').is(':checked') !== false){ reqCurCnt++; }
		if($('input:radio[name=mb_fs_use3]').is(':checked') !== false){ reqCurCnt++; }
		if($("#stadium_list li").length > 0){ reqCurCnt++;  }

		if($("#w").val() == "u"){
			if($("#first_img").val() == "1"){ reqCurCnt++; }
			if($("#bs_img").val() == "1"){ reqCurCnt++; }		
		}else{
			if($("#pic_img_1").val() != ""){ reqCurCnt++; }
			if($("#mb_fs_bs").val() != ""){ reqCurCnt++; }		
		}

		if($("#sd_idx").val() == "36"){
			reqCnt = ($(".req_ipt").length)+4;
			if($("#w").val() == "u"){ reqCnt = ($(".req_ipt").length)+3; }
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

	function fnValueCount2(){
		const stCnt = ($(".st_ipt").length)+3;
		let reqStCnt = 0;
		$(".st_ipt").each(function(){
			if($(this).val() != ""){
				reqStCnt++;
			}
		});

		if($("input[name='st_to[]']:checked").length > 0){ reqStCnt++; }
		if($('input:radio[name=st_sort]').is(':checked') !== false){ reqStCnt++; }
		if($('input:radio[name=st_floor]').is(':checked') !== false){ reqStCnt++; }

		console.log(reqStCnt+"//"+stCnt);

		if(reqStCnt == stCnt){
			$("#submit_button2").addClass("on");
		}else{
			$("#submit_button2").removeClass("on");
		}
	}

	$(".st_ipt").keyup(function(e) {
		fnValueCount2();
	});

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

	function chgSido(v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/sigugun_list.php",
			data: {sd_idx:v},
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#si_idx").empty().append(data);
			}
		});
	}

	function chgSigugun(v){
		if(v){
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/dong_list.php",
				data: {si_idx:v},
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					$("#do_idx").empty().append(data);
				}
			});
		}
	}

	function offStadiumRes(){
		cmPopOff('stadium_pop');
		$(".st_ipt").val("");
		$(".st_frm_chk input").prop("checked", false);
		$("#stadium_pop .fix_btn_box").show();
	}

	function memberLeave(){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/member_leave.php",
			data: {content:"leave"}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				location.href = "<?php echo G5_BBS_URL?>/login.php";
			}
		});
	}
</script>
<script>
	// 우편번호 찾기 찾기 화면을 넣을 element
	var element_wrap = document.getElementById('wrap');

	function foldDaumPostcode() {
		// iframe을 넣은 element를 안보이게 한다.
		element_wrap.style.display = 'none';
	}

	function sample3_execDaumPostcode() {
		// 현재 scroll 위치를 저장해놓는다.
		var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
		new daum.Postcode({
			oncomplete: function(data) {
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 각 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var addr = ''; // 주소 변수
				var extraAddr = ''; // 참고항목 변수

				//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
					addr = data.roadAddress;
				} else { // 사용자가 지번 주소를 선택했을 경우(J)
					addr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
				if(data.userSelectedType === 'R'){
					// 법정동명이 있을 경우 추가한다. (법정리는 제외)
					// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
					if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
						extraAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if(data.buildingName !== '' && data.apartment === 'Y'){
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
					if(extraAddr !== ''){
						extraAddr = ' (' + extraAddr + ')';
					}
					// 조합된 참고항목을 해당 필드에 넣는다.
					//document.getElementById("sample3_extraAddress").value = extraAddr;
				
				} else {
					//document.getElementById("sample3_extraAddress").value = '';
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById('mb_fs_zip').value = data.zonecode;
				document.getElementById("mb_fs_addr1").value = addr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById("mb_fs_addr2").focus();

				// iframe을 넣은 element를 안보이게 한다.
				// (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
				element_wrap.style.display = 'none';

				// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
				document.body.scrollTop = currentScroll;

				Promise.resolve(data).then(o => {
					const { address } = data;

					return new Promise((resolve, reject) => {
						const geocoder = new daum.maps.services.Geocoder();

						geocoder.addressSearch(address, (result, status) =>{
							if(status === daum.maps.services.Status.OK){
								const { x, y } = result[0];

								resolve({ lat: y, lon: x })
							}else{
								reject();
							}
						});
					})
				}).then(result => {
					// 위, 경도 결과 값
					//$("#mb_lat").val(result['lat']);
					//$("#mb_lng").val(result['lon']);
					if(result['lat'] != ""){ $("#mb_fs_lat").val(result['lat']); }
					if(result['lon'] != ""){ $("#mb_fs_lng").val(result['lon']); }
					//alert(result['lat']+"//"+result['lon']);
				});
			},
			// 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
			onresize : function(size) {
				element_wrap.style.height = size.height+'px';
			},
			width : '100%',
			height : '100%'
		}).embed(element_wrap);

		// iframe을 넣은 element를 보이게 한다.
		element_wrap.style.display = 'block';
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>