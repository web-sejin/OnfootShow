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
	
	$local = false;
	
	$sql_common = " FROM g5_member WHERE mb_type = 2 AND mb_leave_status = 1 AND mb_cert = 1 ";
	$sql_order = " ORDER BY mb_fs_name ASC, mb_datetime DESC ";

	$sql = " SELECT COUNT(*) cnt {$sql_common} {$add_query} ";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];	
	
	$sql = " SELECT * {$sql_common} {$add_query} {$sql_order} limit 0, 10 ";
	$result = sql_query($sql);

	if($myLat != "" && $myLat != "00" && $myLon != "" && $myLon != "0"){
		$local = true;
		$sql_common = " (6371*acos(cos(radians({$myLat}))*cos(radians(mb_fs_lat))*cos(radians(mb_fs_lng)-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(mb_fs_lat)))) AS distance
												FROM g5_member
												WHERE 1
													AND mb_type = 2
													AND mb_leave_status = 1
													AND mb_cert = 1
											";
		$sql_order = " ORDER BY mb_fs_name ASC, distance ASC ";

		$sql = " SELECT count(*) cnt,
						{$sql_common}
						{$add_query}
						";	
		$row = sql_fetch($sql);
		$total_count = $row['cnt'];

		$sql = " SELECT *,
						{$sql_common}
						{$add_query}
						{$sql_order}
						limit 0, 10
					";	
		$result = sql_query($sql);
	}
?>

<div class="user_filter_box">
	<div class="simple_filter_box">
		<input type="text" name="s_date" id="s_date" class="simple_ipt simple_date on" readonly placeholder="날짜 선택" value="<?php echo date("Y. m. d", strtotime(G5_TIME_YMD))?>">
		<select name="s_start" id="s_start" class="simple_ipt simple_time" onChange="selectValChk(this.value, '#s_start');">
			<option value="">시작 시간</option>
			<?php for($i=0; $i<25; $i++){?>
			<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
			<?php }?>
		</select>
		<select name="s_end" id="s_end" class="simple_ipt simple_time" onChange="selectValChk(this.value, '#s_end');">
			<option value="">종료 시간</option>
			<?php for($i=1; $i<26; $i++){?>
			<option value="<?php echo $i?>"><?php echo sprintf('%02d', $i)?>:00</option>
			<?php }?>
		</select>
	</div>
	<button type="button" class="dtail_filter_btn" onClick="cmPopOn('filter_pop');"></button>
	<div class="order_filter_box">
		<select name="s_od" id="s_od" onChange="filterSubmit('refresh');">
			<option value="name">이름순</option>
			<option value="distance">거리순</option>
			<option value="up">예약 많은순</option>
			<option value="down">금액 낮은순</option>
		</select>
	</div>
</div>

<div class="user_stadium_box">
	<ul class="user_stadium_ul">
		<?php 
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
			<!--a href="<?php echo G5_URL?>/user/stadium_view.php?idx=<?php echo $row['mb_no']?>"-->
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
				<ul class="ust_list">
					<?php
						$result_stadium = sql_query($sql_stadium);
						for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
							$ary1 = array();
							$exp = explode("|", $stadium['as_to']);
							for($x=0; $x<count($exp); $x++){
								array_push($ary1, $exp[$x]);	
							}
							$ary1 = array_values(array_unique($ary1));
							sort($ary1);

							$to = "";
							for($x=0; $x<count($ary1); $x++){
								if($x != 0){ $to .= ", ";  }
								if($ary1[$x] == "4"){
									$to .= $ary1[$x].":".$ary1[$x]."이하";	
								}else if($ary1[$x] == "7"){
									$to .= $ary1[$x].":".$ary1[$x]."이상";
								}else{
									$to .= $ary1[$x].":".$ary1[$x];
								}								
							}

							$sortKey = array_search($stadium['as_sort'], array_column($_cfg['stadium']['sort'], 'val'));
							$floorKey = array_search($stadium['as_floor'], array_column($_cfg['stadium']['floor'], 'val'));
					?>
					<li>
						<p>
							<?php echo $stadium['as_name']?> - <?php echo $stadium['as_size']?> (<?php echo $to?>) <?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?> <?php echo $_cfg['stadium']['floor'][$floorKey]['txt'];?>
						</p>
						<p><strong><?php echo number_format($stadium['as_price'])?>원</strong><span>/</span>시간</p>
					</li>
					<?php }?>
				</ul>
			</a>
		</li>
		<?php }?>
		<!--li class="user_stadium_li">
			<a href="">
				<p class="ust_km">3.4km</p>
				<h3 class="ust_name">굿웨이 풋살장</h3>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_call.svg" alt=""></strong>
						<span>032-123-4567</span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기도 부천시 상동 536-8 6층</span>
					</li>
				</ul>
				<ul class="ust_sub_info2">
					<li>5:5</li>
					<li>6:6</li>
					<li>실내</li>
					<li>실외</li>
					<li>화장실 (남녀공용)</li>
					<li>화장실 (남녀분리)</li>
					<li>주차장</li>
					<li>샤워실</li>
				</ul>
				<ul class="ust_list">
					<li>
						<p>1구장 - 40x20m (5:5) 실내 인조잔디</p>
						<p><strong>50,000원</strong><span>/</span>시간</p>
					</li>
					<li>
						<p>2구장 - 50x30m (6:6) 실내 인조잔디</p>
						<p><strong>55,000원</strong><span>/</span>시간</p>
					</li>
				</ul>
			</a>
		</li-->
	</ul>
	<input type="hidden" id="limit_num" value="10">
