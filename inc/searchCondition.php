<?php
	include_once("_common.php");

	$sql = " select * from a_match where am_idx = '{$am_idx}' ";
	$row = sql_fetch($sql);
	$startTime = $row['am_time'];
	$endTime = $row['am_time']+1;
	$endTime2 = $row['am_time']+2;

	$add_query = "";

	if($row['am_area'] == 1 || $v == "all"){
		//지역 단위 또는 구장 단위에서 다른 구장 검색
		if($row['pop_size']){
			$add_query .= " and ( ";
			$sizeExp = explode("|", $row['pop_size']);
			for($i=0; $i<count($sizeExp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " as_to like '%{$sizeExp[$i]}%' ";
			}
			$add_query .= " ) ";
		}

		if($row['pop_sort']){
			$add_query .= " and ( ";
			$sortExp = explode("|", $row['pop_sort']);
			for($i=0; $i<count($sortExp); $i++){			
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " as_sort = '{$sortExp[$i]}' ";
			}
			$add_query .= " ) ";
		}
		
		$stadiumList = "";
		$sql2 = " select * from a_stadium where 1 {$add_query} ";
		$result2 = sql_query($sql2);
		for($i=0; $row2=sql_fetch_array($result2); $i++){
			$sql3 = " select count(*) cnt from a_schedule_res_time A, a_schedule_res B
								where 1
									and A.scd_idx = B.scd_idx
									and A.as_idx = '{$row2['as_idx']}' 
									and A.scdt_date = '{$row['am_date']}' 
									and A.scdt_time >= {$startTime}
									and A.scdt_time <= {$endTime}
									and B.scd_res_cert = 1
								";
			$row3 = sql_fetch($sql3);
			if($row3['cnt'] < 1){
				if($stadiumList != ""){ $stadiumList .= ","; }
				$stadiumList .= "'".$row2['as_idx']."'";
			}
		}

		$mbList = "";
		$sql4 = " select * from a_stadium where as_idx IN ({$stadiumList}) group by mb_idx 	";
		$result4 = sql_query($sql4);
		for($i=0; $row4=sql_fetch_array($result4); $i++){
			if($i != 0){ $mbList .= ","; }
			$mbList .= "'".$row4['mb_idx']."'";
		}

		$add_query = "";
		if($row['do_idx'] != "" && $row['do_idx'] != "all"){
			$add_query .= " and do_idx = '{$row['do_idx']}' ";
		}
		
		if($row['pop_use1']){
			$add_query .= " and ( ";
			$useExp = explode("|", $row['pop_use1']);
			for($u=0; $u<count($useExp); $u++){			
				if($u != 0){ $add_query .= " or "; }
				$add_query .= " mb_fs_use1 = '{$useExp[$u]}' ";
			}
			$add_query .= " ) ";
		}

		if($row['pop_use2']){
			$add_query .= " and ( ";
			$useExp = explode("|", $row['pop_use2']);
			for($u=0; $u<count($useExp); $u++){			
				if($u != 0){ $add_query .= " or "; }
				$add_query .= " mb_fs_use2 = '{$useExp[$u]}' ";
			}
			$add_query .= " ) ";
		}

		if($row['pop_use3']){
			$add_query .= " and ( ";
			$useExp = explode("|", $row['pop_use3']);
			for($u=0; $u<count($useExp); $u++){			
				if($u != 0){ $add_query .= " or "; }
				$add_query .= " mb_fs_use3 = '{$useExp[$u]}' ";
			}
			$add_query .= " ) ";
		}

		$type2_query = "";
		if($row['am_area'] == 2){
			$add_query .= " and mb_no NOT IN (";
			for($i=1; $i<=3; $i++){				
				if($row['fs_mb_idx'.$i]){
					if($type2_query != ""){ $type2_query .= ","; }
					$type2_query .= "'".$row['fs_mb_idx'.$i]."'";
				}				
			}
			$add_query .= $type2_query;
			$add_query .= " ) ";
		}
		$sql = " select * from g5_member
						where 1
							and mb_no IN ({$mbList})
							and sd_idx = '{$row['sd_idx']}'
							and si_idx = '{$row['si_idx']}'
							and mb_leave_status = 1
							and mb_fs_end >= {$endTime2}
							{$add_query}
							{$use_query}
						";	
		$result = sql_query($sql);
		$totalCnt = 0;
		for($i=0; $row=sql_fetch_array($result); $i++){
			$totalCnt++;
			$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row['mb_fs_use1']}' ";
			$use1 = sql_fetch($sql_use);
			
			$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row['mb_fs_use2']}' ";
			$use2 = sql_fetch($sql_use);

			$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row['mb_fs_use3']}' ";
			$use3 = sql_fetch($sql_use);
			
			$ary1 = array();
			$ary2 = array();
			$sql_stadium = " select * from a_stadium where mb_idx = '{$row['mb_no']}' and as_delete_st = 1 ";
			$result_stadium = sql_query($sql_stadium);
			for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
				$exp = explode("|", $stadium['as_to']);
				for($x=0; $x<count($exp); $x++){
					array_push($ary1, $exp[$x]);	
				}

				array_push($ary2, $stadium['as_floor']);
			}

			$ary1 = array_values(array_unique($ary1));
			$ary2 = (array_values(array_unique($ary2)));
			
			sort($ary1);
			sort($ary2);
