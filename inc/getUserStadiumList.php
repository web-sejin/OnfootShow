<?php
	include_once("_common.php");

	if($is_member){
		if($member['mb_lat'] && $member['mb_lng']){
			$myLat = $member['mb_lat'];
			$myLon = $member['mb_lng'];
		}else{
			$myLat = $_SESSION['lat'];
			$myLon = $_SESSION['lng'];	
		}
	}else{
		$myLat = $_SESSION['lat'];
		$myLon = $_SESSION['lng'];
	}

	$mbList = "";	
	$common = " AND mb_type = 2 AND mb_leave_status = 1 	AND mb_cert = 1 ";
	$add_query = "";

	if($start != ""){ $add_query .= " and mb_fs_start >= '{$start}' "; }
	if($end != ""){ $add_query .= " and mb_fs_end <= '{$end}' "; }
	if($sd_idx){ $add_query .= " and sd_idx = '{$sd_idx}' "; }
	if($si_idx){ $add_query .= " and si_idx = '{$si_idx}' "; }
	
	if($use1List){
		$exp_query = "";
		$exp = explode("|", $use1List);
		for($i=0; $i<count($exp); $i++){
			if($i == 0){ $exp_query .= " and ( ";  }
			if($i != 0){ $exp_query .= " or ";  }
			$exp_query .= " mb_fs_use1 = '{$exp[$i]}' ";
		}
		$exp_query .= " ) ";
		$add_query .= $exp_query;
	}

	if($use2List){
		$exp_query = "";
		$exp = explode("|", $use2List);
		for($i=0; $i<count($exp); $i++){
			if($i == 0){ $exp_query .= " and ( ";  }
			if($i != 0){ $exp_query .= " or ";  }
			$exp_query .= " mb_fs_use2 = '{$exp[$i]}' ";
		}
		$exp_query .= " ) ";
		$add_query .= $exp_query;
	}

	if($use3List){
		$exp_query = "";
		$exp = explode("|", $use3List);
		for($i=0; $i<count($exp); $i++){
			if($i == 0){ $exp_query .= " and ( ";  }
			if($i != 0){ $exp_query .= " or ";  }
			$exp_query .= " mb_fs_use3 = '{$exp[$i]}' ";
		}
		$exp_query .= " ) ";
		$add_query .= $exp_query;
	}

	if($subject != ""){
		$add_query .= " and mb_fs_name like '%{$subject}%' ";	
	}

	$sql = " select * from g5_member 
					where 1
						{$common}
						{$add_query}
					";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		if($i != 0){ $mbList .= ",";  }
		$mbList .= "'".$row['mb_no']."'";
	}
	
	$mbList2 = "";	
	$add_query2 = "";
	if($toList){
		$exp_query = "";
		$exp = explode("|", $toList);
		for($i=0; $i<count($exp); $i++){
			if($i == 0){ $exp_query .= " and ( ";  }
			if($i != 0){ $exp_query .= " or ";  }
			$exp_query .= " as_to like '%{$exp[$i]}%' ";
		}
		$exp_query .= " ) ";
		$add_query2 .= $exp_query;
	}

	if($floorList){
		$exp_query = "";
		$exp = explode("|", $floorList);
		for($i=0; $i<count($exp); $i++){
			if($i == 0){ $exp_query .= " and ( ";  }
			if($i != 0){ $exp_query .= " or ";  }
			$exp_query .= " as_sort = '{$exp[$i]}' ";
		}
		$exp_query .= " ) ";
		$add_query2 .= $exp_query;
	}

	$sql = " select * from a_stadium
					where 1
						and mb_idx IN ({$mbList})
						{$add_query2}
					group by mb_idx					
					";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		if($i != 0){ $mbList2 .= ",";  }
		$mbList2 .= "'".$row['mb_idx']."'";
	}
	
	$local = false;
	$sql = " SELECT COUNT(*) cnt FROM g5_member WHERE mb_no IN ({$mbList2}) ";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	$sql_order = " ORDER BY mb_fs_name ASC, mb_datetime DESC ";
	if($od){
		if($od == "up"){
			$sql_order = " ORDER BY mb_fs_res_cnt DESC, mb_fs_name ASC, mb_datetime DESC ";
		}else if($od == "down"){
			$sql_order = " ORDER BY mb_fs_row_price ASC, mb_fs_name ASC, mb_datetime DESC ";
		}
	}
	
	$sql = " SELECT * FROM g5_member WHERE mb_no IN ({$mbList2}) {$sql_order} limit {$limitVal}, 2 ";
	$result = sql_query($sql);

	if($myLat != "" && $myLat != "00" && $myLon != "" && $myLon != "0"){
		$local = true;
		$sql_order = "";
		if($od){
			if($od == "name"){
				$sql_order = " ORDER BY mb_fs_name ASC, distance ASC ";
			}else if($od == "distance"){
				$sql_order = " ORDER BY distance ASC, mb_fs_name ASC, mb_datetime DESC ";
			}else if($od == "up"){
				$sql_order = " ORDER BY mb_fs_res_cnt DESC, mb_fs_name ASC, distance ASC, mb_datetime DESC ";
			}else if($od == "down"){
				$sql_order = " ORDER BY mb_fs_row_price ASC, mb_fs_name ASC, distance ASC, mb_datetime DESC ";
			}
		}

		$sql = " SELECT count(*) cnt,
			(6371*acos(cos(radians({$myLat}))*cos(radians(mb_fs_lat))*cos(radians(mb_fs_lng)
			-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(mb_fs_lat)))) AS distance
			FROM g5_member
			WHERE mb_no IN ({$mbList2}) 
		";	
		$row = sql_fetch($sql);
		$total_count = $row['cnt'];

		$sql = " SELECT *,
			(6371*acos(cos(radians({$myLat}))*cos(radians(mb_fs_lat))*cos(radians(mb_fs_lng)
			-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(mb_fs_lat)))) AS distance
			FROM g5_member
			WHERE mb_no IN ({$mbList2}) 
			{$sql_order}
			limit {$limitVal}, 10
		";	
		$result = sql_query($sql);
	}
	
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
<li class="user_stadium_li">
	<a onClick="moveView('<?php echo $row['mb_no']?>');">
		<p class="ust_km">	
			<?php if($local){?>
				<?php	echo round($row['distance'], 2); ?><span>km</span>						
			<?php }else{?>
				위치권한없음
			<?php }?>
		</p>
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
		<ul class="ust_list">
			<?php
				$result_stadium = sql_query($sql_stadium);
				for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
					$ary1 = array();
					$exp = explode("|", $stadium['as_to']);
					for($x=0; $x<count($exp); $x++){
						array_push($ary1, $exp[$x]);	
					}
					$sortKey = array_search($stadium['as_sort'], array_column($_cfg['stadium']['sort'], 'val'));
					$floorKey = array_search($stadium['as_floor'], array_column($_cfg['stadium']['floor'], 'val'));
			?>
			<li>
				<p>
					<?php echo $stadium['as_name']?> - <?php echo $stadium['as_size']?> (5:5) <?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?> <?php echo $_cfg['stadium']['floor'][$floorKey]['txt'];?>
				</p>
				<p><strong><?php echo number_format($stadium['as_price'])?>원</strong><span>/</span>시간</p>
			</li>
			<?php }?>
		</ul>
	</a>
</li>
<?php }?>