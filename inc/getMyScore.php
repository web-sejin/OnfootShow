<?php
	include_once("_common.php");

	$sql = " SELECT SUM(scd_score1) sum, SUM(scd_tention1) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND mb_idx = '{$member['mb_no']}' 
							AND scd_team_idx = '{$at_idx}'
							AND scd_score1 > 0
						";
	$row = sql_fetch($sql);
	
	$sql2 = " SELECT SUM(scd_score2) sum, SUM(scd_tention2) sum2, COUNT(*) cnt
						FROM a_schedule_res 
						WHERE 1
							AND scd_state=1 
							AND scd_idx IS NOT NULL 
							AND scd_vs_team_mb_idx = '{$member['mb_no']}'
							AND scd_vs_team_at_idx = '{$at_idx}'
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

	echo json_encode(array('score'=>$score, 'tention'=>$tention));
?>