<?php
	include_once("_common.php");

	$sql = " select * from g5_member where mb_no = '{$mb_idx}' ";
	$mb = sql_fetch($sql);

	$curr_date = $getDate;
	$curr_stadium = $getStadium;
	$curr_price = getStadiumPrice($getStadium);

	$sql3 = " select * from a_stadium where as_idx = '{$curr_stadium}' ";
	$row3 = sql_fetch($sql3);
	$curr_price = $row3['as_price'];
	$curr_size = $row3['as_size'];

	$ary1 = array();
	$exp = explode("|", $row3['as_to']);
	for($x=0; $x<count($exp); $x++){
		array_push($ary1, $exp[$x]);	
	}
	$ary1 = array_values(array_unique($ary1));
	sort($ary1);

	$to = "";
	for($x=0; $x<count($ary1); $x++){
		if($x != 0){ $to .= ", ";  }
		if($ary1[$x] == "4"){
			$to .= $ary1[$x].":".$ary1[$x]."이하";	
		}else if($ary1[$x] == "7"){
			$to .= $ary1[$x].":".$ary1[$x]."이상";
		}else{
			$to .= $ary1[$x].":".$ary1[$x];
		}								
	}

	$curr_sort = array_search($row3['as_sort'], array_column($_cfg['stadium']['sort'], 'val'));
	$curr_floor = array_search($row3['as_floor'], array_column($_cfg['stadium']['floor'], 'val'));
?>
<ul class="str_info_ul">
	<li><?php echo number_format($curr_price)?>/시간</li>
	<li><?php echo $curr_size?>m (<?php echo $to?>) <?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?> <?php echo $_cfg['stadium']['floor'][$curr_floor]['txt'];?></li>
</ul>
<p class="str_info_desc">* 최소 2시간 부터 예약 가능합니다.</p>
<ul class="str_time_ul">
	<?php
		for($i=$time1; $i<$mb['mb_fs_end']; $i++){
			$sql2 = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx
							from a_schedule_res A, a_schedule_res_time B
							where 1
								and A.scd_idx = B.scd_idx
								and A.mb_idx = '{$mb['mb_no']}' 
								and A.as_idx = '{$curr_stadium}' 
								and A.scd_date = '{$curr_date}' 											
								and A.delete_state = 1 
								and B.scdt_time = {$i}
								and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
							";
			$row2 = sql_fetch($sql2);

			$use_state = true;						
			$hour = $i;

			if($row2['scd_start']){
				$hour = $row2['scd_start'];
			}
			$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
			if(G5_TIME_YMDHIS >= $this_date){
				$use_state = false;
			}

			if($row2['cnt'] < 1 && $use_state){
	?>
	<li id="scd_li_<?php echo $i?>" class="str_time_li">
		<?php if($i == $time1 || $i == $time2){?><button type="button" class="not_uncheck" onClick="notUncheck();"></button><?php }?>
		<input type="checkbox" name="time_chk[]" id="tc_<?php echo $i?>" value="<?php echo $i?>" onChange="sideChk('<?php echo $i?>');">
		<label for="tc_<?php echo $i?>">
			<span><?php echo sprintf('%02d', $i);?>:00 ~ <?php echo sprintf('%02d', ($i+1));?>:00</span>
			<strong><?php echo number_format($curr_price)?></strong>
		</label>
	</li>
	<?php }}?>
</ul>