<?php
	include_once("_common.php");
	
	$total_res = false;
	$sql = " select * from a_schedule_res where scd_idx = '{$scd_idx}' ";
	$row = sql_fetch($sql);

	$sql2 = " select scdt_type from a_schedule_res_time where scd_idx = '{$scd_idx}' and as_idx = '{$row['as_idx']}' and scdt_date = '{$row['scd_date']}' order by scdt_idx desc limit 1 ";
	$row2 = sql_fetch($sql2);
	$type = $row2['scdt_type']+1;

	$lastTime = "";
	for($i=0; $i<count($add_time); $i++){
		$sql_cnt = " select count(*) cnt from a_schedule_res_time A, a_schedule_res B where A.scd_idx = B.scd_idx and A.scd_idx = '{$scd_idx}' and A.scdt_date = '{$row['scd_date']}' and A.as_idx = '{$row['as_idx']}' and A.scdt_time = '{$add_time[$i]}' and B.delete_state = 1 ";
		$row_cnt = sql_fetch($sql_cnt);
		
		if($row_cnt['cnt'] > 0){
			$total_res = false;
			break;
		}else{
			$sql = " insert into a_schedule_res_time set
							scd_idx = '{$scd_idx}'
							, as_idx = '{$row['as_idx']}'
							, scdt_date = '{$row['scd_date']}'
							, scdt_time = '{$add_time[$i]}'
							, scdt_type = '{$type}'
							";
			sql_query($sql);
		
			$lastTime = $add_time[$i];
			$total_res = true;
		}
	}
	
	if($lastTime){
		$sql = " update a_schedule_res set
						scd_end = '{$lastTime}'
						, scd_end_org = '{$row['scd_end']}'
						, scd_updatetime = now()
						where scd_idx = '{$scd_idx}'
						";
		sql_query($sql);
	}

	if($total_res){
		echo "1111";
	}else{
		echo "0000";
	}
?>