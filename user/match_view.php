<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select A.*, B.mb_name from a_match A, g5_member B where A.mb_idx = B.mb_no and A.am_idx = '{$idx}' ";
	$row = sql_fetch($sql);
	
	//구장예약 카운트
	$sql2 = " select count(*) cnt from a_schedule_res where scd_idx = '{$row['scd_idx']}' ";
	$row2Cnt = sql_fetch($sql2);

	//매치글 등록 후 구장 예약 여부(대기중, 승인)
	$sql2 = " select count(*) cnt from a_schedule_res where match_idx = '{$idx}' and scd_res_cert = 0 ";
	$row2Cnt2 = sql_fetch($sql2);
	
	$sql2 = " select * from a_schedule_res where scd_idx = '{$row['scd_idx']}' ";
	$row2 = sql_fetch($sql2);

	$lastCancelDate = $row2['scd_date'];
	$lastCancelDate = date("Y-m-d", strtotime($lastCancelDate." -3 days"));

	$sql3 = " select * from a_stadium A, g5_member B where A.mb_idx = B.mb_no and A.as_idx = '{$row2['as_idx']}' ";
	$row3 = sql_fetch($sql3);

	$sql4 = " SELECT SUM(scd_score1) sum, SUM(scd_tention1) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND mb_idx = '{$row['mb_idx']}' 
							AND scd_team_idx = '{$row['at_idx']}'
							AND scd_score1 > 0
						";
	$row4 = sql_fetch($sql4);
	

	$sql42 = " SELECT SUM(scd_score2) sum, SUM(scd_tention2) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND scd_vs_team_mb_idx = '{$row['mb_idx']}'
							AND scd_vs_team_at_idx = '{$row['at_idx']}'
							AND scd_score2 > 0
						";
	$row42 = sql_fetch($sql42);

	$score = 0;
	$tention = 0;
	$matchCnt = $row4['cnt']+$row42['cnt'];
	if($matchCnt > 0){
		$score = round(($row4['sum']+$row42['sum'])/$matchCnt, 1);
		$tention = round(($row4['sum2']+$row42['sum2'])/$matchCnt, 1);
	}
	$scorePercent = $score*20;

	if($idx && $row['mb_idx'] != $member['mb_no']){
		addViewCnt($idx);
	}

	$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));
	$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
	$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
	$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));

	$name_x ='*';
	$name_a = mb_substr($row['mb_name'],0,1,"UTF-8");
	$name_b = mb_substr($row['mb_name'],2,10,"UTF-8");
	$name = $name_a.$name_x.$name_b;

	$sql_inst = " select count(*) cnt, amr_idx from a_match_req where am_idx = '{$idx}' and mb_idx = '{$member['mb_no']}' and amr_st != 2 ";
	$row_inst = sql_fetch($sql_inst);	
?>

