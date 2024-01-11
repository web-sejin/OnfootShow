<?php
	include_once("_common.php");

	//type1 : 매치 등록, 리매치 신청(매치 등록자 입장)
	//tpye2 : 매치 신청, 리매치 요청받음(매치 신청자 입장)
	
	$nowTime = date("Y-m-d H", strtotime(G5_TIME_YMDHIS));
	$sql_limit = "limit 0, 10";

	$add_query = "";

	if($state_type){
		if($state_type == "1"){
			//$add_query .= "AND (scd_state = 0 or scd_state IS NULL)";
			$add_query .= " AND (scd_state = 0 or scd_state IS NULL or (scd_state = 1 AND CONCAT(scd_date,' ',scd_end) > '{$nowTime}'))";
		}else if($state_type == "2"){
			//$add_query .= "AND scd_state = 1";
			$add_query .= " AND scd_state = 1 AND CONCAT(scd_date,' ',scd_end) <= '{$nowTime}' ";
		}
	}

	if($team_idx){
		$add_query .= " AND at_idx = '{$team_idx}' ";
	}

	if($match_type){
		if($match_type == "1"){
			//매치 등록
			$add_query .= " AND res_type = 'type1' AND am_rematch = 0 ";
		}else if($match_type == "2"){
			//매치 신청
			$add_query .= " AND res_type = 'type2' AND am_rematch = 0 ";
		}else if($match_type == "3"){
			//리매치 요청=매치 신청자
			$add_query .= " AND res_type = 'type2' AND am_rematch = 1 ";
		}else if($match_type == "4"){
			//리매치 신청=매치 등록자
			$add_query .= " AND res_type = 'type1' AND am_rematch = 1 ";
		}else if($match_type == "5"){
			//경기 종료시간이 지나지 않은 매치확정
			$add_query .= " AND scd_state = '1' ";
		}
	}

	$sql = " SELECT COUNT(*) cnt FROM (
						SELECT am_idx, am_level, am_rematch, res_st, am_team_name, am_date, am_area, sd_idx, si_idx, do_idx, fs_mb_idx1, fs_mb_idx2, fs_mb_idx3, am_view, am_to, am_age, am_gender, am_bet, A.mb_idx as mb_no, am_datetime as datetime, 'type1' as res_type, scd_state, at_idx, scd_date, scd_end, scd_score1, scd_score2
						FROM a_match A 
						LEFT JOIN (SELECT scd_idx, scd_state, scd_date, scd_end, scd_score1, scd_score2 FROM a_schedule_res A3 WHERE 1) A2 ON A.scd_idx = A2.scd_idx		
						WHERE A.mb_idx = {$member['mb_no']} AND A.delete_st = 1	
						UNION ALL
						SELECT B.am_idx, am_level, am_rematch, res_st, am_team_name, am_date, am_area, sd_idx, si_idx, do_idx, fs_mb_idx1, fs_mb_idx2, fs_mb_idx3, am_view, am_to, am_age, am_gender, am_bet, B.mb_idx as mb_no, B.amr_datetime as datetime, 'type2' as res_type, scd_state, B.at_idx, scd_date, scd_end, scd_score1, scd_score2
						FROM a_match_req B, a_match C
						LEFT JOIN (SELECT scd_idx, scd_state, scd_date, scd_end, scd_score1, scd_score2 FROM a_schedule_res D2 WHERE 1) D ON C.scd_idx = D.scd_idx		
						WHERE B.am_idx = C.am_idx AND B.mb_idx = {$member['mb_no']} AND C.delete_st = 1 AND B.amr_st != 2
					) a
					WHERE 1					
					{$add_query}
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	$sql = " SELECT * FROM (
						SELECT am_idx, am_level, am_rematch, res_st, am_team_name, am_date, am_area, sd_idx, si_idx, do_idx, fs_mb_idx1, fs_mb_idx2, fs_mb_idx3, am_view, am_to, am_age, am_gender, am_bet, A.mb_idx as mb_no, am_datetime as datetime, 'type1' as res_type, scd_state, at_idx, scd_date, scd_end, scd_score1, scd_score2
						FROM a_match A 
						LEFT JOIN (SELECT scd_idx, scd_state, scd_date, scd_end, scd_score1, scd_score2 FROM a_schedule_res A3 WHERE 1) A2 ON A.scd_idx = A2.scd_idx		
						WHERE A.mb_idx = {$member['mb_no']} AND A.delete_st = 1	
						UNION ALL
						SELECT B.am_idx, am_level, am_rematch, res_st, am_team_name, am_date, am_area, sd_idx, si_idx, do_idx, fs_mb_idx1, fs_mb_idx2, fs_mb_idx3, am_view, am_to, am_age, am_gender, am_bet, B.mb_idx as mb_no, B.amr_datetime as datetime, 'type2' as res_type, scd_state, B.at_idx, scd_date, scd_end, scd_score1, scd_score2
						FROM a_match_req B, a_match C
						LEFT JOIN (SELECT scd_idx, scd_state, scd_date, scd_end, scd_score1, scd_score2 FROM a_schedule_res D2 WHERE 1) D ON C.scd_idx = D.scd_idx		
						WHERE B.am_idx = C.am_idx AND B.mb_idx = {$member['mb_no']} AND C.delete_st = 1 AND B.amr_st != 2
					) a
					WHERE 1
					{$add_query}
					ORDER BY datetime DESC
					limit {$limitVal}, 10
					";
	$result = sql_query($sql);	
	for($i=0; $row=sql_fetch_array($result); $i++){
		$reviewType = false;
		$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));

		$score = 0;
		$tention = 0;
		if($row['score_sum'] > 0 && $row['result_cnt'] > 0){
			$score = round($row['score_sum']/$row['result_cnt'], 1);
		}
		if($row['tention_sum'] > 0 && $row['result_cnt'] > 0){
			$tention = round($row['tention_sum']/$row['result_cnt'], 1);
		}

		$myStClass = "";
		$myStText = "매치 등록";

		if($row['scd_state'] == 1){
			$myStClass = "ver_blue";
			$myStText = "매치 확정";

			$thisTime = $row['scd_date'].' '.$row['scd_end'];
			if($thisTime <= $nowTime){
				$reviewType = true;
				if($row['res_type'] == "type1"){
					if($row['scd_score2'] == 0){
						$myStClass = "ver_orange";
						$myStText = "리뷰 작성 필요";		
					}else{
						$myStClass = "ver_blue";
						$myStText = "리뷰 작성 완료";
					}
				}else if($row['res_type'] == "type2"){
					if($row['scd_score1'] == 0){
						$myStClass = "ver_orange";
						$myStText = "리뷰 작성 필요";		
					}else{
						$myStClass = "ver_blue";
						$myStText = "리뷰 작성 완료";
					}
				}				
			}
		}else if($row['res_type'] == "type1" && $row['am_rematch'] == 1){
			$myStClass = "ver_red";
			$myStText = "리매치 신청";
		}else if($row['res_type'] == "type2" && $row['am_rematch'] == 0){
			$myStClass = "";
			$myStText = "매치 신청";
		}else if($row['res_type'] == "type2" && $row['am_rematch'] == 1){
			$myStClass = "ver_red";
			$myStText = "리매치 요청";
		}

		$sql2 = " select * from a_match where am_idx = '{$row['am_idx']}' ";
		$row2 = sql_fetch($sql2);
		
		$sql3 = " select * from a_schedule_res where scd_idx = '{$row2['scd_idx']}' ";
		$row3 = sql_fetch($sql3);

		$sql4 = " SELECT SUM(scd_score1) sum, SUM(scd_tention1) sum2, COUNT(*) cnt
							FROM a_schedule_res 
							WHERE 1
								AND scd_state=1 
								AND scd_idx IS NOT NULL 
								AND mb_idx = '{$row2['mb_idx']}' 
								AND scd_team_idx = '{$row2['at_idx']}'
								AND scd_score1 > 0
							";
		$row4 = sql_fetch($sql4);

		$sql42 = " SELECT SUM(scd_score2) sum, SUM(scd_tention2) sum2, COUNT(*) cnt
							FROM a_schedule_res 
							WHERE 1
								AND scd_state=1 
								AND scd_idx IS NOT NULL 
								AND scd_vs_team_mb_idx = '{$row2['mb_idx']}'
								AND scd_vs_team_at_idx = '{$row2['at_idx']}'
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
?>
<li class="url_li">		
	<?php if($row3['scd_res_cert'] == 2 || $row3['scd_res_cert'] == 3){?>
	<div class="fix_screen">등록자 또는 구장에 의해<br>취소된 매치입니다.</div>
	<?php }?>
	<?php if($reviewType){?>
	<a <?php if($row3['scd_res_cert'] != 2 && $row3['scd_res_cert'] != 3){?>href="<?php echo G5_URL?>/user/match_reivew.php?am_idx=<?php echo $row['am_idx']?>&scd_idx=<?php echo $row2['scd_idx']?>"<?php }?> <?php if($row['res_st'] == 2 || $row['res_st'] == 3){?>class="res_st"<?php }?>>
	<?php }else{?>
	<a <?php if($row3['scd_res_cert'] != 2 && $row3['scd_res_cert'] != 3){?>href="<?php echo G5_URL?>/user/match_view.php?idx=<?php echo $row['am_idx']?>"<?php }?> <?php if($row['res_st'] == 2 || $row['res_st'] == 3){?>class="res_st"<?php }?>>
	<?php }?>
		<?php if($row['res_st'] == 2 || $row['res_st'] == 3){?><p class="url_res_st">예약</p><?php }?>
		<p class="url_my_state">
			<span class="<?php echo $myStClass?>"><?php echo $myStText?></span>
		</p>
		<div class="url_type_box">
			<p class="url_type ver2"><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></p>
			<p class="url_type ver2">텐션 <?php echo $tention?></p>
		</div>
		<h3 class="url_name"><?php echo $row['am_team_name']?></h3>
		<p class="url_info1">
			<span><?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>)</span>
			<span>
				<?php 
					if($row['res_st'] == 2){
						echo sprintf('%02d', $row3['scd_start']).":00 ~ ".sprintf('%02d', $row3['scd_end']+1).":00";
					}else{
						echo sprintf('%02d', $row2['am_time']).":00";
					}
				?>
			</span>
		</p>
		<?php if($row['res_st'] == 1){?>
		<ul class="ust_sub_info">
			<?php if($row['am_area'] == 1){?>
			<li>
				<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
				<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?></span>
			</li>
			<?php }else if($row['am_area'] == 2){?>
				<?php
					if($row['fs_mb_idx1']){
						$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx1']}' ");
				?>
				<li>
					<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
					<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
				</li>
				<?php }?>
				<?php
					if($row['fs_mb_idx2']){
						$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx2']}' ");
				?>
				<li>
					<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
					<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
				</li>
				<?php }?>
				<?php
					if($row['fs_mb_idx3']){
						$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx3']}' ");
				?>
				<li>
					<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
					<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
				</li>
				<?php }?>
			<?php }?>
		</ul>
		<?php }else if($row['res_st'] == 2){?>
		<ul class="ust_sub_info">
			<li>
				<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
				<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?> <?php echo getFutsalStadiumName($row3['as_idx'])?> (<?php echo getStadiumName($row3['as_idx'])?>)</span>
			</li>
		</ul>
		<?php }else if($row['res_st'] == 3){?>
		<ul class="ust_sub_info">
			<li>
				<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
				<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?> <?php echo getFutsalStadiumName($row['as_idx'])?> (<?php echo getStadiumName($row['as_idx'])?>)</span>
			</li>
		</ul>
		<?php }?>
		<ul class="url_info3">
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg" alt="">
				<span><b><?php echo $score?></b></span>
			</li>
			<li></li>
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
				<span><?php echo number_format($row['am_view'])?></span>
			</li>
			<li></li>
			<li>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
				<span><?php echo number_format(reqCnt($row['am_idx']))?></span>
			</li>
		</ul>
		<ul class="url_info2 ver2">
			<?php if($row['res_st'] == 3){?><li class="adm_mode">관리자 등록</li><?php }?>
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
	</a>
</li>
<?php }?>
<?php if($total_count < 1){?>
<li class="not_data">등록된 매치가 없습니다.</li>
<?php }?>