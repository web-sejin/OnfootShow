<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select * from a_league where al_idx = '{$idx}' ";
	$row = sql_fetch($sql);
	$stateClass = "";
	if($row['al_end'] && $row['al_end'] < G5_TIME_YMD){
		$stateClass = "off";
	}
?>

<div class="notice_view cm_padd4">
	<?php if($row['al_cate'] != 3){?><div class="notive_view_hd"><?php }?>
		<p class="notice_subject"><?php echo $row['al_subject']?></p>
		<?php if($row['al_cate'] != 3){?>
			<p class="notice_date">
				<?php 
					$date = "";
					$date .= date("Y. m. d", strtotime($row['al_start']))." ~";
					if($row['al_end']){
						$date .= " ".date("Y. m. d", strtotime($row['al_end']));
					}
					echo $date;
				?>
			</p>
			<?php if($stateClass == "off"){?>
				<p class="league_view_st ver2">종료</p>
			<?php }else{?>
				<p  class="league_view_st">진행중</p>
			<?php }?>
		<?php }else{?>
			<p class="notice_date"><?php echo date("Y. m. d", strtotime($row['al_datetime']))?></p>
		<?php }?>
	<?php if($row['al_cate'] != 3){?></div><?php }?>
	<div class="notice_cont"><?php echo $row['al_content']?></div>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>