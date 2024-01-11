<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select count(*) cnt from a_match where am_idx = '{$idx}' and scd_idx IS NOT NULL ";
	$row = sql_fetch($sql);
	if($row['cnt'] > 0){
		goto_url(G5_URL."/user/match_view.php?idx=".$idx);
	}

	$sql = " select * from a_match where am_idx = '{$idx}' ";
	$row = sql_fetch($sql);

	$sidoName = "";
	$sigugunName = "";
	$dongName = "";
	
	$sql = " select * from rb_sido where sd_idx = '{$row['sd_idx']}' ";
	$sido = sql_fetch($sql);
	$sidoName = $sido['sd_name'];

	$sql = " select * from rb_sigungu where si_idx = '{$row['si_idx']}' ";
	$sigugun = sql_fetch($sql);
	$sigugunName = $sigugun['si_name'];

	if($row['do_idx'] == "all"){
		$dongName = "전체";
	}else{
		$sql = " select * from rb_dongli where do_idx = '{$row['do_idx']}' ";
		$dong = sql_fetch($sql);
		$dongName = $dong['do_name'];
	}

	$localName = $sidoName." ".$sigugunName." ".$dongName;

	$sizeVal = "";
	if($row['pop_size']){
		$sizeExp = explode("|", $row['pop_size']);
		for($u=0; $u<count($sizeExp); $u++){			
			$toKey = array_search($sizeExp[$u], array_column($_cfg['stadium']['to'], 'val'));
			if($u != 0){ $sizeVal .= ", "; }
			$sizeVal .= $_cfg['stadium']['to'][$toKey]['txt'];
		}
	}
	
	$floorVal = "";
	if($row['pop_sort']){
		$sortExp = explode("|", $row['pop_sort']);
		for($u=0; $u<count($sortExp); $u++){			
			$floorKey = array_search($sortExp[$u], array_column($_cfg['stadium']['floor'], 'val'));
			if($u != 0){ $floorVal .= ", "; }
			$floorVal .= $_cfg['stadium']['floor'][$floorKey]['txt'];
		}
	}
	
	$useVal = "";	
	if($row['pop_use1']){
		$useExp = explode("|", $row['pop_use1']);
		for($u=0; $u<count($useExp); $u++){			
			$sql_use = " select afu_type_tit, afu_subject from a_futsal_use where afu_idx = '{$useExp[$u]}' ";
			$use = sql_fetch($sql_use);
			if($useVal != ""){ $useVal .= ", "; }
			$useVal .= $use['afu_type_tit']."(".$use['afu_subject'].")";
		}
	}
	if($row['pop_use2']){
		$useExp = explode("|", $row['pop_use2']);
		for($u=0; $u<count($useExp); $u++){			
			$sql_use = " select afu_type_tit, afu_subject from a_futsal_use where afu_idx = '{$useExp[$u]}' ";
			$use = sql_fetch($sql_use);
			if($useVal != ""){ $useVal .= ", "; }
			$useVal .= $use['afu_type_tit']."(".$use['afu_subject'].")";
		}
	}
	if($row['pop_use3']){
		$useExp = explode("|", $row['pop_use3']);
		for($u=0; $u<count($useExp); $u++){			
			$sql_use = " select afu_type_tit, afu_subject from a_futsal_use where afu_idx = '{$useExp[$u]}' ";
			$use = sql_fetch($sql_use);
			if($useVal != ""){ $useVal .= ", "; }
			$useVal .= $use['afu_type_tit']."(".$use['afu_subject'].")";
		}
	}	
?>

<div class="match_condition_list cm_padd6">
	<div class="condi_wrap <?php if($row['am_area'] == 2){ echo "ver2"; }?>">
		<ul class="match_condition">
			<li>
				<p class="condi_th">지역</p>
				<div class="condi_td"><?php echo $localName?></div>
			</li>
			<li>
				<p class="condi_th">시간</p>
				<div class="condi_td"><?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>) <?php echo sprintf('%02d', $row['am_time'])?>:00</div>
			</li>
			<li>
				<p class="condi_th">구장 크기</p>
				<div class="condi_td"><?php echo $sizeVal;?></div>
			</li>
			<li>
				<p class="condi_th">구장 종류</p>
				<div class="condi_td"><?php echo $floorVal;?></div>
			</li>
			<li>
				<p class="condi_th">편의시설</p>
				<div class="condi_td"><?php echo $useVal?></div>
			</li>
		</ul>
		<?php if($row['am_area'] == 1){?>
		<button type="button" class="fix_btn fix_modi" onClick="searchCondition();">검색</button>
		<?php }?>
	</div>

	<?php if($row['am_area'] == 1){?>
	<ul class="pop_sch_stadium">
		<li class="not_data">검색을 클릭해 주세요.</li>
	</ul>
	<?php }else{?>
	<div class="fix_stadium_box">
		<p class="mv_title">지정한 구장</p>
		<ul class="pop_sch_stadium"></ul>
		<div class="fix_stadium_btn">
			<button type="button" class="fix_btn fix_modi" onClick="searchCondition('all');">다른 구장 검색</button>
		</div>
	</div>
	<?php }?>
</div>

<input type="hidden" id="picked_idx">
<div class="fix_btn_back"></div>
<div class="fix_btn_box">
	<button type="button" class="fix_btn" id="submit_button" onClick="stadiumRegister();">예약하기</button>
</div>

<script>
	$(function(){
		if("<?php echo $row['am_area'] ?>" == 2){
			searchCondition();
		}
	});

	function searchCondition(v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/searchCondition.php",
			data: {am_idx:"<?php echo $idx?>", v:v}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(v == "all"){
					$(".not_data").remove();
					$(".pop_sch_stadium").append(data);
				}else{
					$(".pop_sch_stadium").empty().append(data);
				}
			}
		});
	}	

	function pick(v){
		if($("#picked_idx").val() == v){
			$("#picked_idx").val('');
			$(`.condi_li`).removeClass("on");
			$("#submit_button").removeClass("on");
		}else{
			$("#picked_idx").val(v);
			$(`#condi_li_${v}`).addClass("on").siblings().removeClass("on");
			$("#submit_button").addClass("on");
		}
	}

	function stadiumRegister(){		
		if($("#picked_idx").val() == ""){
			showToast("풋살장을 선택해 주세요.");
		}else{
			const idx = $("#picked_idx").val();
			const am_idx = "<?php echo $idx?>";
			const amr_idx = "<?php echo $amr_idx?>";

			location.href = `<?php echo G5_URL?>/user/stadium_res2.php?idx=${idx}&am_idx=${am_idx}&amr_idx=${amr_idx}`;
		}
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>