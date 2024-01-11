<?php
	include_once("_common.php");

	$date = str_replace(". ", "-", $scdDate);
	$ary = array();

	$time_ary = array();
	$sql2 = " select * from a_schedule_res_time where scd_idx = '{$idx}' and scdt_time != '{$startTime}' and as_idx = '{$as_idx}' ";
	$result2 = sql_query($sql2);
	for($i=0; $row2=sql_fetch_array($result2); $i++){
		array_push($time_ary, $row2['scdt_time']);
	}

	for($i=($startTime+1); $i<$member['mb_fs_end']; $i++){	
		$use = false;
		$sql = " select count(*) cnt from a_schedule_res_time A,  a_schedule_res B where A.scdt_date = '{$date}' and A.scdt_time = '{$i}' and A.as_idx = '{$as_idx}' and A.scd_idx = B.scd_idx and B.delete_state = 1 ";
		echo $sql."<br>";
		$row = sql_fetch($sql);

		if($w=="u" && $idx){
			for($j=0; $j<count($time_ary); $j++){
				if($time_ary[$j] == $i){
					$use = true;
				}
			}
		}

		if($use || $row['cnt'] < 1){
			array_push($ary, $i);
		}else{
			break;
		}
	}

	if(count($ary) > 0){
	for($i=0; $i<count($ary); $i++){
		echo $sql;
?>
<option value="<?php echo $ary[$i]?>"><?php echo sprintf('%02d', $ary[$i]+1)?>:00</option>
<?php 
		}
	}else{
?>
<option value="">선택가능한 시간이 없습니다.</option>
<?php }?>