<?php
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/index.php');
    return;
}

if($member['mb_type'] == 1){
	goto_url(G5_URL."/user/");
}

include_once(G5_THEME_PATH.'/head.php');

$time1 = $member['mb_fs_start'];
$time2 = $member['mb_fs_end'];
?>

<div class="all_schedule">
	<div class="scd_info">
		<p class="scd_date">
			<input type="text" name="s_datepicker" id="s_datepicker" class="scd_date_ipt" readonly value="<?php echo date("Y. m. d", strtotime(G5_TIME_YMD))?>">
		</p>
		<ul class="scd_st_ul">
			<li>
				<span class="blue"></span>
				<p>매치 확정</p>
			</li>
			<li>
				<span class="green"></span>
				<p>매치 대기</p>
			</li>
		</ul>
		<ul class="scd_st_ul">
			<li>
				<span class="white"></span>
				<p>등록 가능</p>
			</li>
			<li>
				<span class="red"></span>
				<p>매치 고정 팀</p>
			</li>
		</ul>
	</div>
	<div class="all_scd_box">
		<ul class="all_scd_tb ver2">
				<li></li>
				<?php for($i=$time1; $i<$time2; $i++){?>
				<li><?php echo sprintf('%02d', $i)?>:00~<?php echo sprintf('%02d', $i+1)?>:00</li>
				<?php }?>
		</ul>
		<div class="all_scd_scroll">
			<div class="all_scd_scroll_wrap" id="all_scd_scroll_wrap">
				<?php 
					$curr_date = G5_TIME_YMD;
					$sql = " select * from a_stadium where mb_idx = '{$member['mb_no']}' and as_delete_st = 1 order by as_name asc, as_updatetime desc ";
					$result = sql_query($sql);
					for($i=0; $row=sql_fetch_array($result); $i++){						
				?>
				<ul class="all_scd_tb">
					<li><?php echo $row['as_name']?></li>
					<?php 
						for($j=$time1; $j<$time2; $j++){
								$sql2 = " select count(*) cnt, A.scd_date,  A.scd_start, A.scd_end, A.scd_idx, A.scd_match_type, A.scd_match_sort, A.atf_idx, A.scd_team_name, A.scd_state, A.scd_res_type, A.scd_vs_team_idx, A.scd_vs_team_idx, A.scd_res_cert
											from a_schedule_res A, a_schedule_res_time B
											where 1
												and A.scd_idx = B.scd_idx
												and A.as_idx = '{$row['as_idx']}' 
												and A.scd_date = '{$curr_date}' 											
												and A.delete_state = 1 
												and B.scdt_time = {$j}
												and (A.scd_res_cert = 0 or A.scd_res_cert = 1)
											";
							$row2 = sql_fetch($sql2);
							
							$use_state = true;
							$bgClass = "";
							$bgClass2 = "";
							if($row2['scd_match_type'] == 1){ $bgClass = "blue"; }
							if($row2['scd_match_type'] == 2){ 
								if($row2['scd_state'] == 1){
									$bgClass = "blue";
								}else{
									$bgClass = "green"; 
								}
							}
							if($row2['scd_match_sort'] == 2){ $bgClass = "red"; }							
							$time_pick_ipt = $j."|".($j+1);

							$hour = $j;
							if($row2['scd_start']){
								$hour = $row2['scd_start'];
							}
							$this_date = $curr_date." ".sprintf('%02d', $hour).":00:00";
							if(G5_TIME_YMDHIS >= $this_date){
								$use_state = false;
								$bgClass2 = "gray"; 
							}
					?>					
					<li>
						<?php if($row2['cnt'] > 0){?>							
							<?php if($row2['scd_res_cert'] == 0){?>
								<a href="<?php echo G5_URL?>/reserve_list.php">예약확인</a>
							<?php }else{?>
								<a href="<?php echo G5_URL?>/schedule_write.php?w=u&idx=<?php echo $row2['scd_idx']?>&url=1" class="scd_state <?php echo $bgClass?>">
									<?php if($row2['scd_match_type'] == 3){?>임의예약<?php }?>
								</a>
							<?php }?>
						<?php }else{?>
							<?php if($j+1 == $time2){?>
								<a onClick="javascript:showToast('마지막 시간은 등록할 수 없습니다.');" class="scd_state <?php echo $bgClass?> <?php echo $bgClass2?>"></a>
							<?php }else{?>
								<a <?php if($use_state){?>href="<?php echo G5_URL?>/schedule_write.php?s_datepicker=<?php echo $curr_date?>&time_pick_ipt=<?php echo $time_pick_ipt?>&url=1&stadium_idx=<?php echo $row['as_idx']?>"<?php }?> class="scd_state <?php echo $bgClass?> <?php echo $bgClass2?>"></a>
							<?php }?>
						<?php }?>						
					</li>
					<?php }?>
					<!--
					<li><a href="" class="scd_state blue"></a></li>
					<li><a href="" class="scd_state green"></a></li>
					<li><a href="" class="scd_state red"></a></li>
					-->
				</ul>
				<?php }?>
			</div>
		</div>
	</div>
</div>

<script>
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
		getMyStadium();
	},
	onClose:function(){
		$(".datepicker_back").fadeOut();
	},		
});

function getMyStadium(){
	const currDate = $("#s_datepicker").val();
	if(currDate != ""){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/getMyStadium.php",
			data: {currDate:currDate}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				$("#all_scd_scroll_wrap").empty().append(data);
			}
		});
	}
}
</script>

<?php
include_once(G5_THEME_PATH.'/tail.php');