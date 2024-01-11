<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");
?>

<form name="schedule_frm" method="post" action="<?php echo G5_URL?>/schedule_write.php">
	<div class="sub_schedule cm_padd3">		
		<input type="hidden" name="url" value="2">
		<input type="hidden" id="next_ipt">
		<input type="hidden" name="time_pick_ipt" id="time_pick_ipt">
		<div class="scd_info ver2">
			<p class="scd_date">
				<input type="text" name="s_datepicker" id="s_datepicker" class="scd_date_ipt" readonly value="<?php echo date("Y. m. d", strtotime(G5_TIME_YMD))?>">
			</p>		
			<select name="s_stadium" id="s_stadium" onChange="getSchedule();">
				<?php
					$sql = " select * from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 order by as_datetime asc ";
					$result = sql_query($sql);
					for($i=0; $row=sql_fetch_array($result); $i++){
				?>
				<option value="<?php echo $row['as_idx']?>"><?php echo $row['as_name']?></option>
				<?php }?>
			</select>
		</div>

		<div class="scd_list_box">		
			<ul  class="scd_list" id="scd_list">
				<?php 
					$curr_date = G5_TIME_YMD;
					$curr_stadium = getFirstStadium($member['mb_no']);
					if($as_idx){ $curr_stadium = $as_idx; }
					for($i=$member['mb_fs_start']; $i<$member['mb_fs_end']; $i++){												
						$sql = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx, A.scd_name, A.scd_res_cert
										from a_schedule_res A, a_schedule_res_time B
										where 1
											and A.scd_idx = B.scd_idx
											and A.as_idx = '{$curr_stadium}' 
											and A.scd_date = '{$curr_date}' 											
											and A.delete_state = 1 
											and B.scdt_time = {$i}
											and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
										";
						$row = sql_fetch($sql);
						//시간이 지난 경기 = 회색 , scd_gray
						//매치가 잡히지 않은 경기 = 초록색 , scd_green
						//자체경기 또는 매치확정 경기 = 파란색 , scd_blue
						//고정팀 = 빨간색 , scd_red
						//관리자에 의한 예약막음 = 임의예약

						$use_state = true;
						$bgClass = "";
						if($row['scd_match_type'] == 1){ $bgClass = "scd_blue"; }
						if($row['scd_match_type'] == 2){ 
							if($row['scd_state'] == 1){
								$bgClass = "scd_blue";
							}else{
								$bgClass = "scd_green"; 
							}
						}
						if($row['scd_match_sort'] == 2){ $bgClass = "scd_red"; }
						
						$hour = $i;
						if($row['scd_start']){
							$hour = $row['scd_start'];
						}
						$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
						if(G5_TIME_YMDHIS >= $this_date){
							$use_state = false;
							$bgClass = "scd_gray"; 
						}
				?>
				<li id="scd_li_<?php echo $i?>" class="scd_li <?php echo $bgClass?>">				
					<?php if($row['cnt'] > 0){ ?>
						<?php if($row['scd_res_cert'] == 0){?>
							<a onClick="pageChange('<?php echo G5_URL?>/reserve_list.php');">
						<?php }else{?>
							<a onClick="pageChange('<?php echo G5_URL?>/schedule_write.php?w=u&idx=<?php echo $row['scd_idx']?>&url=2');">
						<?php }?>
					<?php }?>

					<input type="checkbox" name="time_chk[]" id="tc_<?php echo $i?>" value="<?php echo $i?>" onChange="sideChk('<?php echo $i?>');" <?php if($row['cnt'] > 0 || !$use_state){ echo "disabled"; }?>>
					<label for="tc_<?php echo $i?>">
						<strong class="scd_strong1">
							<span class="scd_chk_circle"></span>
							<span class="scd_chk_time">
								<?php echo sprintf('%02d', $i);?>:00 ~ <?php echo sprintf('%02d', ($i+1));?>:00
							</span>
						</strong>
						
						<?php if($row['cnt'] > 0 && $row['scd_res_cert'] == 0){?>
							<strong class="scd_strong2">예약확인</strong>
						<?php }else{?>
							<?php if($row['cnt'] > 0 && $row['scd_match_type'] != 3){ ?>
							<strong class="scd_strong2">
								<?php if($row['scd_match_sort'] == 2){?>
								<span class="scd_fix_team">고정팀</span>
								<?php }?>

								<span class="scd_match_team">
									<?php 
										if($row['scd_match_type'] == 1){
											if($row['scd_team_name'] != ""){
												echo $row['scd_team_name'];
											}else{
												echo $row['scd_name'];
											}
										}else if($row['scd_match_type'] == 2){
											if($row['scd_state'] == 1){
												echo $row['scd_team_name']." &nbsp; vs &nbsp; ".getOtherTeam($row['scd_vs_team_idx'], 'other_team_name');
											}else{
												echo $row['scd_team_name']." &nbsp; vs &nbsp; -";
											}
										}
									?>
								</span>
							</strong>
							<?php }?>			
						<?php }?>
						
						<?php if($row['scd_match_type'] == 3){?>
						<strong class="scd_my_res">임의예약</strong>
						<?php }else{?>
						<strong class="scd_type_box">							
							<?php if($row['scd_res_type'] == 1){?><span class="scd_type1"><img src="<?php echo G5_THEME_IMG_URL?>/logo_base.png" alt=""></span><?php }?>
							<?php if($row['scd_match_type'] == 1){?><span class="scd_type2">자체</span><?php }?>
							<?php if($row['scd_match_type'] == 2){?><span class="scd_type3">매치</span><?php }?>
						</strong>
						<?php }?>
					</label>

					<?php if($row['cnt'] > 0){ ?>
					</a>
					<?php }?>
				</li>
				<?php }?>
			</ul>
		</div>
	</div>
	
	<div class="fix_btn_back"></div>
	<div class="fix_btn_box">
		<button type="button" class="fix_btn" id="submit_button" disabled onClick="insertGame();">경기등록</button>
	</div>
</form>

<script>	
	$(function(){
		if("<?php echo $as_idx?>"){
			$("#s_stadium").val("<?php echo $as_idx?>");
		}
	});
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

		//alert('등록페이지로 이동!');
		$("form[name=schedule_frm]").submit();
	}

	$('.scd_date_ipt').datepicker({
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
			getSchedule();
		},
		onClose:function(){
			$(".datepicker_back").fadeOut();
		},		
	});

	function getSchedule(){
		//s_stadium
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/get_schedule_list.php",
			data: {s_datepicker:$("#s_datepicker").val(), s_stadium:$("#s_stadium").val()}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#next_ipt").val('');
				$("#time_pick_ipt").val('');
				$("#scd_list").empty().append(data);
			}
		});
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>