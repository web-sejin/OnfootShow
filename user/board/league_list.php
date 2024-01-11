<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");
	
	$total = 0;
	$sql = " select * from a_league where al_cate = '{$cate}' order by al_datetime desc ";
	$result = sql_query($sql);
?>

<div class="lg_list cm_padd4">
	<?php if($cate != 3){?>
	<ul class="lg_ul">
		<?php 			
			for($i=0; $row=sql_fetch_array($result); $i++){
				$total ++;
				$stateClass = "";
				if($row['al_end'] && $row['al_end'] < G5_TIME_YMD){
					$stateClass = "off";
				}
		?>
		<li class="<?php echo $stateClass?>">
			<a href="<?php echo G5_URL?>/user/board/league_view.php?idx=<?php echo $row['al_idx']?>&cate=<?php echo $row['al_cate']?>">
				<img src="<?php echo G5_THEME_IMG_URL?>/sample.jpg" alt="">
				<div class="lg_info">
					<p><?php echo $row['al_subject']?></p>
					<p>
						<?php 
							$date = "";
							$date .= " (".date("Y. m. d", strtotime($row['al_start']))." ~";
							if($row['al_end']){
								$date .= " ".date("Y. m. d", strtotime($row['al_end']));
							}
							$date .= ")";
							echo $date;
						?>
					</p>
				</div>
				<span class="lg_st"><?php if($stateClass == "off"){ echo "종료"; }else{ echo "진행중"; }?></span>
			</a>
		</li>
		<?php }?>

		<?php if($total < 1){?>
		<li class="not_data">작성된 정보가 없습니다.</li>
		<?php }?>
	</ul>
	<?php }else{?>
	<ul class="notice_ul">
		<?php 
			for($i=0; $row=sql_fetch_array($result); $i++){
				$total++;
		?>
		<li>
			<a href="<?php echo G5_URL?>/user/board/league_view.php?idx=<?php echo $row['al_idx']?>&cate=<?php echo $row['al_cate']?>">
				<strong><?php echo $row['al_subject']?></strong>
				<span><?php echo date("Y. m. d", strtotime($row['al_datetime']))?></span>
			</a>
		</li>
		<?php }?>

		<?php if($total < 1){?>
		<li class="not_data">작성된 정보가 없습니다.</li>
		<?php }?>
	</ul>
	<?php }?>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>