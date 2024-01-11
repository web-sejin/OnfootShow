<?php
	include_once("_common.php");

	//if($s_txt != ""){ $add_query .= " and (a.gr_name like '%{$s_txt}%' or a.gr_hp like '%{$s_txt}%' or replace(a.gr_hp, '-', '') like '%{$s_txt}%') "; }
	/*$sql = " select * 
				from a_schedule_res
				where 1
					and mb_idx = '{$member['mb_no']}'
					and as_idx = '{$as_idx}'
					and (scd_name like '%{$v}%' or scd_hp like '%{$v}%' or replace(scd_hp, '-', '') like '%{$v}%')
					GROUP BY scd_hp
					order by scd_name asc
				";*/
	$sql = " select * 
				from a_schedule_res
				where 1
					and mb_idx = '{$member['mb_no']}'
					and (scd_name like '%{$v}%' or scd_hp like '%{$v}%' or replace(scd_hp, '-', '') like '%{$v}%')
					GROUP BY scd_hp
					order by scd_name asc
				";
	$result = sql_query($sql);

	$totalCnt = 0;
	for($i=0; $row=sql_fetch_array($result); $i++){
		$totalCnt++;
?>
<li>
	<input type="radio" name="people" id="people_<?php echo $i?>" value="<?php echo $row['scd_name']?>||<?php echo $row['scd_hp']?>" onChange="peopleRadioChg();">
	<label for="people_<?php echo $i?>">
		<span class="peo_name"><?php echo $row['scd_name']?></span>
		<span class="peo_bar"></span>
		<span class="peo_phone"><?php echo $row['scd_hp']?></span>
	</label>
</li>
<?php }?>
<?php if($totalCnt < 1){?>
<li class="not_data">조건에 일치하는 예약자가 없습니다.</li>
<?php }?>