<div class="match_view cm_padd4">
	<div class="mv_info_box1">
		<p>
			<strong><?php echo $name?></strong>
			<span></span>
			<b><?php echo date("Y. m. d H:i", strtotime($row['am_datetime']))?></b>
		</p>
		<ul>
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
				<span><?php echo number_format($row['am_view'])?></span>
			</li>
			<li></li>
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
				<span><?php echo number_format(reqCnt($idx))?></span>
			</li>
		</ul>
	</div>

	<?php if($row['res_st'] == 2 || $row['res_st'] == 3){?>
		<div class="mv_info_box2">
			<span>예약</span>
			<p><?php echo getFutsalStadiumName($row2['as_idx'])?> (<?php echo getStadiumName($row2['as_idx'])?>)</p>
		</div>
		<p class="mv_date">
			<?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row2['scd_start']).":00 ~ ".sprintf('%02d', $row2['scd_end']+1).":00"; ?>
		</p>
		<p class="mv_addr">
			<img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt="">
			<span>[<?php echo $row3['mb_fs_zip']?>] <?php echo $row3['mb_fs_addr1']?> <?php echo $row3['mb_fs_addr2']?> <?php echo $row3['mb_fs_addr3']?></span>
		</p>
		<?php if($row['res_st'] == 3){?><p class="mv_adm_mode">관리자 등록</p><?php }?>
	<?php }else{?>
		<div class="mv_info_box2 ver2">

			<ul class="mv_stadium_list">
				<?php if($row['am_area'] == 1){?>
				<li>
					<p class="mv_stl_name"><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?></p>
				</li>
				<?php }else if($row['am_area'] == 2){?>
					<?php
						if($row['fs_mb_idx1']){
							$row5 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx1']}' ");
					?>
					<li>
						<p class="mv_stl_name"><?php echo $row['fs_mb_name1']?></p>
						<p class="mv_stl_date">
							<?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row['am_time']).":00"; ?>
						</p>
						<p class="mv_stl_addr">
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span>[<?php echo $row5['mb_fs_zip']?>] <?php echo $row5['mb_fs_addr1']?> <?php echo $row5['mb_fs_addr2']?> <?php echo $row5['mb_fs_addr3']?></span>
						</p>
						<ul class="ust_sub_info2">
							<?php 
								$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
								$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
								$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
							?>
							<?php if($row['am_to']){?><li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li><?php }?>
							<?php if($row['am_age']){?><li><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></li><?php }?>
							<?php if($row['am_gender']){?><li><?php echo $_cfg['match']['gender'][$genderKey]['txt'];?></li><?php }?>
							<?php if($row['am_bet'] == 1){ echo "<li>구장비 내기</li>"; }else if($row['am_bet'] == 2){ echo "<li>음료수 내기</li>"; }?>			
						</ul>
					</li>
					<?php }?>
					<?php
						if($row['fs_mb_idx2']){
							$row5 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx2']}' ");
					?>
					<li>
						<p class="mv_stl_name"><?php echo $row['fs_mb_name2']?></p>
						<p class="mv_stl_date">
							<?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row['am_time']).":00"; ?>
						</p>
						<p class="mv_stl_addr">
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span>[<?php echo $row5['mb_fs_zip']?>] <?php echo $row5['mb_fs_addr1']?> <?php echo $row5['mb_fs_addr2']?> <?php echo $row5['mb_fs_addr3']?></span>
						</p>
						<ul class="ust_sub_info2">
							<?php 
								$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
								$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
								$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
							?>
							<?php if($row['am_to']){?><li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li><?php }?>
							<?php if($row['am_age']){?><li><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></li><?php }?>
							<?php if($row['am_gender']){?><li><?php echo $_cfg['match']['gender'][$genderKey]['txt'];?></li><?php }?>
							<?php if($row['am_bet'] == 1){ echo "<li>구장비 내기</li>"; }else if($row['am_bet'] == 2){ echo "<li>음료수 내기</li>"; }?>			
						</ul>
					</li>
					<?php }?>
					<?php
						if($row['fs_mb_idx3']){
							$row5 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx3']}' ");
					?>
					<li>
						<p class="mv_stl_name"><?php echo $row['fs_mb_name3']?></p>
						<p class="mv_stl_date">
							<?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row['am_time']).":00"; ?>
						</p>
						<p class="mv_stl_addr">
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span>[<?php echo $row5['mb_fs_zip']?>] <?php echo $row5['mb_fs_addr1']?> <?php echo $row5['mb_fs_addr2']?> <?php echo $row5['mb_fs_addr3']?></span>
						</p>
						<ul class="ust_sub_info2">
							<?php 
								$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
								$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
								$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
							?>
							<?php if($row['am_to']){?><li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li><?php }?>
							<?php if($row['am_age']){?><li><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></li><?php }?>
							<?php if($row['am_gender']){?><li><?php echo $_cfg['match']['gender'][$genderKey]['txt'];?></li><?php }?>
							<?php if($row['am_bet'] == 1){ echo "<li>구장비 내기</li>"; }else if($row['am_bet'] == 2){ echo "<li>음료수 내기</li>"; }?>			
						</ul>
					</li>
					<?php }?>
				<?php }?>
			</ul>			
		</div>
	<?php }?>

	<div class="mv_info_detail">
		<p class="mv_title">정보</p>
		<ul class="mv_detail_list">
			<li>
				<p class="mv_dt_th"><span>팀 이름</span></p>
				<p class="mv_dt_td"><?php echo $row['am_team_name']?></p>
			</li>
			<li>
				<p class="mv_dt_th">
					<span>매치 레벨</span>
					<button type="button" class="info_window">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
					</button>
				</p>
				<p class="mv_dt_td"><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></p>
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
			<li>
				<p class="mv_dt_th">
					<span>경기 평점</span>
					<button type="button" class="info_window">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
					</button>
				</p>
				<div class="mv_dt_td">
					<p class="mv_score_box">
						<img src="<?php echo G5_THEME_IMG_URL?>/score_star.png" alt="">
						<span style="width:<?php echo $scorePercent?>%;"></span>
					</p>
				</div>
				<div class="regi_th_desc ver2">
					<p>*경기 평점 - 매치한 상대팀이 입력한 점수입니다.</p>
					<ul >
						<li>
							<p>경기 평점 5 : </p>
							<p>더 높은 레벨에서 매치해야 합니다.</p>
						</li>
						<li>
							<p>경기 평점 4 : </p>
							<p>현재 레벨에서 상위권 입니다.</p>
						</li>
						<li>
							<p>경기 평점 3 : </p>
							<p>현재 레벨에서 평균 입니다.</p>
						</li>
						<li>
							<p>경기 평점 2 : </p>
							<p>현재 레벨에서 하위권 입니다.</p>
						</li>
						<li>
							<p>경기 평점 1 : </p>
							<p>더 낮은 레벨에서 매치해야 합니다.</p>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<p class="mv_dt_th"><span>인원 / 연령대</span></p>
				<p class="mv_dt_td"><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?> / <?php echo $_cfg['match']['age'][$ageKey]['txt'];?></p>						
			</li>
			<li>
				<p class="mv_dt_th"><span>내기 유형</span></p>
				<p class="mv_dt_td"><?php if($row['am_bet'] == 1){ echo "구장비 내기"; }else if($row['am_bet'] == 2){ echo "음료수 내기"; }else{ echo "내기 없음"; }?></p>
			</li>
			<li>
				<p class="mv_dt_th">
					<span>텐션</span>
					<button type="button" class="info_window">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_question.svg" alt="">
					</button>
				</p>
				<p class="mv_dt_td"><?php echo $tention?></p>
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
			<li>
				<p class="mv_dt_th"><span>성별</span></p>
				<p class="mv_dt_td"><?php echo $_cfg['match']['gender'][$genderKey]['txt'];?></p>
			</li>
		</ul>
	</div>

	<?php if($row['mb_idx'] != $member['mb_no'] && ($row['res_st'] == 2 || $row['res_st'] == 3)){?>
	<button type="button" class="mv_message" onClick="rnMessage('<?php echo $row['am_hp']?>');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_message.svg" alt="">
		<span>문자보내기</span>
	</button>
	<?php }?>

	<div class="mv_record">
		<p class="mv_record_alert">* 최근 3경기가 보여집니다.</p>
		<p class="mv_title">경기 최근 전적</p>
		<?php
			$sql_vs = " SELECT COUNT(*) cnt
									FROM a_match A, a_schedule_res B
									WHERE 1 
										AND A.scd_idx = B.scd_idx
										AND (at_idx = '{$row['at_idx']}' or at_vs_idx = '{$row['at_idx']}') 
										AND B.scd_state = 1
										AND B.delete_state = 1
										AND B.scd_result1 != 0
										AND B.scd_result2 != 0
									";
			$row_vs = sql_fetch($sql_vs);
			$vsCnt = $row_vs['cnt'];						
		?>
		<ul class="mv_record_ul">			
			<?php
				$sql_vs = " SELECT A.at_idx, A.mb_idx, A.at_vs_idx, B.scd_result1, B.scd_result2
										FROM a_match A, a_schedule_res B
										WHERE 1 
											AND A.scd_idx = B.scd_idx
											AND (at_idx = '{$row['at_idx']}' or at_vs_idx = '{$row['at_idx']}') 
											AND B.scd_state = 1
											AND B.delete_state = 1
											AND B.scd_result1 != 0
											AND B.scd_result2 != 0
										ORDER BY A.am_date DESC, B.scd_end DESC
										LIMIT 3
										";
				$result_vs = sql_query($sql_vs);
				for($v=0; $vs=sql_fetch_array($result_vs); $v++){
			?>
			<li>
				<div class="ms_detail_team">
					<p>
						<span><?php echo getTeamName($vs['at_idx'])?></span>
						<strong class="<?php echo matchResutClass($vs['scd_result1'])?>"><?php echo matchResut($vs['scd_result1'])?></strong>
					</p>
					<p><span>vs</span></p>
					<p>						
						<strong class="<?php echo matchResutClass($vs['scd_result2'])?>"><?php echo matchResut($vs['scd_result2'])?></strong>
						<span><?php echo getTeamName($vs['at_vs_idx'])?></span>
					</p>
				</div>
			</li>
			<?php }?>
			<?php if($vsCnt < 1){?>
			<li class="not_data">최근 전적이 없습니다.</li>
			<?php }?>
		</ul>
	</div>
	
	<?php if($row2['scd_state'] == 0){ ?>
	<div class="mv_team_list">
		<p class="mv_title">
			매치 신청 리스트
			<button type="button" onClick="teamRefresh();">
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_refresh.svg" alt="">
			</button>
		</p>		
		<ul class="mv_team_ul <?php if($row['mb_idx'] == $member['mb_no']){?>ver2<?php }?>">			
			<?php
				$reqTotal = 0;
				$sql_req = " select * 
											from a_match_req A, g5_member B, a_team C
											where A.mb_idx = B.mb_no 
												and B.mb_leave_status = 1 
												and  A.am_idx = '{$idx}' 
												and A.at_idx = C.at_idx
											order by A.amr_datetime asc 
											";
				$result_req = sql_query($sql_req);
				for($r=0; $req=sql_fetch_array($result_req); $r++){
					$reqTotal++;
			?>
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball2.svg" alt="">
				<span><?php echo $req['at_team_name']?></span>
				<?php if($row['mb_idx'] == $member['mb_no']){?>
				<div class="mv_team_confirm">
					<button type="button" onClick="rnMessage('<?php echo $req['mb_hp']?>');">
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_message.svg" alt="">
						<span>문자보내기</span>
					</button>
					<?php if($row2Cnt2['cnt'] > 0){?>
						<button type="button" class="ver2" onClick="matchReady();">
							<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk2.svg" alt="">
							<span>확정하기</span>
						</button>			
					<?php }else{?>
						<?php if($row['res_st'] == 1){?>
						<a href="<?php echo G5_URL?>/user/match_stadium_list.php?idx=<?php echo $idx?>&amr_idx=<?php echo $req['amr_idx']?>">
							<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk2.svg" alt="">
							<span>확정하기</span>
						</a>
						<?php }else{?>
						<button type="button" onClick="teamConform('<?php echo $req['mb_idx']?>', '<?php echo $req['at_idx']?>', '<?php echo getTeamName($req['at_idx'])?>');">
							<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk2.svg" alt="">
							<span>확정하기</span>
						</button>					
						<?php }?>			
					<?php }?>
				</div>
				<?php }?>
			</li>
			<?php }?>
			<?php if($reqTotal < 1){?>
			<li class="not_data">매치 신청팀이 없습니다.</li>
			<?php }?>
		</ul>
	</div>
	<?php } else if($row2['scd_state'] == 1){ ?>
	<div class="mv_vs_info">
		<div class="mv_vs_info_line">
			<p class="mv_vs_tit">매치 확정</p>
			<p class="mv_vs_team_name">
				<strong><span><?php echo $row['am_team_name']?></span></strong>
				<strong><span>vs</span></strong>
				<strong><span>
					<?php
						//상대팀이 비회원팀일 경우 작업해야 할 듯
						echo getTeamName($row['at_vs_idx']);
					?>
				</span></strong>			
			</p>
			<div class="mv_info_box2 ver2 ver3">
				<p><?php echo getFutsalStadiumName($row2['as_idx'])?></p>
			</div>
			<p class="mv_date">
				<?php echo getStadiumName($row2['as_idx'])?> <?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row2['scd_start']).":00 ~ ".sprintf('%02d', $row2['scd_end']+1).":00"; ?>
			</p>
			<p class="mv_addr">
				<img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt="">
				<span>[<?php echo $row3['mb_fs_zip']?>] <?php echo $row3['mb_fs_addr1']?> <?php echo $row3['mb_fs_addr2']?> <?php echo $row3['mb_fs_addr3']?></span>
			</p>
			<ul class="ust_sub_info2">
				<?php 
					$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row3['mb_fs_use1']}' ";
					$use1 = sql_fetch($sql_use);
					
					$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row3['mb_fs_use2']}' ";
					$use2 = sql_fetch($sql_use);

					$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row3['mb_fs_use3']}' ";
					$use3 = sql_fetch($sql_use);
					
					$ary1 = array();
					$ary2 = array();
					$sql_stadium = " select * from a_stadium where mb_idx = '{$row3['mb_no']}' and as_delete_st = 1 ";
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
		</div>
	</div>
	<?php }?>
