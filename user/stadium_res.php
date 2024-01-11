<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	$showDate = date("Y. m. d", strtotime($date))." (".getYoil($date).")";

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

<form name="res_frm" method="post">
	<input type="hidden" name="mb_idx" id="mb_idx" value="<?php echo $idx?>">
	<input type="hidden" name="date" id="date" value="<?php echo $date?>">
	<input type="hidden" id="next_ipt">
	<input type="hidden" name="time_pick_ipt" id="time_pick_ipt">
	<div class="stadium_res">
		<div class="str_box">
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
		</div>

		<div class="str_box">
			<p class="str_tit">날짜</p>
			<input type="text" id="show_date" class="regi_ipt" readonly value="<?php echo $showDate?>">
		</div>

		<div class="str_box ver2">
			<p class="str_tit">구장 선택</p>
			<ul class="str_sub_list">
				<?php
					$result_stadium = sql_query($sql_stadium);
					for($j=0; $stadium=sql_fetch_array($result_stadium); $j++){
				?>
				<li>
					<input type="radio" name="as_idx" id="as_idx_<?php echo $j?>" value="<?php echo $stadium['as_idx']?>" <?php if($j == 0){ echo "checked"; }?> onChange="getTimeList();">
					<label for="as_idx_<?php echo $j?>"><?php echo $stadium['as_name']?></label>
				</li>
				<?php }?>
			</ul>
		</div>
		
		<?php
			$curr_date = $date;
			$curr_stadium = getFirstStadium($row['mb_no']);
			$sql3 = " select * from a_stadium where as_idx = '{$curr_stadium}' ";
			$row3 = sql_fetch($sql3);
			//$curr_price = getStadiumPrice($curr_stadium);
			$curr_price = $row3['as_price'];
			$curr_size = $row3['as_size'];

			$ary1 = array();
			$exp = explode("|", $row3['as_to']);
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

			$curr_sort = array_search($row3['as_sort'], array_column($_cfg['stadium']['sort'], 'val'));
			$curr_floor = array_search($row3['as_floor'], array_column($_cfg['stadium']['floor'], 'val'));
		?>
		<div class="str_box ver2" id="str_box">
			<ul class="str_info_ul">
				<li><?php echo number_format($curr_price)?>/시간</li>
				<li><?php echo $curr_size?>m (<?php echo $to?>) <?php echo $_cfg['stadium']['sort'][$sortKey]['txt'];?> <?php echo $_cfg['stadium']['floor'][$curr_floor]['txt'];?></li>
			</ul>
			<p class="str_info_desc">* 최소 연속 2시간 부터 예약 가능합니다.</p>
			<ul class="str_time_ul">
				<?php					
					for($i=$row['mb_fs_start']; $i<$row['mb_fs_end']; $i++){
						$sql2 = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx
										from a_schedule_res A, a_schedule_res_time B
										where 1
											and A.scd_idx = B.scd_idx
											and A.as_idx = '{$curr_stadium}' 
											and A.scd_date = '{$curr_date}' 											
											and A.delete_state = 1 
											and B.scdt_time = {$i}
											and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
										";
						$row2 = sql_fetch($sql2);

						$use_state = true;						
						$hour = $i;

						if($row2['scd_start']){
							$hour = $row2['scd_start'];
						}
						$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
						if(G5_TIME_YMDHIS >= $this_date){
							$use_state = false;
						}

						if($row2['cnt'] < 1 && $use_state){
				?>
				<li id="scd_li_<?php echo $i?>" class="str_time_li">
					<input type="checkbox" name="time_chk[]" id="tc_<?php echo $i?>" value="<?php echo $i?>" onChange="sideChk('<?php echo $i?>');">
					<label for="tc_<?php echo $i?>">
						<span><?php echo sprintf('%02d', $i);?>:00 ~ <?php echo sprintf('%02d', ($i+1));?>:00</span>
						<strong><?php echo number_format($curr_price)?>원</strong>
					</label>
				</li>
				<?php }}?>
			</ul>
		</div>

		<div class="str_box">
			<p class="str_tit">취소/환불 규정</p>
			<div class="refund_desc"><?php echo nl2br($row['mb_fs_refund'])?></div>
		</div>
	</div>

	<div class="fix_btn_back"></div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn" id="submit_button" disabled onClick="insertGame();">선택</a>
	</div>
</form>

<script>
	$(function(){
		//getTimeList();	
	});

	$('#show_date').datepicker({
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-20:c+20', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy-mm-dd", // 날짜의 형식
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
			$("#date").val(dateText);
			showDate(dateText);
			getTimeList();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});

	function showDate(dateText){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getYoil.php",
			data: {dateText:dateText}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#show_date").val(data);
			}
		});
	}

	function getTimeList(dateText){		
		const getDate = $("#date").val();
		const getStadium = $("input[name=as_idx]:checked").val();
		//console.log(getDate+"//"+getStadium);

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getTimeEtcStadiumList.php",
			data: {getDate:getDate, getStadium:getStadium, mb_idx:$("#mb_idx").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				console.log(data);
				$("#str_box").empty().append(data);
				$("#next_ipt").val("");
				$("#time_pick_ipt").val("");
			}
		});
	}

	function sideChk(time_val){
		const timeVal = parseInt(time_val);
		const next_ipt = $("#next_ipt");
		const next_ipt_val = parseInt(next_ipt.val());
		const startTime = parseInt("<?php echo $member['mb_fs_start']?>");
		const endTime = parseInt("<?php echo $member['mb_fs_end']?>");		

		if(!next_ipt_val || timeVal == next_ipt_val){
			next_ipt.val(timeVal+1);
		}else{			
			const chked = $("input[id=tc_"+time_val+"]").is(':checked');
			if(chked){
				showToast("처음 선택한 시간을 기준으로 연속된 시간만 선택할 수 있습니다.");
				$("input[id=tc_"+time_val+"]").prop("checked", false);
				return false;
			}else{
				if(timeVal+1 == next_ipt_val){
					next_ipt.val(timeVal);
				}else{
					for(var i=parseInt(startTime); i<timeVal; i++){
						$("input[id=tc_"+i+"]").prop("checked", false);
					}
				}
			}			
		}
		
		const chkCnt = $("input[name='time_chk[]']:checked").length;
		if(chkCnt >= 2){
			$("#submit_button").addClass("on").attr("disabled", false);
		}else{
			$("#submit_button").removeClass("on").attr("disabled", true);
			if(chkCnt < 1){ next_ipt.val(''); }
		}
	}

	function insertGame(){
		let timeKey = [];
		$("input[name='time_chk[]']:checked").each(function(){
			timeKey.push($(this).val());
		});
		$("#time_pick_ipt").val(timeKey.join("|"));
		$("input[name='time_chk[]']").prop("checked", false);
		$("#next_ipt").val('');

		//$("form[name=res_frm]").submit();
		const v1 = $("#mb_idx").val();
		const v2 = $("#date").val();
		const v3 = $("input[name=as_idx]:checked").val();
		const v4 = $("#time_pick_ipt").val();

		location.href = `<?php echo G5_URL?>/user/stadium_res_step2.php?v1=${v1}&v2=${v2}&v3=${v3}&v4=${v4}`;
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>