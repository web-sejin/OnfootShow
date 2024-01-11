<?php
	include_once("_common.php");

	$prevMonth = date("Y-m", strtotime($day." -1 month"));
	$nextMonth = date("Y-m", strtotime($day." +1 month"));

	$sql_vs = " SELECT COUNT(*) cnt
							FROM a_match A, a_schedule_res B
							WHERE 1 
								AND A.scd_idx = B.scd_idx
								AND (at_idx = '{$at_idx}' or at_vs_idx = '{$at_idx}') 
								AND B.scd_state = 1
								AND B.delete_state = 1
								AND B.scd_result1 != 0
								AND B.scd_result2 != 0
								AND LEFT(B.scd_date, 7) = '{$day}'
							";
	$row_vs = sql_fetch($sql_vs);
	$vsCnt = $row_vs['cnt'];
?>

<div class="ms_record_date">
	<button type="button" onClick="instDate('<?php echo $prevMonth?>');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_prev.svg" alt="">
	</button>
	<strong><?php echo date("Y. m", strtotime($day))?></strong>
	<button type="button" onClick="instDate('<?php echo $nextMonth?>');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_next.svg" alt="">
	</button>
</div>
<ul class="ms_record_ul">
	<?php
		$sql_vs = " SELECT A.at_idx, A.mb_idx, A.at_vs_idx, B.scd_result1, B.scd_result2, A.am_date
								FROM a_match A, a_schedule_res B
								WHERE 1 
									AND A.scd_idx = B.scd_idx
									AND (at_idx = '{$at_idx}' or at_vs_idx = '{$at_idx}') 
									AND B.scd_state = 1
									AND B.delete_state = 1
									AND B.scd_result1 != 0
									AND B.scd_result2 != 0
									AND LEFT(B.scd_date, 7) = '{$day}'
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