</div>

<div class="fix_btn_back"></div>
<?php if($row['mb_idx'] == $member['mb_no']){?>
<div class="fix_btn_box fix_btn_box_flex">
	<a href="<?php echo G5_URL?>/user/match_write.php?w=u&idx=<?php echo $idx?>" class="fix_btn fix_modi">수정</a>
	<?php if($row2Cnt['cnt'] > 0){?>
		<?php if($row2['scd_state'] == 0){?>
			<?php if(G5_TIME_YMD <= $lastCancelDate){?>
			<button type="button" class="fix_btn ver3" onClick="cmPopOn('leave_pop');">예약취소</button>
			<?php }else{?>
			<button type="button" class="fix_btn ver4" onClick="noMore();">예약취소</button>
			<?php }?>
		<?php }else if($row2['scd_state'] == 1){?>
		<button type="button" class="fix_btn ver3" onClick="cmPopOn('cancel_pop');">매치취소</button>
		<?php }?>		
	<?php }else{?>

		<?php if($row2Cnt2['cnt'] > 0){?>
			<button type="button" class="fix_btn ver4" onClick="matchReady();">삭제</button>
		<?php }else{?>
			<button type="button" class="fix_btn ver3" onClick="cmPopOn('delete_pop');">삭제</button>
		<?php }?>
	<?php }?>
</div>
<?php }else{?>
<div class="fix_btn_box">
	<?php if($row2['scd_state'] == 1){?>
		<button type="button" class="fix_btn fix_modi">매치 확정</button>	
	<?php }else{?>
		<?php if($row_inst['cnt'] > 0){?>
		<button type="button" class="fix_btn ver2" onClick="cmPopOn('match_inst_pop');">매치 취소</button>	
		<?php }else{?>
		<button type="button" class="fix_btn on" onClick="cmPopOn('match_inst_pop');">매치 신청</button>	
		<?php }?>
	<?php }?>
</div>
<?php }?>

