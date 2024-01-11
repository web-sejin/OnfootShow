<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$today = date("Y-m");
	$prevMonth = date("Y-m", strtotime($today." -1 month"));
	$nextMonth = date("Y-m", strtotime($today." +1 month"));

	$sql = " SELECT SUM(scd_score1) sum, SUM(scd_tention1) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND mb_idx = '{$member['mb_no']}' 
							AND scd_team_idx = '{$member['mb_user_team1']}'
							AND scd_score1 > 0
						";
	$row = sql_fetch($sql);
	
	$sql2 = " SELECT SUM(scd_score2) sum, SUM(scd_tention2) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND scd_vs_team_mb_idx = '{$member['mb_no']}'
							AND scd_vs_team_at_idx = '{$member['mb_user_team1']}'
							AND scd_score2 > 0
						";
	$row2 = sql_fetch($sql2);

	$score = 0;
	$tention = 0;
	$matchCnt = $row['cnt']+$row2['cnt'];
	if($matchCnt > 0){
		$score = round(($row['sum']+$row2['sum'])/$matchCnt, 1);
		$tention = round(($row['sum2']+$row2['sum2'])/$matchCnt, 1);
	}
?>

<div class="my_score_area cm_padd6">
	<div class="ms_team_info">
		<input type="hidden" id="curr_date" value="<?php echo $today?>">
		<select id="team_select" class="my_score_select" onChange="viewRecord();">
			<?php for($t=1; $t<=3; $t++){?>
				<?php if($member['mb_user_team'.$t]){?>
				<option value="<?php echo $member['mb_user_team'.$t]?>">
					<?php echo getTeamName($member['mb_user_team'.$t])?>
				</option>
				<?php }?>
			<?php }?>
		</select>
		<ul class="ms_score_list">
			<li>
				<p class="ms_score_tit">경기 평점</p>
				<p class="ms_score_cont">
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg">
					<span id="score_val"><?php echo $score?></span>
				</p>
			</li>
			<li>
				<p class="ms_score_tit">텐션</p>
				<p id="tention_val" class="ms_score_cont"><?php echo $tention?></p>
			</li>
		</ul>
	</div>

	<div class="ms_record">
		<p class="ms_record_tit">경기 전적</p>
		<div id="record_area">
			<div class="ms_record_date">
				<button type="button" onClick="instDate('<?php echo $prevMonth?>');">
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_prev.svg" alt="">
				</button>
				<strong><?php echo date("Y. m", strtotime($today))?></strong>
				<button type="button" onClick="instDate('<?php echo $nextMonth?>');">
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_next.svg" alt="">
				</button>
			</div>
			<?php
				$sql_vs = " SELECT COUNT(*) cnt
										FROM a_match A, a_schedule_res B
										WHERE 1 
											AND A.scd_idx = B.scd_idx
											AND (at_idx = '{$member['mb_user_team1']}' or at_vs_idx = '{$member['mb_user_team1']}') 
											AND B.scd_state = 1
											AND B.delete_state = 1
											AND B.scd_result1 != 0
											AND B.scd_result2 != 0
											AND LEFT(B.scd_date, 7) = '{$today}'
										";
				$row_vs = sql_fetch($sql_vs);
				$vsCnt = $row_vs['cnt'];
			?>
			<ul class="ms_record_ul">
				<?php
					$sql_vs = " SELECT A.at_idx, A.mb_idx, A.at_vs_idx, B.scd_result1, B.scd_result2, A.am_date
											FROM a_match A, a_schedule_res B
											WHERE 1 
												AND A.scd_idx = B.scd_idx
												AND (at_idx = '{$member['mb_user_team1']}' or at_vs_idx = '{$member['mb_user_team1']}') 
												AND B.scd_state = 1
												AND B.delete_state = 1
												AND B.scd_result1 != 0
												AND B.scd_result2 != 0
												AND LEFT(B.scd_date, 7) = '{$today}'
											ORDER BY A.am_date DESC, B.scd_end DESC
											";
					$result_vs = sql_query($sql_vs);
					for($v=0; $vs=sql_fetch_array($result_vs); $v++){
				?>
				<li>
					<p class="ms_detail_date"><?php echo date("m. d", strtotime($vs['am_date']))?></p>
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
				<li class="not_data">경기 전적이 없습니다.</li>
				<?php }?>
			</ul>
		</div>
	</div>
</div>

<script>
function instDate(v){
	$("#curr_date").val(v);
	viewRecord();
}

function viewRecord(){		
	getMyScore();	
	getMyRecord();
}

function getMyScore(){
	const selectVal = $("#team_select").val();
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/getMyScore.php",
		data: {at_idx:selectVal}, 
		dataType : "json",
		cache: false,
		async: false,
		//contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);
			$("#score_val").text(data.score);
			$("#tention_val").text(data.tention);
		}
	});
}

function getMyRecord(){
	const selectVal = $("#team_select").val();
	const dayVal = $("#curr_date").val();
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/getMyRecord.php",
		data: {at_idx:selectVal, day:dayVal}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);
			$("#record_area").empty().append(data);
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>