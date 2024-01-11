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
?>
<div class="mainpage">
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
	</div>

	<ul class="url_ul">
		<!-- 지역 단위 -->
		<li class="url_li">
			<a href="">
				<div class="url_type_box">
					<p class="url_type ver2">최하</p>
					<p class="url_type ver2">텐션 1.5</p>
				</div>
				<h3 class="url_name">부천풋살</h3>
				<p class="url_info1">
					<span>2023. 11. 06 (수)</span>
					<span>10:00</span>
				</p>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 전체</span>
					</li>
				</ul>
				<ul class="url_info3">
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg" alt="">
						<span><b>4.5</b></span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
						<span>15</span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
						<span>1</span>
					</li>
				</ul>
				<ul class="url_info2 ver2">
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
			</a>
		</li>

		<!-- 구장 단위 -->
		<li class="url_li">
			<a href="">
				<div class="url_type_box">
					<p class="url_type ver2">하</p>
					<p class="url_type ver2">텐션 1.5</p>
				</div>
				<h3 class="url_name">인천생제르망FC</h3>
				<p class="url_info1">
					<span>2023. 11. 06 (수)</span>
					<span>10:00</span>
				</p>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 굿웨이 풋살장</span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 스토래지풋살 부천 소사역점</span>
					</li>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 웅진플레이 도시 풋살장</span>
					</li>
				</ul>
				<ul class="url_info3">
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg" alt="">
						<span><b>4.5</b></span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
						<span>15</span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
						<span>1</span>
					</li>
				</ul>
				<ul class="url_info2 ver2">
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
			</a>
		</li>

		<!-- 구장(매칭)예약 -->
		<li class="url_li">
			<a href="">
				<p class="url_res_st">예약</p>
				<div class="url_type_box">
					<p class="url_type ver2">하</p>
					<p class="url_type ver2">텐션 1.5</p>
				</div>
				<h3 class="url_name">팀바르셀로나FC</h3>
				<p class="url_info1">
					<span>2023. 11. 06 (수)</span>
					<span>14:00~16:00</span>
				</p>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 굿웨이 풋살장 (1구장)</span>
					</li>
				</ul>
				<ul class="url_info3">
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg" alt="">
						<span><b>4.5</b></span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
						<span>15</span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
						<span>1</span>
					</li>
				</ul>
				<ul class="url_info2 ver2">
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
			</a>
		</li>

		<!-- 구장관리자가 직접 매칭 등록 -->
		<li class="url_li">
			<a href="">
				<p class="url_res_st">예약</p>
				<div class="url_type_box">
					<p class="url_type ver2">상</p>
				</div>
				<h3 class="url_name">팀바르셀로나FC</h3>
				<p class="url_info1">
					<span>2023. 11. 04 (수)</span>
					<span>14:00~16:00</span>
				</p>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span>경기 부천시 굿웨이 풋살장 (1구장)</span>
					</li>
				</ul>
				<ul class="url_info3">
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
						<span>15</span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
						<span>1</span>
					</li>
				</ul>
				<ul class="url_info2 ver2">
					<li class="adm_mode">관리자 등록</li>
					<li>5:5</li>
					<li>20~25</li>
					<li>혼성</li>
					<li>음료수 내기</li>
				</ul>
			</a>
		</li>
	</ul>
</div>

<a href="" class="match_write_btn"><img src="<?php echo G5_THEME_IMG_URL?>/ic_write_plus.svg" alt=""></a>

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
		//filterSubmit('refresh');
	}

	function selectValChk(v, id){
		if(v != ""){
			$(id).addClass("on");
		}else{
			$(id).removeClass("on");
		}
		//filterSubmit('refresh');
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>