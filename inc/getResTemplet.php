<?php
	include_once("_common.php");
	
	$dateReplace = str_replace(". ", "-", $date);
	$time_ary = array();
	//$sql2 = " select * from a_schedule_res_time where scd_idx = '{$idx}' ";
	$sql2 = " select * from a_schedule_res_time A, a_schedule_res B
						where 1
							and A.scd_idx = B.scd_idx
							and A.scdt_date = '{$dateReplace}' 
							and A.as_idx = '{$as_idx}' 
							and B.delete_state = 1
						";
	$result2 = sql_query($sql2);
	for($i=0; $row2=sql_fetch_array($result2); $i++){
		array_push($time_ary, $row2['scdt_time']);
	}
?>

<p class="regi_th">시간대<span>*</span></p>
<div class="regi_td frm_btn_flex regi_td_time">
	<select name="scd_start" id="mb_fs_start" class="regi_ipt req_ipt regi_select2 regi_time ver3" onChange="getEndTimeList(this.value); fnValueCount();">
		<?php 
			for($i=$member['mb_fs_start']; $i<($member['mb_fs_end']-1); $i++){
				$use = false;				
				if($w=="u" && $idx){
					for($j=0; $j<count($time_ary); $j++){
						if($time_ary[$j] == $i){
							$use = true;
						}
					}
				}

				$baseResultDate = $dateReplace." ".sprintf('%02d', $i).":00:00";
				if(G5_TIME_YMDHIS < $baseResultDate){										
	
				$sql_dt = " select count(*) cnt from a_schedule_res_time A, a_schedule_res B
						where 1
							and A.scd_idx = B.scd_idx
							and A.scdt_date = '{$dateReplace}' 
							and A.as_idx = '{$as_idx}'
							and A.scdt_time = '{$i}'
							and B.delete_state = 1
						";
				$row_dt = sql_fetch($sql_dt);				
				if($use || $row_dt['cnt'] < 1){
		?>
		<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
		<?php }}}?>
	</select>
	<span>~</span>
	<select name="scd_end" id="mb_fs_end" class="regi_ipt req_ipt regi_select2 regi_time ver3" onChange="fnValueCount();"></select>
</div>