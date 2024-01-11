<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	$today = G5_TIME_YMD;
	$ago7 = date("Y-m-d", strtotime($today." -7 days"));
	$after7 = date("Y-m-d", strtotime($today." +7 days"));

	$sql = " SELECT COUNT(*) cnt
					FROM a_schedule_res A, g5_member B, a_stadium C
					WHERE 1
						AND A.as_idx = C.as_idx
						AND B.mb_no = C.mb_idx
						AND A.scd_res_type = 2
						AND A.delete_state = 1
						AND B.mb_no = '{$member['mb_no']}'
						AND A.scd_date >= '{$ago7}'
						AND A.scd_date <= '{$after7}'
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];
	
	$sql = " SELECT A.*, C.mb_idx AS mb_no, C.as_name
					FROM a_schedule_res A, g5_member B, a_stadium C
					WHERE 1
						AND A.as_idx = C.as_idx
						AND B.mb_no = C.mb_idx
						AND A.scd_res_type = 2
						AND A.delete_state = 1
						AND B.mb_no = '{$member['mb_no']}'
						AND A.scd_date >= '{$ago7}'
						AND A.scd_date <= '{$after7}'
					ORDER BY scd_datetime DESC
					limit 0, 10
					";
	$result = sql_query($sql);
?>

<div class="reserve_list_area">
	<div class="people_sch_box">
		<input type="text" id="s_val" placeholder="팀명 또는 예약자 이름 검색">
		<button type="button" onClick="getList();">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
		</button>
	</div>

	<ul class="list_tab">
		<li>
			<input type="radio" name="list_state" id="list_1" value="all" checked onChange="getList();">
			<label for="list_1"><span>예약 전체</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_2" value="2" onChange="getList();">
			<label for="list_2"><span>승인 거절</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_3" value="1" onChange="getList();">
			<label for="list_3"><span>예약 승인</span></label>
		</li>
		<li>
			<input type="radio" name="list_state" id="list_4" value="0" onChange="getList();">
			<label for="list_4"><span>예약 대기</span></label>
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
				if ($row['scd_res_cert'] == 1) {
					$stateClass = "list_blue";
					$stateText = "예약 승인";
				}else if ($row['scd_res_cert'] == 2) {
					$stateClass = "list_red";
					$stateText = "승인 거절";
				}else if ($row['scd_res_cert'] == 3) {
					$stateClass = "list_red";
					$stateText = "승인 거절";
				}

				$ageKey = array_search($row['scd_match_age'], array_column($_cfg['match']['age'], 'val'));
				$ageVal = $_cfg['match']['age'][$ageKey]['txt'];
		?>
		<li id="list_<?php echo $row['scd_idx']?>">
			<p class="list_state">
				<span class="list_date"><?php echo date("Y. m. d H:i", strtotime($row['scd_datetime']));?></span>
				<strong class="<?php echo $stateClass?>"><span><?php echo $stateText?></span></strong>
			</p>
			<div class="list_info">
				<p class="list_info_p">
					<span>예약 날짜</span>
					<strong><?php echo date("Y. m. d", strtotime($row['scd_date']))?></strong>
				</p>
				<p class="list_info_p">
					<span>예약 시간</span>
					<strong><?php echo sprintf('%02d', $row['scd_start'])?>:00 ~ <?php echo sprintf('%02d', $row['scd_end']+1)?>:00</strong>
				</p>
				<p class="list_info_p">
					<span>예약 구장</span>
					<strong><?php echo $row['as_name']?></strong>
				</p>
				<p class="list_info_p">
					<span>경기 종류</span>
					<strong><?php if($row['scd_match_type'] == 1){ echo "자체";  }else{ echo "매치";  }?></strong>
				</p>
				<p class="list_info_p">
					<span>예약자 명</span>
					<strong><?php echo $row['scd_name']?></strong>
				</p>
				<p class="list_info_p">
					<span>전화번호</span>
					<strong><?php echo $row['scd_hp']?></strong>
				</p>
				<p class="list_info_p">
					<span>팀 명</span>
					<strong><?php if($row['scd_team_name']){ echo $row['scd_team_name']; }else{ echo "-";  }?></strong>
				</p>
				<p class="list_info_p">
					<span>연령대</span>
					<strong><?php echo $ageVal?></strong>
				</p>
			</div>
			<div class="list_btn_box">
				<?php if ($row['scd_res_cert'] == 0) {?>
					<button type="button" class="list_btn ver3" onClick="fnRes('<?php echo $row['scd_idx']?>', '2')">예약취소</button>
					<button type="button" class="list_btn ver4" onClick="fnRes('<?php echo $row['scd_idx']?>', '1')">예약승인</button>
				<?php }else if ($row['scd_res_cert'] == 1) {?>
					<button type="button" class="list_btn ver2 ver3" onClick="fnRes('<?php echo $row['scd_idx']?>', '2')">예약취소</button>
				<?php }else if ($row['scd_res_cert'] == 2) {?>
					<button type="button" class="list_btn" disabled>예약취소</button>
					<button type="button" class="list_btn" disabled>예약승인</button>
				<?php }?>
			</div>
		</li>
		<?php }?>
		<?php if($total_count < 1){?>
		<li class="not_data">예약내역이 없습니다.</li>
		<?php }?>
	</ul>

	<input type="hidden" id="limit_num" value="10" style="position:fixed;left:0;top:0;">
