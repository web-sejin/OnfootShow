<?php
	include_once("_common.php");

	$time1 = $member['mb_fs_start'];
	$time2 = $member['mb_fs_end'];

	$curr_date = str_replace(". ", "-", $currDate);
	$sql = " select * from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 order by as_name asc, as_updatetime desc ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){						
?>
<ul class="all_scd_tb">
	<li><?php echo $row['as_name']?></li>
	<?php 
		for($j=$time1; $j<$time2; $j++){
				$sql2 = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx, A.scd_res_cert
							from a_schedule_res A, a_schedule_res_time B
							where 1
								and A.scd_idx = B.scd_idx
								and A.as_idx = '{$row['as_idx']}' 
								and A.scd_date = '{$curr_date}' 											
								and A.delete_state = 1 
								and B.scdt_time = {$j}
								and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
							";
			$row2 = sql_fetch($sql2);
			
			$use_state = true;
			$bgClass = "";
			$bgClass2 = "";
			if($row2['scd_match_type'] == 1){ $bgClass = "blue"; }
			if($row2['scd_match_type'] == 2){ 
				if($row2['scd_state'] == 1){
					$bgClass = "blue";
				}else{
					$bgClass = "green"; 
				}
			}
			if($row2['scd_match_sort'] == 2){ $bgClass = "red"; }
			$time_pick_ipt = $j."|".($j+1);

			$hour = $j;
			if($row2['scd_start']){
				$hour = $row2['scd_start'];
			}
			$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
			if(G5_TIME_YMDHIS >= $this_date){
				$use_state = false;
				$bgClass2 = "gray"; 
			}			
	?>					
	<li>		
		<?php if($row2['cnt'] > 0){?>
			<?php if($row2['scd_res_cert'] == 0){?>
				<a href="<?php echo G5_URL?>/reserve_list.php">예약확인</a>
			<?php }else{?>
				<a href="<?php echo G5_URL?>/schedule_write.php?w=u&idx=<?php echo $row2['scd_idx']?>&url=1" class="scd_state <?php echo $bgClass?>">
					<?php if($row2['scd_match_type'] == 3){?>임의예약<?php }?>				
				</a>
			<?php }?>
		<?php }else{?>
			<?php if($j+1 == $time2){?>
				<a onClick="javascript:showToast('마지막 시간은 등록할 수 없습니다.');" class="scd_state <?php echo $bgClass?> <?php echo $bgClass2?>"></a>
			<?php }else{?>
				<a <?php if($use_state){?>href="<?php echo G5_URL?>/schedule_write.php?s_datepicker=<?php echo $curr_date?>&time_pick_ipt=<?php echo $time_pick_ipt?>&url=1"<?php }?> class="scd_state <?php echo $bgClass?> <?php echo $bgClass2?>">
				</a>
			<?php }?>
		<?php }?>						
	</li>
	<?php }?>
</ul>
<?php }?>