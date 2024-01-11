<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	$today = G5_TIME_YMD;
	$ago7 = date("Y-m-d", strtotime($today." -7 days"));
	$after7 = date("Y-m-d", strtotime($today." +7 days"));

	$sql = " SELECT COUNT(*) cnt
					FROM a_match_req A, a_match B, a_schedule_res C
					WHERE 1
						AND A.am_idx = B.am_idx
						AND B.scd_idx = C.scd_idx
						AND B.mb_idx = '{$member['mb_no']}'
						AND C.delete_state = 1
						AND LEFT(A.amr_datetime, 10) >= '{$ago7}'
						AND LEFT(A.amr_datetime, 10) <= '{$after7}'
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	$sql = " SELECT A.amr_idx, A.am_idx, A.amr_st, A.amr_datetime, B.mb_idx, A.at_idx, B.am_to, B.am_age, B.am_gender, B.am_bet, C.scd_idx, C.scd_date, C.scd_start, C.scd_end, C.as_idx, C.scd_team_name
					FROM a_match_req A, a_match B, a_schedule_res C
					WHERE 1
						AND A.am_idx = B.am_idx
						AND B.scd_idx = C.scd_idx
						AND B.mb_idx = '{$member['mb_no']}'
						AND C.delete_state = 1
						AND LEFT(A.amr_datetime, 10) >= '{$ago7}'
						AND LEFT(A.amr_datetime, 10) <= '{$after7}'
					ORDER BY A.amr_datetime DESC
					limit 0, 10
					";
	$result = sql_query($sql);
?>

<div class="reserve_list_area">
	<div class="people_sch_box">
		<input type="text" id="sch_val" placeholder="팀명 또는 예약자 이름 검색">
		<button type="button" onClick="getList();">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
		</button>
	</div>

	<ul class="list_tab">
		<li>
			<input type="radio" name="list_state" id="list_1" value="all" checked onChange="getList();">
			<label for="list_1"><span>매치 전체</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_2" value="2" onChange="getList();">
			<label for="list_2"><span>매치 거절</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_3" value="1" onChange="getList();">
			<label for="list_3"><span>매치 승인</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_4" value="0" onChange="getList();">
			<label for="list_4"><span>승인 대기</span></label>
		</li>
	</ul>

	<div class="list_date_box">
		<input type="text" name="date1" id="date1" value="<?php echo date("Y. m. d", strtotime($ago7));?>" readonly>
		<span>~</span>
		<input type="text" name="date2" id="date2" value="<?php echo date("Y. m. d", strtotime($after7));?>" readonly>
	</div>

	<ul class="cm_list_ul1" id="list">
		<?php 
			for($i=0; $row=sql_fetch_array($result); $i++){
				$stateClass = "";
				$stateText = "승인 대기";
				if ($row['amr_st'] == 1) {
					$stateClass = "list_blue";
					$stateText = "예약 승인";
				}else if ($row['amr_st'] == 2) {
					$stateClass = "list_red";
					$stateText = "매치 거절";
				}

				$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
				$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
				$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
				$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));

				$sql2 = " select mb_idx, at_idx from a_match_req where amr_idx =  '{$row['amr_idx']}' ";
				$row2 = sql_fetch($sql2);
				
				$vs_team = "-";
				if($row['amr_st'] == 1){
					$vs_team = getTeamName($row['at_idx']);
				}
		?>
		<li>
			<p class="list_state">
				<span class="list_date"><?php echo date("Y. m. d H:i", strtotime($row['amr_datetime']));?></span>
				<strong class="<?php echo $stateClass?>"><span><?php echo $stateText?></span></strong>
			</p>
			<div class="list_info">
				<div class="list_match_info">
					<p class="list_match_type">매치</p>
					<p class="list_match_date"><?php echo date("Y. m. d", strtotime($row['scd_date']))?></p>
					<p class="list_match_time"><?php echo sprintf('%02d', $row['scd_start'])?>:00 ~ <?php echo sprintf('%02d', $row['scd_end'])?>:00</p>
					<p class="list_match_team">						
						<?php echo $row['scd_team_name']?> &nbsp;vs&nbsp; <?php echo $vs_team?>
					</p>
					<p class="list_match_desc">
						<span><?php echo getStadiumName($row['as_idx']);?></span>
						<span><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></span>
						<span><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></span>
						<?php if($row['am_bet'] == 1){ echo "<span>구장비 내기</span>"; }else if($row['am_bet'] == 2){ echo "<span>음료수 내기</span>"; }?>
					</p>
				</div>
				<p class="list_info_p">
					<span>매치 신청자</span>
					<strong><?php echo getMemberinfo($row2['mb_idx'], 'mb_name');?></strong>
				</p>
				<p class="list_info_p">
					<span>전화번호</span>
					<strong><?php echo getMemberinfo($row2['mb_idx'], 'mb_hp');?></strong>
				</p>
				<p class="list_info_p">
					<span>팀 명</span>
					<strong><?php echo getTeamName($row2['at_idx'])?></strong>
				</p>
			</div>
			
			<div class="list_btn_box">
				<?php if ($row['amr_st'] == 1) { ?>
					<button type="button" class="list_btn ver2 ver3" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '2', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '3');">매치 취소</button>
				<?php }else if ($row['amr_st'] == 2) { ?>
					<button type="button" class="list_btn" disabled>매치 거절</button>
					<button type="button" class="list_btn" disabled>매치 승인</button>
				<?php }else{ ?>
					<button type="button" class="list_btn ver3" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '2', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '2');">매치 거절</button>
					<button type="button" class="list_btn ver4" onClick="matchCancel('<?php echo $row['scd_idx']?>', '<?php echo $row['am_idx']?>', '<?php echo $row['amr_idx']?>', '1', '<?php echo $row['mb_idx']?>', '<?php echo $row2['mb_idx']?>', '<?php echo $row['at_idx']?>', '1');">매치 승인</button>
				<?php }?>								
			</div>
		</li>
		<?php }?>

		<?php if($total_count < 1){?>
		<li class="not_data">매치 신청 내역이 없습니다.</li>
		<?php }?>
	</ul>

	<input type="hidden" id="limit_num" value="10" style="position:fixed;left:0;top:0;">
