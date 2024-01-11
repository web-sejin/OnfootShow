<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");
?>

<div class="notice_list cm_padd4">
	<ul class="notice_ul">
		<?php 
			$total = 0;
			$sql = " select * from a_notice where 1 order by an_datetime desc ";
			$result = sql_query($sql);
			for($i=0; $row=sql_fetch_array($result); $i++){
				$total++;
		?>
		<li>
			<a href="<?php echo G5_URL?>/user/board/notice_view.php?idx=<?php echo $row['an_idx']?>">
				<strong><?php echo $row['an_subject']?></strong>
				<span><?php echo date("Y. m. d", strtotime($row['an_datetime']))?></span>
			</a>
		</li>
		<?php }?>

		<?php if($total < 1){?>
		<li class="not_data">작성된 정보가 없습니다.</li>
		<?php }?>
	</ul>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>