</div>

<div id="filter_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="filter_pop_cont">
		<p class="filter_pop_tit">검색 필터</p>		
		<input type="hidden" name="s_to_list" id="s_to_list">
		<input type="hidden" name="s_floor_list" id="s_floor_list">
		<input type="hidden" name="s_use1_list" id="s_use1_list">
		<input type="hidden" name="s_use2_list" id="s_use2_list">
		<input type="hidden" name="s_use3_list" id="s_use3_list">
		<button type="button" class="filter_pop_off" onClick="cmPopOff('filter_pop');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">지역</p>
				<div class="regi_td regi_td_flex">
					<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select ver5" onchange="chgSido(this.value);">
						<option value="">시/도</option>
						<?php
						$sqlS = " select * from rb_sido where 1 order by sd_idx asc ";
						$resultS = sql_query($sqlS);
						$sido_selected = "";
						for($i=0; $sido=sql_fetch_array($resultS); $i++){
						?>
						<option value="<?php echo $sido['sd_idx']?>"><?php echo $sido['sd_name']?></option>
						<?php }?>
					</select>
					<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select ver5">
						<option value="">시/구/군</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">구장 크기</p>
				<div class="regi_td">
					<ul class="st_frm_chk">
						<?php for($i=0; $i<count($_cfg['stadium']['to']); $i++){?>
						<li>							
							<input type="checkbox" name="st_to[]" id="st_to_<?php echo $i?>" value="<?php echo $_cfg['stadium']['to'][$i]['val']?>">
							<label for="st_to_<?php echo $i?>"><?php echo $_cfg['stadium']['to'][$i]['txt']?></label>
						</li>
						<?php }?>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">구장종류</p>
				<div class="regi_td">
					<ul class="st_frm_chk ver2">
						<?php for($i=0; $i<count($_cfg['stadium']['sort']); $i++){?>
						<li>							
							<input type="checkbox" name="st_sort[]" id="st_sort_<?php echo $i?>" value="<?php echo $_cfg['stadium']['sort'][$i]['val']?>">
							<label for="st_sort_<?php echo $i?>"><?php echo $_cfg['stadium']['sort'][$i]['txt']?></label>
						</li>
						<?php }?>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">편의시설</p>
				<div class="regi_td">
					<ul class="st_frm_chk ver2">
						<?php 
							$sql_use = " select * from a_futsal_use where afu_type = 1 and afu_subject != '없음' ";
							$result_use = sql_query($sql_use);
							for($i=0; $use=sql_fetch_array($result_use); $i++){
						?>
						<li>
							<input type="checkbox" name="mb_fs_use1[]" id="mb_fs_use1_<?php echo $i?>" value="<?php echo $use['afu_idx']?>">
							<label for="mb_fs_use1_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
						</li>
						<?php }?>
					</ul>
					<ul class="st_frm_chk ver2">
						<?php 
								$sql_use = " select * from a_futsal_use where afu_type = 2 and afu_subject != '없음' ";
								$result_use = sql_query($sql_use);
								for($i=0; $use=sql_fetch_array($result_use); $i++){
							?>
							<li>
								<input type="checkbox" name="mb_fs_use2[]" id="mb_fs_use2_<?php echo $i?>" value="<?php echo $use['afu_idx']?>">
								<label for="mb_fs_use2_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
							</li>
							<?php }?>
					</ul>
					<ul class="st_frm_chk ver2">
						<?php 
								$sql_use = " select * from a_futsal_use where afu_type = 3 and afu_subject != '없음' ";
								$result_use = sql_query($sql_use);
								for($i=0; $use=sql_fetch_array($result_use); $i++){
							?>
							<li>
								<input type="checkbox" name="mb_fs_use3[]" id="mb_fs_use3_<?php echo $i?>" value="<?php echo $use['afu_idx']?>">
								<label for="mb_fs_use3_<?php echo $i?>"><?php echo $use['afu_type_tit']?> (<?php echo $use['afu_subject']?>)</label>
							</li>
							<?php }?>
					</ul>
				</div>
			</li>
		</ul>
		<div class="fix_btn_box not_fix">
			<button type="button" class="fix_btn on" onClick="filterSubmit('refresh');">적용</button>
		</div>
	</div>
