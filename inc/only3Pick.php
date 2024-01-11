<?php
	include_once("_common.php");
	
	for($i=0; $i<count($pick_chk); $i++){
		$sql = " select * from g5_member where mb_no = '{$pick_chk[$i]}' ";
		$row = sql_fetch($sql);

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
<li id="pick_list_<?php echo $row['mb_no']?>">
	<input type="hidden" name="fs_idx[]" value="<?php echo $row['mb_no']?>">
	<input type="hidden" name="fs_idx_name[]" value="<?php echo $row['mb_fs_name']?>">
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
	<button type="button" class="picked_delete" onClick="delete_li('pick_list_<?php echo $row['mb_no']?>');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_trash.svg" alt="">
		<span>삭제</span>
	</button>
</li>
<?php }?>