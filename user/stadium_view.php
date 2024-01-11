<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

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

	$sql = " select * from g5_member where mb_no = '{$idx}' ";	
	
	if($myLat != "" && $myLat != "00" && $myLon != "" && $myLon != "0"){
		$sql = " SELECT *,
			(6371*acos(cos(radians({$myLat}))*cos(radians(mb_fs_lat))*cos(radians(mb_fs_lng)
			-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(mb_fs_lat)))) AS distance
			FROM g5_member
			WHERE mb_no = '{$idx}'
		";	
	}
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
<style>#sub_div {margin-top:0;}</style>

<div class="stadium_view">
	<button type="button" class="back_btn" onClick="goback();">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_back_fff.svg" alt="">
	</button>
	<div class="stv_slider">
		<?php
			$sql_sl = " select count(*) cnt from a_futsal_img where mb_idx = '{$idx}' ";
			$row_sl = sql_fetch($sql_sl);
			$sl_cnt = $row_sl['cnt'];			
		?>
		<div class="swiper-container stv_swiper">
			<ul class="swiper-wrapper">
				<?php if($sl_cnt < 1){?>
				<li class="swiper-slide"></li>
				<?php }else{?>
					<?php
						$sql_sl = " select * from a_futsal_img where mb_idx = '{$idx}' ";
						$result_sl = sql_query($sql_sl);
						for($i=0; $slider=sql_fetch_array($result_sl); $i++){
					?>
					<li class="swiper-slide"><img src="<?php echo G5_DATA_URL?>/footsalFile/<?php echo $slider['img_af']?>" alt=""></li>
					<?php }?>
				<?php }?>
			</ul>
		</div>
		<div class="stv_pagination"></div>
	</div>

	<div class="stv_info">
		<div class="stv_info_box stv_info1">
			<p class="ust_km">	
				<?php if($_SESSION['lat'] == "0"){?>
					위치권한없음
				<?php }else{?>
					<?php	echo round($row['distance'], 2); ?><span>km</span>						
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
					<span>[<?php echo $row['mb_fs_zip']?>] <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?> <?php echo $row['mb_fs_addr3']?></span>
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
		</div>
	</div>

	<div class="stv_info_box stv_info2">
		<p class="stv_info2_tit">풋살장 소개</p>
		<div class="stv_info2_cont"><?php echo nl2br($row['mb_fs_content'])?></div>
	</div>
</div>

<div class="fix_btn_back"></div>
<div class="fix_btn_box">
	<a href="<?php echo G5_URL?>/user/stadium_res.php?idx=<?php echo $idx?>&date=<?php echo $date?>" class="fix_btn on">시간 선택하기</a>
</div>

<script>
	const swiper = new Swiper('.stv_swiper', {
		speed: 500,
		followFinger:false,
		loop:true,
		autoHeight:true,
		/*autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},*/
		pagination: {
			el: ".stv_pagination",
	        type: "fraction",
		},
	});
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>