</div>

<script>
	$('.simple_date').datepicker({
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-20:c+20', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy. mm. dd", // 날짜의 형식
		showAnim: "fade", //애니메이션을 적용한다. 
		showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
		monthNames : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'], 
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		minDate: 0,
		beforeShow:function(){
			$(".datepicker_back").fadeIn();
		},
		onSelect:function(dateText, inst){
			$(".datepicker_back").fadeOut();
			inputTextChk();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});

	function chgSido(v){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/sigugun_list2.php",
			data: {sd_idx:v},
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#si_idx").empty().append(data);
			}
		});
	}

	function inputTextChk(){
		if($("#s_date").val() != ""){
			$("#s_date").addClass("on");
		}else{
			$("#s_date").removeClass("on");
		}
		filterSubmit('refresh');
	}

	function selectValChk(v, id){
		if(v != ""){
			$(id).addClass("on");
		}else{
			$(id).removeClass("on");
		}
		filterSubmit('refresh');
	}

	$(window).scroll(function() {
		const scrollTop = $(window).scrollTop();
		const innerHeight = $(window).height();
		const scrollHeight = $(document).height();
		const lastHeight = ($(".user_stadium_li:last-child").height());

		if (scrollTop + innerHeight >= scrollHeight-lastHeight) {
			//console.log("bottom!!!");
			filterSubmit('limit');			
		}
	});

	function filterSubmit(v){
		const s_date = $("#s_date").val();
		const s_start = $("#s_start").val();
		const s_end = $("#s_end").val();
		const s_od = $("#s_od").val();
		const sd_idx = $("#sd_idx").val();
		const si_idx = $("#si_idx").val();
		let limitVal = 0;
		if(v == "limit"){
			limitVal = $("#limit_num").val();
		}

		let sizeKey = [];	
		$("input[name='st_to[]']:checked").each(function(){ sizeKey.push($(this).val()); });
		$("#s_to_list").val(sizeKey.join("|"));
		
		let floorKey = [];
		$("input[name='st_sort[]']:checked").each(function(){ floorKey.push($(this).val()); });
		$("#s_floor_list").val(floorKey.join("|"));

		let use1Key = [];
		$("input[name='mb_fs_use1[]']:checked").each(function(){ use1Key.push($(this).val()); });
		$("#s_use1_list").val(use1Key.join("|"));

		let use2Key = [];
		$("input[name='mb_fs_use2[]']:checked").each(function(){ use2Key.push($(this).val()); });
		$("#s_use2_list").val(use2Key.join("|"));

		let use3Key = [];
		$("input[name='mb_fs_use3[]']:checked").each(function(){ use3Key.push($(this).val()); });
		$("#s_use3_list").val(use3Key.join("|"));
		
		if(sd_idx!="" || si_idx!="" || sizeKey.length > 0 || floorKey.length > 0 || use1Key.length > 0 || use2Key.length > 0 || use3Key.length > 0){
			$(".dtail_filter_btn").addClass("on");
		}else{
			$(".dtail_filter_btn").removeClass("on");
		}
		
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getUserStadiumList.php",
			data: {
				date: s_date,
				start: s_start,
				end: s_end,
				od: s_od,
				sd_idx: sd_idx,
				si_idx: si_idx,
				floorList: $("#s_floor_list").val(),
				toList: $("#s_to_list").val(),
				use1List: $("#s_use1_list").val(),
				use2List: $("#s_use2_list").val(),
				use3List: $("#s_use3_list").val(),
				limitVal: limitVal,
				subject: $("#sch_val").val(),
			}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(v == "limit"){
					$(".user_stadium_ul").append(data);
					if((limitVal*1) <= "<?php echo $total_count?>"){
						$("#limit_num").val((limitVal*1)+10);
					}
				}else{
					$(".user_stadium_ul").empty().append(data);
					$("#limit_num").val("10");
				}
				cmPopOff('filter_pop');
			}
		});
	}

	function moveView(idx){
		const selDate = $("#s_date").val().replaceAll(". ", "-");
		location.href = "<?php echo G5_URL?>/user/stadium_view.php?idx="+idx+"&date="+selDate;
	}

	function searchList(){
		if($("#sch_val").val() == ""){
			showToast("구장명을 입력해 주세요.");
			return false;
		}

		filterSubmit('refresh');
	}
</script>

<?php include_once(G5_PATH."/_tail.php");?>