?>
	<li id="condi_li_<?php echo $row['mb_no']?>" class="condi_li" onClick="pick('<?php echo $row['mb_no']?>')">
		<h3 class="ust_name"><?php echo $row['mb_fs_name']?></h3>
		<ul class="ust_sub_info">
			<li>
				<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
				<span><?php echo $row['mb_fs_tel']?></span>
			</li>
			<li>
				<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
				<span>[<?php echo $row['mb_fs_zip']?>] <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?> <?php echo $fs['mb_fs_addr3']?></span>
			</li>
		</ul>
		<ul class="ust_sub_info2">
			<?php 
				for($s=0; $s<count($ary1); $s++){				
					$toKey = array_search($ary1[$s], array_column($_cfg['stadium']['to'], 'val'));
			?>
			<li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li>
			<?php }?>
			<?php 
				for($s=0; $s<count($ary2); $s++){
					$floorKey = array_search($ary2[$s], array_column($_cfg['stadium']['floor'], 'val'));
			?>
			<li><?php echo $_cfg['stadium']['floor'][$floorKey]['txt'];?></li>
			<?php }?>
			<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
			<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
			<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
		</ul>
	</li>
	<?php }?>
	<?php if($totalCnt < 1){?>
	<li class="not_data">조건에 일치하는 다른 구장이 없습니다.</li>
	<?php }?>
<?php }else if($row['am_area'] == 2){ //구장 단위?>
		<?php 
		for($i=1; $i<=3; $i++){
			$mb_idx = $row['fs_mb_idx'.$i];
			if($mb_idx){
				$sql_fs = " select * from g5_member where mb_no = '{$mb_idx}' ";
				$fs = sql_fetch($sql_fs);

				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$fs['mb_fs_use1']}' ";
				$use1 = sql_fetch($sql_use);
				
				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$fs['mb_fs_use2']}' ";
				$use2 = sql_fetch($sql_use);

				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$fs['mb_fs_use3']}' ";
				$use3 = sql_fetch($sql_use);
				
				$ary1 = array();
				$ary2 = array();
				$sql_stadium = " select * from a_stadium where mb_idx = '{$mb_idx}' and as_delete_st = 1 ";
				$result_stadium = sql_query($sql_stadium);
				for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
					$exp = explode("|", $stadium['as_to']);
					for($x=0; $x<count($exp); $x++){
						array_push($ary1, $exp[$x]);	
					}

					array_push($ary2, $stadium['as_floor']);
				}

				$ary1 = array_values(array_unique($ary1));
				$ary2 = (array_values(array_unique($ary2)));
				
				sort($ary1);
				sort($ary2);
				
				$stadiumCnt = 0;
				$sql2 = " select * from a_stadium where mb_idx = '{$mb_idx}' ";
				$result2 = sql_query($sql2);
				for($x=0; $row2=sql_fetch_array($result2); $x++){
					$sql3 = " select count(*) cnt from a_schedule_res_time A, a_schedule_res B
								where 1
									and A.scd_idx = B.scd_idx
									and A.as_idx = '{$row2['as_idx']}' 
									and A.scdt_date = '{$row['am_date']}' 
									and A.scdt_time >= {$startTime}
									and A.scdt_time <= {$endTime}
									and B.scd_res_cert = 1
								";
					$row3 = sql_fetch($sql3);
					if($row3['cnt'] < 1){
						$stadiumCnt++;
					}
				}
		?>
		<li id="condi_li_<?php echo $mb_idx?>" class="condi_li" <?php if($stadiumCnt > 0){?>onClick="pick('<?php echo $mb_idx?>')"<?php }?>>
			<h3 class="ust_name"><?php echo $row['fs_mb_name'.$i]?></h3>
			<ul class="ust_sub_info">
				<li>
					<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
					<span><?php echo $fs['mb_fs_tel']?></span>
				</li>
				<li>
					<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
					<span>[<?php echo $fs['mb_fs_zip']?>] <?php echo $fs['mb_fs_addr1']?> <?php echo $fs['mb_fs_addr2']?> <?php echo $fs['mb_fs_addr3']?></span>
				</li>
			</ul>
			<ul class="ust_sub_info2">
				<?php 
					for($s=0; $s<count($ary1); $s++){				
						$toKey = array_search($ary1[$s], array_column($_cfg['stadium']['to'], 'val'));
				?>
				<li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li>
				<?php }?>
				<?php 
					for($s=0; $s<count($ary2); $s++){
						$floorKey = array_search($ary2[$s], array_column($_cfg['stadium']['floor'], 'val'));
				?>
				<li><?php echo $_cfg['stadium']['floor'][$floorKey]['txt'];?></li>
				<?php }?>
				<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
				<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
				<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
			</ul>
			<?php if($stadiumCnt > 0){?>
				<span class="res_use_state ver2">예약가능</span>
			<?php }else{?>
				<span class="res_use_state">예약불가</span>
			<?php }?>
		</li>
		<?php }}?>
<?php }?>