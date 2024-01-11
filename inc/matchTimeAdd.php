<?php
	include_once("_common.php");
	
	$cnt = 0;
	for($i=($scdEnd+1); $i<$member['mb_fs_end']; $i++){
		$sql2 = " select count(*) cnt from a_schedule_res_time A, a_schedule_res B where A.scd_idx = B.scd_idx and A.scdt_date = '{$baseDate}' and A.as_idx = '{$as_idx}' and A.scdt_time = '{$i}' and B.delete_state = 1 ";
		$row2 = sql_fetch($sql2);
		if($row2['cnt'] > 0){
			break;
		}
		
		if($row2['cnt'] < 1){			
?>
<li>
	<input type="checkbox" name="add_time[]" id="add_time_<?php echo $i?>" value="<?php echo $i?>" onChange="prevChk('<?php echo $cnt?>', this.value);">
	<label for="add_time_<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00 ~ <?php echo sprintf('%02d', $i+1)?>:00</label>
</li>
<?php 
		$cnt = $cnt+1;
		}
	}
?>
<?php if($cnt < 1){?>
<li class="not_data">추가 가능한 시간대가 없습니다.</li>
<?php }?>