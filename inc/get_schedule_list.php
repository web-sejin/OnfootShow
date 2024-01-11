<?php
	include_once("_common.php");

	$curr_date = str_replace(". ", "-", $s_datepicker);
	$curr_stadium = $s_stadium;

	for($i=$member['mb_fs_start']; $i<$member['mb_fs_end']; $i++){		
		$sql = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx, A.scd_name, A.scd_res_cert
		from a_schedule_res A, a_schedule_res_time B
		where 1
			and A.scd_idx = B.scd_idx
			and A.as_idx = '{$curr_stadium}' 
			and A.scd_date = '{$curr_date}' 											
			and A.delete_state = 1 
			and B.scdt_time = {$i}
			and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
		";
		$row = sql_fetch($sql);
		//예약없이 시간이 지났거나 시간이 지난 경기 = 회색 , scd_gray
		//매치가 잡히지 않은 경기 = 초록색 , scd_green
		//자체경기 또는 매치확정 경기 = 파란색 , scd_blue
		//고정팀 = 빨간색 , scd_red
		//관리자에 의한 예약막음 = 임의예약
		
		$use_state = true;
		$bgClass = "";
		if($row['scd_match_type'] == 1){ $bgClass = "scd_blue"; }
		if($row['scd_match_type'] == 2){ 
			if($row['scd_vs_team_idx']){
				$bgClass = "scd_blue";
			}else{
				$bgClass = "scd_green"; 
			}
		}
		if($row['scd_match_sort'] == 2){ $bgClass = "scd_red"; }

		$hour = $i;
		if($row['scd_start']){
			$hour = $row['scd_start'];
		}
		$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
		if(G5_TIME_YMDHIS >= $this_date){
			$use_state = false;
			$bgClass = "scd_gray"; 
		}
?>
<li id="scd_li_<?php echo $i?>" class="scd_li <?php if($row['scd_res_cert'] != 0){ echo $bgClass; }?>">				
	<?php if($row['cnt'] > 0){ ?>
		<?php if($row['scd_res_cert'] == 0){?>
			<a onClick="pageChange('<?php echo G5_URL?>/reserve_list.php');">
		<?php }else{?>
			<a onClick="pageChange('<?php echo G5_URL?>/schedule_write.php?w=u&idx=<?php echo $row['scd_idx']?>&url=2');">
		<?php }?>		
	<?php }?>

	<input type="checkbox" name="time_chk[]" id="tc_<?php echo $i?>" value="<?php echo $i?>" onChange="sideChk('<?php echo $i?>');" <?php if($row['cnt'] > 0 || !$use_state){ echo "disabled"; }?>>
	<label for="tc_<?php echo $i?>">
		<strong class="scd_strong1">
			<span class="scd_chk_circle"></span>
			<span class="scd_chk_time">
				<?php echo sprintf('%02d', $i);?>:00 ~ <?php echo sprintf('%02d', ($i+1));?>:00
			</span>
		</strong>
		
		<?php if($row['cnt'] > 0 && $row['scd_res_cert'] == 0){?>
			<strong class="scd_strong2">예약확인</strong>
		<?php }else{?>
			<?php if($row['cnt'] > 0 && $row['scd_match_type'] != 3){ ?>
			<strong class="scd_strong2">
				<?php if($row['scd_match_sort'] == 2){?>
				<span class="scd_fix_team">고정팀</span>
				<?php }?>

				<span class="scd_match_team">
					<?php 
						if($row['scd_match_type'] == 1){
							if($row['scd_team_name'] != ""){
								echo $row['scd_team_name'];
							}else{
								echo $row['scd_name'];
							}
						}else if($row['scd_match_type'] == 2){
							if($row['scd_state'] == 1){
								echo $row['scd_team_name']." &nbsp; vs &nbsp; ".getOtherTeam($row['scd_vs_team_idx'], 'other_name');
							}else{
								echo $row['scd_team_name']." &nbsp; vs &nbsp; -";
							}
						}
					?>
				</span>
			</strong>
			<?php }?>	
		<?php }?>
		
		<?php if($row['scd_match_type'] == 3){?>
		<strong class="scd_my_res">임의예약</strong>
		<?php }else{?>
		<strong class="scd_type_box">							
			<?php if($row['scd_res_type'] == 1){?><span class="scd_type1"><img src="<?php echo G5_THEME_IMG_URL?>/logo_base.png" alt=""></span><?php }?>
			<?php if($row['scd_match_type'] == 1){?><span class="scd_type2">자체</span><?php }?>
			<?php if($row['scd_match_type'] == 2){?><span class="scd_type3">매치</span><?php }?>
		</strong>
		<?php }?>
	</label>

	<?php if($row['cnt'] > 0){ ?>
	</a>
	<?php }?>
</li>
<?php }?>