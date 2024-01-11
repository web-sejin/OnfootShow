<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select count(*) cnt from a_schedule_res where mb_idx = '{$member['mb_no']}' and delete_state = 1 ";
	$row = sql_fetch($sql);
	$total_cnt = $row['cnt'];
	
	$sql = " select * from a_schedule_res where mb_idx = '{$member['mb_no']}' and delete_state = 1 order by scd_datetime desc ";
	$result = sql_query($sql);
?>

<div class="user_res_list cm_padd4">
	<ul class="url_ul">
		<?php
			for($i=0; $row=sql_fetch_array($result); $i++){
				$sql2 = " select * from a_stadium A, g5_member B where A.mb_idx = B.mb_no and A.as_idx = '{$row['as_idx']}' ";
				$row2 = sql_fetch($sql2);

				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row2['mb_fs_use1']}' ";
				$use1 = sql_fetch($sql_use);
				
				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row2['mb_fs_use2']}' ";
				$use2 = sql_fetch($sql_use);

				$sql_use = " select afu_subject from a_futsal_use where afu_idx = '{$row2['mb_fs_use3']}' ";
				$use3 = sql_fetch($sql_use);

				$ary1 = array();
				$ary2 = array();
				$sql_stadium = " select * from a_stadium where mb_idx = '{$row2['mb_no']}' and as_delete_st = 1 ";
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
		<li class="url_li">
			<a href="<?php echo G5_URL?>/user/member/user_res_detail.php?idx=<?php echo $row['scd_idx']?>">				
				<?php if($row['scd_match_type'] == 1){?>
				<p class="url_type">자체</p>
				<?php }else if($row['scd_match_type'] == 2){?>
				<p class="url_type ver2">매치</p>
				<?php } ?>

				<h3 class="url_name">
					<?php echo $row2['mb_fs_name']?> - <?php echo $row2['as_name']?>
				</h3>
				<p class="url_info1">
					<span><?php echo date("Y. m. d", strtotime($row['scd_date']))?> (<?php echo getYoil($row['scd_date'])?>)</span>
					<span><?php echo sprintf('%02d', $row['scd_start'])?>:00 ~ <?php echo sprintf('%02d', $row['scd_end']+1)?>:00</span>
				</p>
				<ul class="ust_sub_info">				
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>[<?php echo $row2['mb_fs_zip']?>] <?php echo $row2['mb_fs_addr1']?> <?php echo $row2['mb_fs_addr2']?></span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
						<span><?php echo $row2['mb_fs_tel']?></span>
					</li>
				</ul>
				<ul class="url_info2">
					<!--li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li-->
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
						<?php if($row['scd_match_bet'] == 1 || $row['scd_match_bet'] == 2){?>
						<li><?php if($row['scd_match_bet'] == 1){ echo "구장비 내기";  }else{ echo "음료수 내기";  }?></li>
						<?php }?>
				</ul>
				<?php if($row['scd_res_cert'] == 0){?>
				<p class="url_state">예약 대기</p>
				<?php }else if($row['scd_res_cert'] == 1){?>
				<p class="url_state ver3">예약 승인</p>
				<?php }else if($row['scd_res_cert'] == 2){?>
				<p class="url_state ver2">예약 거절</p>
				<?php }else if($row['scd_res_cert'] == 3){?>
				<p class="url_state ver2">예약 취소</p>
				<?php } ?>
			</a>
		</li>
		<?php }?>
		<!--
		<li class="url_li">
			<a href="">
				<p class="url_type">자체</p>
				<h3 class="url_name">필웨이 풋살장</h3>
				<p class="url_info1">
					<span>2023. 11. 06 (수)</span>
					<span>10:00 ~ 12:00</span>
				</p>
				<ul class="ust_sub_info">				
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>[<?php echo $row['mb_fs_zip']?>] <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?></span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
						<span><?php echo $row['mb_fs_tel']?></span>
					</li>
				</ul>
				<ul class="url_info2">
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
				<p class="url_state ver2">예약 거절</p>
			</a>
		</li>
		<li class="url_li">
			<a href="">
				<p class="url_type">자체</p>
				<h3 class="url_name">필웨이 풋살장</h3>
				<p class="url_info1">
					<span>2023. 11. 06 (수)</span>
					<span>10:00 ~ 12:00</span>
				</p>
				<ul class="ust_sub_info">				
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>[<?php echo $row['mb_fs_zip']?>] <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?></span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
						<span><?php echo $row['mb_fs_tel']?></span>
					</li>
				</ul>
				<ul class="url_info2">
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
				<p class="url_state">승인 대기</p>
			</a>
		</li>
		-->
		<?php if($total_cnt < 1){?>
		<li class="not_data">예약내역이 없습니다.</li>
		<?php }?>
	</ul>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>