</div>

<div id="leave_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="cm_pop_alert">
		<input type="hidden" id="ipt1">
		<input type="hidden" id="ipt2">
		<p class="cm_pop_desc" id="content"></p>
		<div class="cm_pop_btn_box">
			<button type="button" class="cm_pop_btn ver2 ver4" onClick="cmPopOff('leave_pop');">취소</button>
			<button type="button" class="cm_pop_btn ver2 ver3" onClick="fnResOk();">확인</button>
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
		dateFormat: "yy. mm. dd", // 텍스트 필드에 입력되는 날짜 형식. 
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
		dateFormat: "yy. mm. dd", // 날짜의 형식
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
	const dataVal = $("#s_val").val();
	const dataType = $("input[name=list_state]:checked").val();
	const dataStart = $("#date1").val();
	const dataEnd = $("#date2").val();
	let limitVal = 0;
	if(v && v == "limit"){
		limitVal = $("#limit_num").val();
	}

	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/my_fs_res_list.php",
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

function fnRes(idx, state){
	let content = "";
	if(state == "2"){
		content = "예약을 취소하시겠습니까?";
	}else if(state == "1"){
		content = "예약을 승인하시겠습니까?";
	}

	$("#ipt1").val(idx);
	$("#ipt2").val(state);
	$("#content").text(content);
	cmPopOn('leave_pop');
}

function fnResOk(){
	const ipt1 = $("#ipt1").val();
	const ipt2 = $("#ipt2").val();

	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/res_cert_process.php",
		data: {idx:ipt1, state:ipt2}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			console.log(data);			
			if($("input[name=list_state]:checked").val() != "all"){
				$(`#list_${ipt1}`).remove();
				if($("#list li").length < 1){
					$("#list").html('<li class="not_data">예약내역이 없습니다.</li>');
				}
			}else{
				if(ipt2 == "1"){
					$(`#list_${ipt1} .list_state strong`).addClass("list_blue");
					$(`#list_${ipt1} .list_state strong span`).text("예약 승인");
					$(`#list_${ipt1} .list_btn_box`).html(`<button type="button" class="list_btn ver2 ver3" onClick="fnRes('${ipt1}', '2')">예약취소</button>`);
				}else{
					$(`#list_${ipt1} .list_state strong`).addClass("list_red");
					$(`#list_${ipt1} .list_state strong span`).text("승인 거절");
					$(`#list_${ipt1} .list_btn_box`).html(`<button type="button" class="list_btn" disabled>예약취소</button>	<button type="button" class="list_btn" disabled>예약승인</button>`);
				}
			}

			cmPopOff('leave_pop');			
			showToast("예약이 변경되었습니다.");
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>