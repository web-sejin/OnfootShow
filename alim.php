<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");

	$sql = " select count(*) cnt from a_alim where mb_idx = '{$member['mb_no']}' ";
	$row = sql_fetch($sql);
	$total_count = $row['cnt'];

	$sql = " select * from a_alim where mb_idx = '{$member['mb_no']}' order by alim_datetime desc limit 0, 15 ";
	$result = sql_query($sql);
?>

<div class="alim_area cm_padd4">
	<ul class="alim_list">
		<?php for($i=0; $row=sql_fetch_array($result); $i++){?>
		<li>
			<div class="alim_info">
				<p class="alim_txt1"><?php echo $row['alim_info']?></p>
				<p class="alim_txt2"><?php echo $row['alim_content']?></p>
			</div>
			<p class="alim_date"><?php echo date("Y. m. d", strtotime($row['alim_datetime']))?></p>
		</li>
		<?php }?>
		<!--li>
			<div class="alim_info">
				<p class="alim_txt1">13:20 / 1구장 / 홍길동 (매칭글 주인)</p>
				<p class="alim_txt2">매치 신청이 들어왔습니다!</p>
			</div>
			<p class="alim_date">2023. 11. 01</p>
		</li-->

		<?php if($total_count < 1){?>
		<li class="not_data">알림 내역이 없습니다.</li>
		<?php }?>
	</ul>

	<input type="hidden" id="limit_num" value="15" style="position:fixed;left:0;top:0;">
</div>

<script>
$(window).scroll(function() {
	const scrollTop = $(window).scrollTop();
	const innerHeight = $(window).height();
	const scrollHeight = $(document).height();
	const lastHeight = $(".alim_list li:last-child").height();

	if (scrollTop + innerHeight >= scrollHeight-lastHeight) {
		//console.log("bottom!!!");
		addAlim();			
	}
});

function addAlim(){
	const limitVal = $("#limit_num").val();
	$.ajax({
		type: "POST",
		url: "<?php echo G5_URL?>/inc/add_alim.php",
		data: {limitVal: limitVal,}, 
		cache: false,
		async: false,
		contentType : "application/x-www-form-urlencoded; charset=UTF-8",
		success: function(data) {
			//console.log(data);
			$(".alim_list").append(data);
			if((limitVal*1) <= "<?php echo $total_count?>"){
				$("#limit_num").val((limitVal*1)+10);
			}
		}
	});
}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>