<div id="match_inst_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc <?php if($row_inst['cnt'] < 1){?>ver2<?php }?>"><?php if($row_inst['cnt'] > 0){?>매치를 취소하겠습니까?<?php }else{?>매치를 신청하겠습니까?<?php }?></p>
		<?php if($row_inst['cnt'] < 1){?>
		<ul class="my_team_pop">
			<?php for($t=1; $t<=3; $t++){?>
			<?php if($member['mb_user_team'.$t]){ ?>
			<li>
				<input type="radio" name="mb_user_team" id="mb_user_team<?php echo $t?>" value="<?php echo $member['mb_user_team'.$t]?>" <?php if($t==1){ echo "checked";  }?>>
				<label for="mb_user_team<?php echo $t?>"><?php echo getTeamName($member['mb_user_team'.$t])?></label>
			</li>
			<?php }?>
		<?php }?>		
		</ul>
		<?php }?>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('match_inst_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="del_cofirm_btn" onClick="submitMatch('<?php echo $idx?>', '<?php if($row_inst['cnt'] > 0){?>2<?php }else{?>1<?php }?>', '<?php echo $row_inst['amr_idx']?>');">확인</button>
		</div>
	</div>
</div>

<div id="team_confirm" class="cm_pop">	
	<input type="hidden" id="res_ver2_mb_idx">
	<input type="hidden" id="res_ver2_at_idx">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">		
		<p class="cm_pop_desc" id="team_info_content"></p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('team_confirm');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="del_cofirm_btn" onClick="matchTeamPick();">확인</button>
		</div>
	</div>
</div>

<div id="leave_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc" id="content">예약을 취소하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="resCancel();">확인</button>
		</div>
	</div>
</div>

<div id="cancel_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc" id="content">매치를 취소하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('cancel_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="matchDelete();">확인</button>
		</div>
	</div>
</div>

<div id="delete_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc">글을 삭제하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('delete_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="matchCancel();">확인</button>
		</div>
	</div>
</div>

<script>
function submitMatch(idx, type, amr_idx){
	//alert($("input[name=mb_user_team]:checked").val());
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/submitMatch.php",
		data: {idx:idx, type:type, amr_idx:amr_idx, mb_user_team:$("input[name=mb_user_team]:checked").val()}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);
			location.reload();
		}
	});
}

