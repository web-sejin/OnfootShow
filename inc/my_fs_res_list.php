<?php
	include_once("_common.php");

	$add_query = "";

	if($dataVal != ""){
		$add_query .= " AND (scd_name LIKE '%{$dataVal}%' OR scd_team_name LIKE '%{$dataVal}%') ";
	}

	if($dataType != "" && $dataType != "all"){
		if($dataType == "2"){
			$add_query .= " AND (scd_res_cert = 2 OR scd_res_cert = 3) ";	
		}else{
			$add_query .= " AND scd_res_cert = '{$dataType}' ";	
		}
	}

	if($dataStart != ""){
		$date = str_replace(". ", "-", $dataStart);
		$add_query .= " AND scd_date >= '{$date}' ";	
	}

	if($dataEnd != ""){
		$date = str_replace(". ", "-", $dataEnd);
		$add_query .= " AND scd_date <= '{$date}' ";	
	}

	$sql = " SELECT COUNT(*) cnt
					FROM a_schedule_res A, g5_member B, a_stadium C
					WHERE 1
						AND A.as_idx = C.as_idx
						AND B.mb_no = C.mb_idx
						AND A.scd_res_type = 2
						AND A.delete_state = 1
						AND B.mb_no = '{$member['mb_no']}'
						{$add_query}
					";
	$row = sql_fetch($sql);
	$totalCnt = $row['cnt'];

	$sql = " SELECT A.*, C.mb_idx AS mb_no, C.as_name
					FROM a_schedule_res A, g5_member B, a_stadium C
					WHERE 1
						AND A.as_idx = C.as_idx
						AND B.mb_no = C.mb_idx
						AND A.scd_res_type = 2
						AND A.delete_state = 1
						AND B.mb_no = '{$member['mb_no']}'
						{$add_query}
					ORDER BY A.scd_datetime DESC
					limit {$limitVal}, 10
					";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$stateClass = "";
		$stateText = "승인 대기";
		if ($row['scd_res_cert'] == 1) {
			$stateClass = "list_blue";
			$stateText = "예약 승인";
		}else if ($row['scd_res_cert'] == 2) {
			$stateClass = "list_red";
			$stateText = "승인 거절";
		}else if ($row['scd_res_cert'] == 3) {
			$stateClass = "list_red";
			$stateText = "승인 거절";
		}

		$ageKey = array_search($row['scd_match_age'], array_column($_cfg['match']['age'], 'val'));
		$ageVal = $_cfg['match']['age'][$ageKey]['txt'];
?>
<li id="list_<?php echo $row['scd_idx']?>">
	<p class="list_state">
		<span class="list_date"><?php echo date("Y. m. d H:i", strtotime($row['scd_datetime']));?></span>
		<strong class="<?php echo $stateClass?>"><span><?php echo $stateText?></span></strong>
	</p>
	<div class="list_info">
		<p class="list_info_p">
			<span>예약 날짜</span>
			<strong><?php echo date("Y. m. d", strtotime($row['scd_date']))?></strong>
		</p>
		<p class="list_info_p">
			<span>예약 시간</span>
			<strong><?php echo sprintf('%02d', $row['scd_start'])?>:00 ~ <?php echo sprintf('%02d', $row['scd_end']+1)?>:00</strong>
		</p>
		<p class="list_info_p">
			<span>예약 구장</span>
			<strong><?php echo $row['as_name']?></strong>
		</p>
		<p class="list_info_p">
			<span>경기 종류</span>
			<strong><?php if($row['scd_match_type'] == 1){ echo "자체";  }else{ echo "매치";  }?></strong>
		</p>
		<p class="list_info_p">
			<span>예약자 명</span>
			<strong><?php echo $row['scd_name']?></strong>
		</p>
		<p class="list_info_p">
			<span>전화번호</span>
			<strong><?php echo $row['scd_hp']?></strong>
		</p>
		<p class="list_info_p">
			<span>팀 명</span>
			<strong><?php if($row['scd_team_name']){ echo $row['scd_team_name']; }else{ echo "-";  }?></strong>
		</p>
		<p class="list_info_p">
			<span>연령대</span>
			<strong><?php echo $ageVal?></strong>
		</p>
	</div>
	<div class="list_btn_box">
		<?php if ($row['scd_res_cert'] == 0) {?>
			<button type="button" class="list_btn ver3" onClick="fnRes('<?php echo $row['scd_idx']?>', '2')">예약취소</button>
			<button type="button" class="list_btn ver4" onClick="fnRes('<?php echo $row['scd_idx']?>', '1')">예약승인</button>
		<?php }else if ($row['scd_res_cert'] == 1) {?>
			<button type="button" class="list_btn ver2 ver3" onClick="fnRes('<?php echo $row['scd_idx']?>', '2')">예약취소</button>
		<?php }else if ($row['scd_res_cert'] == 2) {?>
			<button type="button" class="list_btn" disabled>예약취소</button>
			<button type="button" class="list_btn" disabled>예약승인</button>
		<?php }?>
	</div>
</li>
<?php }?>
<?php if($totalCnt < 1){?>
<li class="not_data">예약 내역이 없습니다.</li>
<?php }?>