</div>

<div id="leave_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc" id="content">
			매치를 승인하시겠습니까?<br>
			같은 매치에 신청한 목록은 모두 취소됩니다.
		</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="cert_btn" onClick="resCancel();">확인</button>
		</div>
	</div>
</div>

<div id="cancel_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<p class="cm_pop_desc" id="content">매치를 <span id="state_txt"></span>하시겠습니까?</p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('cancel_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" id="cancel_btn">확인</button>
		</div>
	</div>
</div>

<script>
$(function(){
	$("#date1").datepicker({ 
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-100:c+100', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy-mm-dd", // 텍스트 필드에 입력되는 날짜 형식. 
		showAnim: "fade", //애니메이션을 적용한다. 
		showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
		monthNames : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'], 
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		beforeShow:function(){
			$(".datepicker_back").fadeIn();
		},
		onSelect: function (date) {
			var endDate = $('#date2');
			var startDate = $(this).datepicker('getDate');
			var minDate = $(this).datepicker('getDate');
			endDate.datepicker('setDate', minDate);
			startDate.setDate(startDate.getDate() + 36500);
			endDate.datepicker('option', 'maxDate', startDate);
			endDate.datepicker('option', 'minDate', minDate);
			$(".datepicker_back").fadeOut();
			getList();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},	
	});
	$('#date2').datepicker({
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
		changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
		yearRange: 'c-5:c+5', 
		showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
		currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
		closeText: '닫기', // 닫기 버튼 패널 
		dateFormat: "yy-mm-dd", // 날짜의 형식
		showAnim: "fade", //애니메이션을 적용한다. 
		showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
		monthNames : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'], 
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		beforeShow:function(){
			$(".datepicker_back").fadeIn();
		},
		onSelect:function(dateText, inst){
			$(".datepicker_back").fadeOut();
			getList();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});
});

$(window).scroll(function() {
	const scrollTop = $(window).scrollTop();
	const innerHeight = $(window).height();
	const scrollHeight = $(document).height();
	const lastHeight = ($("#list li:last-child").height());

	if (scrollTop + innerHeight >= scrollHeight-lastHeight) {
		//console.log("bottom!!!");
		getList('limit');			
	}
});

function getList(v){
	const dataVal = $("#sch_val").val();
	const dataType = $("input[name=list_state]:checked").val();
	const dataStart = $("#date1").val();
	const dataEnd = $("#date2").val();
	let limitVal = 0;
	if(v && v == "limit"){
		limitVal = $("#limit_num").val();
	}

	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/my_match_res_list.php",
		data: {dataVal:dataVal, dataType:dataType, dataStart:dataStart, dataEnd:dataEnd, limitVal:limitVal}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);			
			if(v && v == "limit"){
				$("#list").append(data);
				if((limitVal*1) <= "<?php echo $total_count?>"){
					$("#limit_num").val((limitVal*1)+10);
				}
			}else{
				$("#list").empty().append(data);
				$("#limit_num").val("10");
			}
		}
	});
}

function adminMatch(scd_idx, am_idx, amr_idx, state, mb_idx, mb_idx2, at_idx, type){
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/adminMatch.php",
		data: {scd_idx:scd_idx, am_idx:am_idx, amr_idx:amr_idx, state:state, mb_idx:mb_idx, mb_idx2:mb_idx2, at_idx:at_idx}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			if(type == "1"){
				cmPopOff('leave_pop');
			}else{
				cmPopOff('cancel_pop');
			}			
			location.reload();
		}
	});
}

function matchCancel(scd_idx, am_idx, amr_idx, state, mb_idx, mb_idx2, at_idx, type){
	if(state == "1"){
		$("#cert_btn").attr("onClick", `adminMatch(${scd_idx}, ${am_idx}, ${amr_idx}, ${state}, ${mb_idx}, ${mb_idx2}, ${at_idx}, ${type})`);
		cmPopOn('leave_pop');
	}else{
		if(type == "2"){
			$("#state_txt").text("거절");
		}else{
			$("#state_txt").text("취소");
		}
		$("#cancel_btn").attr("onClick", `adminMatch(${scd_idx}, ${am_idx}, ${amr_idx}, ${state}, ${mb_idx}, ${mb_idx2}, ${at_idx}, ${type})`);
		cmPopOn('cancel_pop');
	}
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>