function teamRefresh(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/teamRefresh.php",
		data: {am_idx:"<?php echo $idx?>"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);
			$(".mv_team_ul").empty().append(data);
		}
	});
}

function teamConform(v, z, x){
	$("#res_ver2_mb_idx").val(v);
	$("#res_ver2_at_idx").val(z);
	$("#team_info_content").text(`${x} 팀과의 매치를 확정하시겠습니까?`);
	cmPopOn('team_confirm');
}

function matchTeamPick(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/matchTeamPick.php",
		data: {am_idx:"<?php echo $idx?>", mb_idx:$("#res_ver2_mb_idx").val(), at_idx:$("#res_ver2_at_idx").val(), scd_idx:"<?php echo $row2['scd_idx']?>"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			cmPopOff('team_confirm');
			location.reload();
		}
	});
}

function resCancel(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/res_cert_process.php",
		data: {idx:"<?php echo $row['scd_idx']?>", state:"3"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {			
			//console.log(data);
			cmPopOff('leave_pop');
			location.href = "<?php echo G5_URL?>/user/";
		}
	});
}

function matchCancel(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/match_cencel.php",
		data: {am_idx:"<?php echo $idx?>", scd_idx:"<?php echo $row['scd_idx']?>"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {			
			//console.log(data);
			cmPopOff('cancel_pop');
			location.reload();
		}
	});
}

function matchReady(){
	showToast("구장 예약 승인 대기중인 상태입니다.");
}

function matchDelete(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/match_delete.php",
		data: {am_idx:"<?php echo $idx?>"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			console.log(data);
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>