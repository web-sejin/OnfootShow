<?php
	include_once("_common.php");

	$date = str_replace(". ", "-", $date);

	if($is_member){
		if($member['mb_lat'] && $member['mb_lng']){
			$myLat = $member['mb_lat'];
			$myLon = $member['mb_lng'];
		}else{
			$myLat = $_SESSION['lat'];
			$myLon = $_SESSION['lng'];	
		}
	}else{
		$myLat = $_SESSION['lat'];
		$myLon = $_SESSION['lng'];
	}
	
	$local = false;
	$add_query = "";

	if($date != ""){ $add_query .= " and am_date = '{$date}' "; }
	if($start != ""){ 
		$add_query .= " and ((A.res_st=1 and A.am_time='{$start}') or (A.res_st=2 and B.scd_start='{$start}') or (A.res_st=3 and B.scd_start='{$start}')) "; 
	}
	if($end != ""){
		$endTime = ($end*1)-2;
		$endTime2 = ($end*1)-1;
		$add_query .= " and ((A.res_st=1 and A.am_time='{$endTime}') or (A.res_st=2 and B.scd_end='{$endTime2}') or (A.res_st=3 and B.scd_end='{$endTime2}')) "; 
	}
	if($sort != ""){
		if($sort == "1"){
			$add_query .= " and (A.res_st=1 or A.res_st=2) ";
		}else if($sort == "2"){
			$add_query .= " and A.res_st=3 ";
		}
	}
	if($bet != "" && $bet != "all"){
		if($bet == "3"){
			$add_query .= " and A.am_bet=0 ";
		}else{
			$add_query .= " and A.am_bet='{$bet}' ";
		}
	}
	if($subject != ""){
		$add_query .= " and (A.am_team_name like '%{$subject}%' or A.fs_mb_name1 like '%{$subject}%' or A.fs_mb_name2 like '%{$subject}%' or A.fs_mb_name3 like '%{$subject}%') ";	
	}
	if($levelList){
		$expLevel = explode("|", $levelList);
		$add_query .= " and ( ";
		for($i=0; $i<count($expLevel); $i++){
			if($i != 0){ $add_query .= " or ";  }
			$add_query .= " am_level = '{$expLevel[$i]}' ";
		}
		$add_query .= " ) ";
	}
	if($ageList){
		$expAge = explode("|", $ageList);
		$add_query .= " and ( ";
		for($i=0; $i<count($expAge); $i++){
			if($i != 0){ $add_query .= " or ";  }
			$add_query .= " am_age = '{$expAge[$i]}' ";
		}
		$add_query .= " ) ";
	}
	if($localList){
		$expLocal = explode("||", $localList);
		$add_query .= " and ( ";
		for($i=0; $i<count($expLocal); $i++){
			if($i != 0){ $add_query .= " or ";  }
			
			$add_query .= " ( ";
			$exp2 = explode("|", $expLocal[$i]);			
			for($j=0; $j<count($exp2); $j++){
				if($j == 0){ $add_query .= " sd_idx = '{$exp2[$j]}' "; }
				if($j == 1){ $add_query .= " and si_idx = '{$exp2[$j]}' "; }
			}
			$add_query .= " ) ";
		}
		$add_query .= " ) ";
	}

	$sql_common = " FROM a_match A 											
											LEFT JOIN (SELECT * FROM a_schedule_res WHERE delete_state = 1) B ON A.scd_idx = B.scd_idx
											WHERE 1
												AND A.delete_st = 1	
												AND (B.delete_state = 1 OR B.delete_state IS NULL)
												AND (B.scd_state = 0 or B.scd_state is NULL)					
												AND (B.scd_res_cert = 0 OR B.scd_res_cert = 1 OR B.scd_res_cert IS NULL)
												AND A.am_rematch = 0
											";
	$sql_order = " ORDER BY am_datetime DESC ";
	if($score != "" && $score != "all"){
		if($score == "1"){
			$sql_order = " ORDER BY (score_sum/result_cnt) DESC, am_datetime DESC ";
		}else if($score == "2"){
			$sql_order = " ORDER BY (score_sum/result_cnt) ASC, am_datetime DESC ";
		}
	}
	if($tention != "" && $tention != "all"){
		if($tention == "1"){
			$sql_order = " ORDER BY (tention_sum/result_cnt) DESC, am_datetime DESC ";
		}else if($tention == "2"){
			$sql_order = " ORDER BY (tention_sum/result_cnt) ASC, am_datetime DESC ";
		}
	}

	$sql_limit = "limit {$limitVal}, 10";

	$sql = " SELECT COUNT(*) cnt 
					{$sql_common}
					{$add_query} 
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	/*


		데이터 몇 개 더 쌓고
		, (SELECT SUM(scd_score2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx != A.mb_vs_idx AND scd_vs_team_idx = A.at_vs_idx AND scd_score2 > 0) AS score_sum2
		, (SELECT SUM(scd_tention2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx != A.mb_vs_idx AND scd_vs_team_idx = A.at_vs_idx AND scd_tention2 > 0) AS tention_sum2
		데이터 가공해서 넣기
		위 데이터는 임시로 만들어둔 것


		*/

	$sql = " SELECT SELECT A.*, B.as_idx, B.scd_state
					, (SELECT SUM(scd_score1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS score_sum
					, (SELECT SUM(scd_score2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS score_sum2
					, (SELECT SUM(scd_tention1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_tention1 > 0) AS tention_sum
					, (SELECT SUM(scd_tention2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_tention2 > 0) AS tention_sum2
					, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS result_cnt
					, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS result_cnt2
					{$sql_common}
					{$add_query} 
					{$sql_order} 
					{$sql_limit}
					";
	$result = sql_query($sql);	
	
	if($myLat != "" && $myLat != "00" && $myLon != "" && $myLon != "0"){
		$local = true;		
		$sql_common = " , (6371*acos(cos(radians({$myLat}))*cos(radians(lat))*cos(radians(lng)-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(lat)))) AS distance
												FROM a_match A 
												LEFT JOIN (SELECT * FROM a_schedule_res C WHERE C.delete_state = 1) B ON A.scd_idx = B.scd_idx												
												WHERE 1
													AND A.delete_st = 1
													AND (B.delete_state = 1 OR B.delete_state IS NULL)
													AND (B.scd_state = 0 or B.scd_state is NULL) 
													AND (B.scd_res_cert = 0 OR B.scd_res_cert = 1 OR B.scd_res_cert IS NULL)
													AND A.am_rematch = 0
											";
		$sql_order = " ORDER BY distance ASC, A.am_datetime DESC ";
		if($score != "" && $score != "all"){
			if($score == "1"){
				$sql_order = " ORDER BY (score_sum/result_cnt) DESC, distance ASC, am_datetime DESC ";
			}else if($score == "2"){
				$sql_order = " ORDER BY (score_sum/result_cnt) ASC, distance ASC, am_datetime DESC ";
			}
		}
		if($tention != "" && $tention != "all"){
			if($tention == "1"){
				$sql_order = " ORDER BY (tention_sum/result_cnt) DESC, distance ASC, am_datetime DESC ";
			}else if($tention == "2"){
				$sql_order = " ORDER BY (tention_sum/result_cnt) ASC, distance ASC, am_datetime DESC ";
			}
		}

		$sql = " SELECT count(*) cnt
				{$sql_common}
				{$add_query}
		";	
		$row = sql_fetch($sql);
		$total_count = $row['cnt'];

		$sql = " SELECT A.*, B.*
			, (SELECT SUM(scd_score1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS score_sum
			, (SELECT SUM(scd_score2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS score_sum2
			, (SELECT SUM(scd_tention1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_tention1 > 0) AS tention_sum
			, (SELECT SUM(scd_tention2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_tention2 > 0) AS tention_sum2
			, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS result_cnt
			, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS result_cnt2
			{$sql_common}
			{$add_query}
			{$sql_order}
			{$sql_limit}
		";	
		$result = sql_query($sql);
	}
	//echo $sql;

	for($i=0; $row=sql_fetch_array($result); $i++){
		$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));

		$score = 0;
		$tention = 0;
		$matchCnt = $row['result_cnt']+$row['result_cnt2'];
		if($matchCnt > 0){
			$score = round(($row['score_sum']+$row['score_sum2'])/$matchCnt, 1);
			$tention = round(($row['tention_sum']+$row['tention_sum2'])/$matchCnt, 1);
		}
?>
<li class="url_li">
	<a href="<?php echo G5_URL?>/user/match_view.php?idx=<?php echo $row['am_idx']?>" <?php if($row['res_st'] == 2 || $row['res_st'] == 3){?>class="res_st"<?php }?>>
		<?php if($row['res_st'] == 2 || $row['res_st'] == 3){?><p class="url_res_st">예약</p><?php }?>
		<div class="url_type_box">
			<p class="url_type ver2"><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></p>
			<p class="url_type ver2">텐션 <?php echo $tention?></p>
		</div>
		<h3 class="url_name"><?php echo $row['am_team_name']?></h3>
		<p class="url_info1">
			<span><?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>)</span>
			<span>
				<?php 
					if($row['res_st'] == 2 || $row['res_st'] == 3){
						echo sprintf('%02d', $row['scd_start']).":00 ~ ".sprintf('%02d', $row['scd_end']+1).":00";
					}else{
						echo sprintf('%02d', $row['am_time']).":00";
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
				<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?> <?php echo getFutsalStadiumName($row['as_idx'])?> (<?php echo getStadiumName($row['as_idx'])?>)</span>
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