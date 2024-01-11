<?php
	include_once("_common.php");

	$add_query = "";

	if($dataVal != ""){
		$add_query .= " AND (scd_name LIKE '%{$dataVal}%' OR scd_team_name LIKE '%{$dataVal}%') ";
	}

	if($dataType != "" && $dataType != "all"){
		$add_query .= " AND A.amr_st = '{$dataType}' ";	
	}

	if($dataStart != ""){
		$date = str_replace(". ", "-", $dataStart);
		$add_query .= " AND LEFT(A.amr_datetime, 10) >= '{$date}' ";	
	}

	if($dataEnd != ""){
		$date = str_replace(". ", "-", $dataEnd);
		$add_query .= " AND LEFT(A.amr_datetime, 10) <= '{$date}' ";	
	}

	$sql = " SELECT COUNT(*) cnt
					FROM a_match_req A, a_match B, a_schedule_res C
					WHERE 1
						AND A.am_idx = B.am_idx
						AND B.scd_idx = C.scd_idx
						AND B.mb_idx = '{$member['mb_no']}'
						AND C.delete_state = 1
						{$add_query}
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];
	
	$sql = " SELECT A.amr_idx, A.am_idx, A.amr_st, A.amr_datetime, B.mb_idx, A.at_idx, B.am_to, B.am_age, B.am_gender, B.am_bet, C.scd_idx, C.scd_date, C.scd_start, C.scd_end, C.as_idx, C.scd_team_name
					FROM a_match_req A, a_match B, a_schedule_res C
					WHERE 1
						AND A.am_idx = B.am_idx
						AND B.scd_idx = C.scd_idx
						AND B.mb_idx = '{$member['mb_no']}'
						AND C.delete_state = 1
						{$add_query}
					ORDER BY A.amr_datetime DESC
					limit {$limitVal}, 10
					";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$stateClass = "";
		$stateText = "승인 대기";
		if ($row['amr_st'] == 1) {
			$stateClass = "list_blue";
			$stateText = "예약 승인";
		}else if ($row['amr_st'] == 2) {
			$stateClass = "list_red";
			$stateText = "매치 거절";
		}

		$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
		$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
		$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
		$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));

		$sql2 = " select mb_idx, at_idx from a_match_req where amr_idx =  '{$row['amr_idx']}' ";
		$row2 = sql_fetch($sql2);
		
		$vs_team = "-";
		if($row['amr_st'] == 1){
			$vs_team = getTeamName($row['at_idx']);
		}
?>
<li>
	<p class="list_state">
		<span class="list_date"><?php echo date("Y. m. d H:i", strtotime($row['amr_datetime']));?></span>
		<strong class="<?php echo $stateClass?>"><span><?php echo $stateText?></span></strong>
	</p>
	<div class="list_info">
		<div class="list_match_info">
			<p class="list_match_type">매치</p>
			<p class="list_match_date"><?php echo date("Y. m. d", strtotime($row['scd_date']))?></p>
			<p class="list_match_time"><?php echo sprintf('%02d', $row['scd_start'])?>:00 ~ <?php echo sprintf('%02d', $row['scd_end'])?>:00</p>
			<p class="list_match_team">						
				<?php echo $row['scd_team_name']?> &nbsp;vs&nbsp; <?php echo $vs_team?>
			</p>
			<p class="list_match_desc">
				<span><?php echo getStadiumName($row['as_idx']);?></span>
				<span><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></span>
				<span><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></span>
				<?php if($row['am_bet'] == 1){ echo "<span>구장비 내기</span>"; }else if($row['am_bet'] == 2){ echo "<span>음료수 내기</span>"; }?>
			</p>
		</div>
		<p class="list_info_p">
			<span>매치 신청자</span>
			<strong><?php echo getMemberinfo($row2['mb_idx'], 'mb_name');?></strong>
		</p>
		<p class="list_info_p">
			<span>전화번호</span>
			<strong><?php echo getMemberinfo($row2['mb_idx'], 'mb_hp');?></strong>
		</p>
		<p class="list_info_p">
			<span>팀 명</span>
			<strong><?php echo getTeamName($row2['at_idx'])?></strong>
		</p>
	</div>
	
	<div class="list_btn_box">
		<?php if ($row['amr_st'] == 1) { ?>
			<button type="button" class="list_btn ver2 ver3" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '2', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '3');">매치 취소</button>
		<?php }else if ($row['amr_st'] == 2) { ?>
			<button type="button" class="list_btn" disabled>매치 거절</button>
			<button type="button" class="list_btn" disabled>매치 승인</button>
		<?php }else{ ?>
			<button type="button" class="list_btn ver3" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '2', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '2');">매치 거절</button>
			<button type="button" class="list_btn ver4" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '1', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '1');">매치 승인</button>
		<?php }?>								
	</div>
</li>
<?php }?>
<?php if($total_count < 1){?>
<li class="not_data">신청 내역이 없습니다.</li>
<?php }?>