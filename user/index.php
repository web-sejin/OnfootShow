<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");
	
	$today = G5_TIME_YMD;
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
	
	$add_query = " and am_date = '{$today}' ";
	//$add_query = "";
	$local = false;

	$sql_common = " FROM a_match A 											
											LEFT JOIN (SELECT * FROM a_schedule_res WHERE 1) B ON A.scd_idx = B.scd_idx
											WHERE 1
												AND A.delete_st = 1	
												AND (B.delete_state = 1 OR B.delete_state IS NULL)
												AND (B.scd_state = 0 or B.scd_state is NULL)
												AND (B.scd_res_cert = 0 OR B.scd_res_cert = 1 OR B.scd_res_cert IS NULL)
												AND A.am_rematch = 0
											";
	$sql_order = " ORDER BY am_datetime DESC ";
	$sql_limit = "limit 0, 10";

	$sql = " SELECT COUNT(*) cnt 
					{$sql_common}
					{$add_query} 
					";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	$sql = " SELECT A.*, B.as_idx, B.scd_state
					, (SELECT SUM(scd_score1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS score_sum
					, (SELECT SUM(scd_score2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS score_sum2
					, (SELECT SUM(scd_tention1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_tention1 > 0) AS tention_sum
					, (SELECT SUM(scd_tention2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_tention2 > 0) AS tention_sum2
					, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS result_cnt
					, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS result_cnt2
					{$sql_common}
					{$add_query} 
					{$sql_order} 
					{$sql_limit}
					";
	$result = sql_query($sql);	

	if($myLat != "" && $myLat != "00" && $myLon != "" && $myLon != "0"){
		$local = true;		

		$sql_common = " , (6371*acos(cos(radians({$myLat}))*cos(radians(lat))*cos(radians(lng)-radians({$myLon}))+sin(radians({$myLat}))*sin(radians(lat)))) AS distance
												FROM a_match A 
												LEFT JOIN (SELECT * FROM a_schedule_res C WHERE 1) B ON A.scd_idx = B.scd_idx												
												WHERE 1
													AND A.delete_st = 1
													AND (B.delete_state = 1 OR B.delete_state IS NULL)
													AND (B.scd_state = 0 or B.scd_state IS NULL) 
													AND (B.scd_res_cert = 0 OR B.scd_res_cert = 1 OR B.scd_res_cert IS NULL)
													AND A.am_rematch = 0
											";
		$sql_order = " ORDER BY distance ASC, A.am_datetime DESC ";

		$sql = " SELECT count(*) cnt
				{$sql_common}
				{$add_query}
		";	
		$row = sql_fetch($sql);
		$total_count = $row['cnt'];

		$sql = " SELECT A.*, B.*
			, (SELECT SUM(scd_score1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS score_sum
			, (SELECT SUM(scd_score2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS score_sum2
			, (SELECT SUM(scd_tention1) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_tention1 > 0) AS tention_sum
			, (SELECT SUM(scd_tention2) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_tention2 > 0) AS tention_sum2
			, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND mb_idx = A.mb_idx AND scd_team_idx = A.at_idx AND scd_score1 > 0) AS result_cnt
			, (SELECT COUNT(*) FROM a_schedule_res WHERE scd_state=1 AND scd_idx IS NOT NULL AND scd_vs_team_mb_idx = A.mb_idx AND scd_vs_team_at_idx = A.at_idx AND scd_score2 > 0) AS result_cnt2
			{$sql_common}
			{$add_query}
			{$sql_order}
			{$sql_limit}
		";	
		$result = sql_query($sql);
	}
	//echo "<br><br><br><br><br>".$sql;
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

	<ul class="url_ul" id="main_ul">
		<?php 
			for($i=0; $row=sql_fetch_array($result); $i++){
				$levelKey = array_search($row['am_level'], array_column($_cfg['match']['level'], 'val'));

				$score = 0;
				$tention = 0;
				$matchCnt = $row['result_cnt']+$row['result_cnt2'];
				if($matchCnt > 0){
					$score = round(($row['score_sum']+$row['score_sum2'])/$matchCnt, 1);
					$tention = round(($row['tention_sum']+$row['tention_sum2'])/$matchCnt, 1);
				}
		?>
		<li class="url_li">
			<a href="<?php echo G5_URL?>/user/match_view.php?idx=<?php echo $row['am_idx']?>" <?php if($row['res_st'] == 2 || $row['res_st'] == 3){?>class="res_st"<?php }?>>
				<?php if($row['res_st'] == 2 || $row['res_st'] == 3){?><p class="url_res_st">예약</p><?php }?>
				<div class="url_type_box">
					<p class="url_type ver2"><?php echo $_cfg['match']['level'][$levelKey]['txt'];?></p>
					<p class="url_type ver2">텐션 <?php echo $tention?></p>
				</div>
				<h3 class="url_name"><?php echo $row['am_team_name']?></h3>
				<p class="url_info1">
					<span><?php echo date("Y. m. d", strtotime($row['am_date']))?> (<?php echo getYoil($row['am_date'])?>)</span>
					<span>
						<?php 
							if($row['res_st'] == 2 || $row['res_st'] == 3){
								echo sprintf('%02d', $row['scd_start']).":00 ~ ".sprintf('%02d', $row['scd_end']+1).":00";
							}else{
								echo sprintf('%02d', $row['am_time']).":00";
							}
						?>
					</span>
				</p>
				<?php if($row['res_st'] == 1){?>
				<ul class="ust_sub_info">
					<?php if($row['am_area'] == 1){?>
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?></span>
					</li>
					<?php }else if($row['am_area'] == 2){?>
						<?php
							if($row['fs_mb_idx1']){
								$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx1']}' ");
						?>
						<li>
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
						</li>
						<?php }?>
						<?php
							if($row['fs_mb_idx2']){
								$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx2']}' ");
						?>
						<li>
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
						</li>
						<?php }?>
						<?php
							if($row['fs_mb_idx3']){
								$row2 = sql_fetch(" select * from g5_member where mb_no = '{$row['fs_mb_idx3']}' ");
						?>
						<li>
							<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
							<span><?php echo getLocalName($row2['sd_idx'], $row2['si_idx'], $row2['do_idx'])?> <?php echo $row2['mb_fs_name']?></span>
						</li>
						<?php }?>
					<?php }?>
				</ul>
				<?php }else if($row['res_st'] == 2){?>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?> <?php echo getFutsalStadiumName($row['as_idx'])?> (<?php echo getStadiumName($row['as_idx'])?>)</span>
					</li>
				</ul>
				<?php }else if($row['res_st'] == 3){?>
				<ul class="ust_sub_info">
					<li>
						<strong><img src="<?php echo G5_THEME_IMG_URL?>/user_ic_local.svg" alt=""></strong>
						<span><?php echo getLocalName($row['sd_idx'], $row['si_idx'], $row['do_idx'])?> <?php echo getFutsalStadiumName($row['as_idx'])?> (<?php echo getStadiumName($row['as_idx'])?>)</span>
					</li>
				</ul>
				<?php }?>
				<ul class="url_info3">
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_star.svg" alt="">
						<span><b><?php echo $score?></b></span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_eye.svg" alt="">
						<span><?php echo number_format($row['am_view'])?></span>
					</li>
					<li></li>
					<li>
						<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball.svg" alt="">
						<span><?php echo number_format(reqCnt($row['am_idx']))?></span>
					</li>
				</ul>
				<ul class="url_info2 ver2">
					<?php if($row['res_st'] == 3){?><li class="adm_mode">관리자 등록</li><?php }?>
					<?php 
						$toKey = array_search($row['am_to'], array_column($_cfg['stadium']['to'], 'val'));
						$ageKey = array_search($row['am_age'], array_column($_cfg['match']['age'], 'val'));
						$genderKey = array_search($row['am_gender'], array_column($_cfg['match']['gender'], 'val'));
					?>
					<?php if($row['am_to']){?><li><?php echo $_cfg['stadium']['to'][$toKey]['txt'];?></li><?php }?>
					<?php if($row['am_age']){?><li><?php echo $_cfg['match']['age'][$ageKey]['txt'];?></li><?php }?>
					<?php if($row['am_gender']){?><li><?php echo $_cfg['match']['gender'][$genderKey]['txt'];?></li><?php }?>
					<?php if($row['am_bet'] == 1){ echo "<li>구장비 내기</li>"; }else if($row['am_bet'] == 2){ echo "<li>음료수 내기</li>"; }?>			
				</ul>
			</a>
		</li>
		<?php }?>
		<?php if($total_count < 1){?>
		<li class="not_data">등록된 매치가 없습니다.</li>
		<?php }?>
	</ul>
	<input type="hidden" id="limit_num" value="10" style="position:fixed;left:0;top:0;">
</div>

<a href="<?php echo G5_URL?>/user/match_write.php" class="match_write_btn">
	<img src="<?php echo G5_THEME_IMG_URL?>/ic_write_plus.svg" alt="">
</a>

<div id="filter_pop" class="cm_pop">
	<p class="cm_pop_back"></p>
	<div class="filter_pop_cont ver2">
		<p class="filter_pop_tit">검색 필터</p>		
		<input type="hidden" name="s_level_list" id="s_level_list">
		<input type="hidden" name="s_age_list" id="s_age_list">
		<input type="hidden" name="s_local_list" id="s_local_list">
		<button type="button" class="filter_pop_off" onClick="cmPopOff('filter_pop');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">분류</p>
				<div class="regi_td">
					<select name="sort" id="sort" class="regi_ipt regi_select regi_select2">
						<option value="all">전체</option>
						<option value="1">일반등록</option>
						<option value="2">관리자등록</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">매칭 레벨</p>
				<div class="regi_td">
					<ul class="st_frm_chk ver3">
						<?php for($i=0; $i<count($_cfg['match']['level']); $i++){?>
						<li>							
							<input type="checkbox" name="level[]" id="level_<?php echo $i?>" value="<?php echo $_cfg['match']['level'][$i]['val']?>">
							<label for="level_<?php echo $i?>"><?php echo $_cfg['match']['level'][$i]['txt']?></label>
						</li>
						<?php }?>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">경기 평점</p>
				<div class="regi_td">
					<select name="score" id="score" class="regi_ipt regi_select regi_select2">
						<option value="all">전체</option>
						<option value="1">오름차순</option>
						<option value="2">내림차순</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">텐션</p>
				<div class="regi_td">
					<select name="tention" id="tention" class="regi_ipt regi_select regi_select2">
						<option value="all">전체</option>
						<option value="1">오름차순</option>
						<option value="2">내림차순</option>
					</select>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">지역 (최대 3개)</p>
				<div class="regi_td regi_td_flex">
					<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select regi_select2 ver5" onchange="chgSido(this.value);">
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
					<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select regi_select2 ver5" onChange="chgSigugun(this.value);">
						<option value="">시/구/군</option>
					</select>
				</div>
				<ul class="local_list"></ul>
			</li>			
			<li class="regi_li">
				<p class="regi_th">연령대</p>
				<div class="regi_td">
					<ul class="st_frm_chk ver3">
						<?php for($i=0; $i<count($_cfg['match']['age']); $i++){?>
						<li>							
							<input type="checkbox" name="age[]" id="age_<?php echo $i?>" value="<?php echo $_cfg['match']['age'][$i]['val']?>">
							<label for="age_<?php echo $i?>"><?php echo $_cfg['match']['age'][$i]['txt']?></label>
						</li>
						<?php }?>
					</ul>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">내기유형</p>
				<div class="regi_td">
					<select name="bet" id="bet" class="regi_ipt regi_select regi_select2">
						<option value="all">전체</option>
						<option value="1">구장비내기</option>
						<option value="2">음료수내기</option>
						<option value="3">내기없음</option>
					</select>
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
		
		if(v == "36"){
			const li_len = $(".local_list li").length;
			if(li_len >= 3){
				showToast("지역은 최대 3개까지 선택할 수 있습니다.");
			}else{
				$.ajax({
					type: "POST",
					url: "<?php echo G5_URL?>/inc/create_local.php",
					data: {sd_idx:v, len:li_len},
					cache: false,
					async: false,
					contentType : "application/x-www-form-urlencoded; charset=UTF-8",
					success: function(data) {
						$(".local_list").append(data);
					}
				});
			}
		}
	}

	function chgSigugun(v){
		const sido_val = $("#sd_idx").val()
		const li_len = $(".local_list li").length;
		if(li_len >= 3){
			showToast("지역은 최대 3개까지 선택할 수 있습니다.");
		}else{
			if(v){
				$.ajax({
					type: "POST",
					url: "<?php echo G5_URL?>/inc/create_local.php",
					data: {sd_idx:sido_val, si_idx:v, len:li_len},
					cache: false,
					async: false,
					contentType : "application/x-www-form-urlencoded; charset=UTF-8",
					success: function(data) {
						$(".local_list").append(data);
					}
				});
			}
		}
	}

	function deleteLocal(v){
		$(`#local_li_${v}`).remove();

		$(".local_list li").each(function(i){
			$(this).attr("id", `local_li_${i}`);
			$(this).children("button").attr("onClick", `deleteLocal('${i}');`);
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

	function searchList(){
		if($("#sch_val").val() == ""){
			showToast("구장명을 입력해 주세요.");
			return false;
		}

		filterSubmit('refresh');
	}

	$(window).scroll(function() {
		const scrollTop = $(window).scrollTop();
		const innerHeight = $(window).height();
		const scrollHeight = $(document).height();
		const lastHeight = ($("#main_ul .url_li:last-child").height());

		if (scrollTop + innerHeight >= scrollHeight-lastHeight) {
			//console.log("bottom!!!");
			filterSubmit('limit');			
		}
	});

	function filterSubmit(v){
		const s_date = $("#s_date").val();
		const s_start = $("#s_start").val();
		const s_end = $("#s_end").val();
		const sort = $("#sort").val();
		const score = $("#score").val();
		const tention = $("#tention").val();
		const bet = $("#bet").val();
		let limitVal = 0;
		if(v == "limit"){
			limitVal = $("#limit_num").val();
		}

		let levelKey = [];	
		$("input[name='level[]']:checked").each(function(){ levelKey.push($(this).val()); });		
		$("#s_level_list").val(levelKey.join("|"));
		
		let ageKey = [];
		$("input[name='age[]']:checked").each(function(){ ageKey.push($(this).val()); });
		$("#s_age_list").val(ageKey.join("|"));

		let localKey = [];
		$("input[name='local[]']").each(function(){ localKey.push($(this).val()); });
		$("#s_local_list").val(localKey.join("||"));
		
		if((sort!="" && sort!="all") || (score!="" && score!="all" ) || (tention!="" && tention!="all") || (bet!="" && bet!="all") || levelKey.length > 0 || ageKey.length > 0 || localKey.length > 0){
			/*console.log("sort : "+sort);
			console.log("score : "+score);
			console.log("tention : "+tention);
			console.log("bet : "+bet);
			console.log("levelKey : "+levelKey.length);
			console.log("ageKey : "+ageKey.length);
			console.log("localKey : "+localKey.length);*/
			$(".dtail_filter_btn").addClass("on");
		}else{
			$(".dtail_filter_btn").removeClass("on");
		}
		
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getUserMatchList.php",
			data: {
				date: s_date,
				start: s_start,
				end: s_end,
				sort: sort,
				score: score,
				tention: tention,					
				bet: bet,
				levelList: $("#s_level_list").val(),
				ageList: $("#s_age_list").val(),
				localList: $("#s_local_list").val(),
				limitVal: limitVal,
				subject: $("#sch_val").val(),
			}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				//console.log(data);
				if(v == "limit"){
					$("#main_ul").append(data);
					if((limitVal*1) <= "<?php echo $total_count?>"){
						$("#limit_num").val((limitVal*1)+10);
					}
				}else{
					$("#main_ul").empty().append(data);
					$("#limit_num").val("10");
				}
				cmPopOff('filter_pop');
			}
		});
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>