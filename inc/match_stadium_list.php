<?php
	include_once("_common.php");

	$sql = " select * from rb_sido where sd_idx = '{$sd_idx}' ";
	$sido = sql_fetch($sql);
	$sido = $sido['sd_name'];

	$sql = " select * from rb_sigungu where si_idx = '{$si_idx}' ";
	$sigugn = sql_fetch($sql);
	if($si_idx == "all"){
		$sigugn = "전체";
		$dong = "";
	}else{
		$sigugn = $sigugn['si_name'];	
	}
	
	if($si_idx != "all"){
		$sql = " select * from rb_dongli where do_idx = '{$do_idx}' ";
		$dong = sql_fetch($sql);
		if($do_idx == "all"){
			$dong = "전체";		
		}else{
			$dong = $dong['do_name'];
		}
	}

	if($area == "1"){ //지역 단위
?>
<li id="local_sort_li">
	<h3 class="ust_name"><?php echo $sido?> <?php echo $sigugn?> <?php echo $dong?></h3>
	<ul class="ust_sub_info2">
		<?php
			if($pop_size){
			$exp1 = explode("|", $pop_size);
			for($e=0; $e<count($exp1); $e++){
				$key = array_search($exp1[$e], array_column($_cfg['stadium']['to'], 'val'));
		?>
		<li><?php echo $_cfg['stadium']['to'][$key]['txt'];?></li>
		<?php }}?>

		<?php
			if($pop_sort){
			$exp2 = explode("|", $pop_sort);
			for($e=0; $e<count($exp2); $e++){
				$key = array_search($exp2[$e], array_column($_cfg['stadium']['sort'], 'val'));
		?>
		<li><?php echo $_cfg['stadium']['sort'][$key]['txt'];?></li>
		<?php }}?>

		<?php
			if($pop_use1){
			$exp3 = explode("|", $pop_use1);
			for($e=0; $e<count($exp3); $e++){
				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp3[$e]}' ";
				$use1 = sql_fetch($sql_use);
		?>
		<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
		<?php }}?>

		<?php
			if($pop_use2){
			$exp4 = explode("|", $pop_use2);
			for($e=0; $e<count($exp4); $e++){
				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp4[$e]}' ";
				$use2 = sql_fetch($sql_use);
		?>
		<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
		<?php }}?>

		<?php
			if($pop_use3){
			$exp5 = explode("|", $pop_use3);
			for($e=0; $e<count($exp5); $e++){
				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$exp5[$e]}' ";
				$use3 = sql_fetch($sql_use);
		?>
		<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
		<?php }}?>						
	</ul>
	<button type="button" class="picked_delete" onClick="delete_li('local_sort_li');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_trash.svg" alt="">
		<span>삭제</span>
	</button>
</li>

<?php 
	}else if($area == "2"){ //구장 단위
		$add_query = "";
		if($pop_size){
			$add_query .= " and ( ";
			$exp = explode("|", $pop_size);
			for($i=0; $i<count($exp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " A.as_to like '%{$exp[$i]}%'  ";
			}
			$add_query .= " ) ";
		}

		if($pop_sort){
			$add_query .= " and ( ";
			$exp = explode("|", $pop_sort);
			for($i=0; $i<count($exp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " A.as_sort = '{$exp[$i]}'  ";
			}
			$add_query .= " ) ";
		}

		if($pop_use1){
			$add_query .= " and ( ";
			$exp = explode("|", $pop_use1);
			for($i=0; $i<count($exp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " B.mb_fs_use1 = '{$exp[$i]}'  ";
			}
			$add_query .= " ) ";
		}

		if($pop_use2){
			$add_query .= " and ( ";
			$exp = explode("|", $pop_use2);
			for($i=0; $i<count($exp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " B.mb_fs_use2 = '{$exp[$i]}'  ";
			}
			$add_query .= " ) ";
		}

		if($pop_use3){
			$add_query .= " and ( ";
			$exp = explode("|", $pop_use3);
			for($i=0; $i<count($exp); $i++){
				if($i != 0){ $add_query .= " or ";  }
				$add_query .= " B.mb_fs_use3 = '{$exp[$i]}'  ";
			}
			$add_query .= " ) ";
		}

		if($sd_idx){ $add_query .= " and sd_idx = '{$sd_idx}' "; }
		if($si_idx && $si_idx != "all"){ $add_query .= " and si_idx = '{$si_idx}' "; }
		if($do_idx && $do_idx != "all"){ $add_query .= " and do_idx = '{$do_idx}' "; }

		$sqlCnt = " select count(*) cnt from a_stadium A, g5_member B
											where 1
												and A.mb_idx = B.mb_no												
												and A.as_delete_st = 1 
												and B.mb_leave_status = 1
												{$add_query}
											";
		$rowCnt = sql_fetch($sqlCnt);
		$totalCnt = $rowCnt['cnt'];
		
		$sql_stadium = " select * from a_stadium A, g5_member B
											where 1
												and A.mb_idx = B.mb_no												
												and A.as_delete_st = 1 
												and B.mb_leave_status = 1
												{$add_query}
											group by A.mb_idx
											";
		$result = sql_query($sql_stadium);
		for($i=0; $row=sql_fetch_array($result); $i++){
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

				array_push($ary2, $stadium['as_sort']);
			}

			$ary1 = array_values(array_unique($ary1));
			$ary2 = (array_values(array_unique($ary2)));
			
			sort($ary1);
			sort($ary2);
?>
<li>
	<h3 class="ust_name"><?php echo $row['mb_fs_name']?></h3>
	<ul class="ust_sub_info">
		<li>
			<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
			<span><?php echo $row['mb_fs_tel']?></span>
		</li>
		<li>
			<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
			<span>[<?php echo $row['mb_fs_zip']?>] <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?></span>
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
				$sortKey = array_search($ary2[$s], array_column($_cfg['stadium']['sort'], 'val'));
		?>
		<li><?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?></li>
		<?php }?>
		<li>화장실 (<?php echo $use1['afu_subject']?>)</li>
		<li>샤워실 (<?php echo $use2['afu_subject']?>)</li>
		<li>주차장 (<?php echo $use3['afu_subject']?>)</li>
	</ul>
	<p class="pick_box">
		<input type="checkbox" name="pick_chk[]" id="pick_chk_<?php echo $row['mb_no']?>" value="<?php echo $row['mb_no']?>" onChange="pick_len('<?php echo $row['mb_no']?>');">
		<label for="pick_chk_<?php echo $row['mb_no']?>">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk.svg" alt="">
			<span>선택</span>
		</label>
	</p>
</li>
<?php } ?>
<?php if($totalCnt < 1){?>
<li class="not_data">등록된 풋살장이 없습니다.</li>
<?php }?>
<?php }?>