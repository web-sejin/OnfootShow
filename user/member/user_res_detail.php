<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select * from a_schedule_res where scd_idx = '{$idx}' ";
	$row = sql_fetch($sql);

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

	//$lastCancelDate = $row['scd_date']." ".sprintf('%02d', $row['scd_start']).":00:00";
	//$lastCancelDate = date("Y-m-d H:i:s", strtotime($lastCancelDate." -3 days"));

	$lastCancelDate = $row['scd_date'];
	$lastCancelDate = date("Y-m-d", strtotime($lastCancelDate." -3 days"));
?>

<div class="user_res_dt">
	<div class="urd_box urd_box1">
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
			<p class="url_state ver2">예약 거절</p>
			<?php } ?>
			<p class="urd_alert">* 예약 취소는 매치 3일 전까지만 가능합니다.</p>
	</div>

	<div class="urd_box urd_box2">
		<p class="urd_box2_tit">취소/환불 규정</p>
		<p class="urd_box2_cont"><?php echo nl2br($row2['mb_fs_refund'])?></p>
	</div>
</div>

<?php if($row['scd_res_cert'] == 0 || $row['scd_res_cert'] == 1){?>
<div class="fix_btn_back"></div>
<div class="fix_btn_box">
	<?php if(G5_TIME_YMD <= $lastCancelDate){?>
	<button type="button" class="fix_btn ver3" onClick="cmPopOn('leave_pop');">예약취소</button>
	<?php }else{?>
	<button type="button" class="fix_btn ver4" onClick="noMore();">예약취소</button>
	<?php }?>
</div>
<?php }?>

<div id="leave_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc" id="content">예약을 취소하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="resCancel();">확인</button>
		</div>
	</div>
</div>

<script>
function resCancel(){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/res_cert_process.php",
		data: {idx:"<?php echo $idx?>", state:"2"}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {			
			cmPopOff('leave_pop');			
			$(".fix_btn_back").remove();
			$(".fix_btn_box").remove();
			$(".url_state").addClass("ver2");
			$(".url_state").text("예약 거절");
			showToast("예약이 취소되었습니다.");
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>