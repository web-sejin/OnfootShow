<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");
?>

<div class="qna_list cm_padd4">
	<ul class="qna_ul">
		<?php 
			$sql = " select * from a_qna where mb_idx = '{$member['mb_no']}' order by aq_datetime desc ";
			$result = sql_query($sql);
			for($i=0; $row=sql_fetch_array($result); $i++){
				$total ++;
		?>
		<li>
			<a href="<?php echo G5_URL?>/user/board/qna_view.php?idx=<?php echo $row['aq_idx']?>">
				<p class="qna_subject"><?php echo $row['aq_subject']?></p>
				<p class="qna_info">
					<?php if($row['aq_answer']){?>
					<strong class="on">[답변완료]</strong>
					<?php }else{?>
					<strong>[답변대기]</strong>
					<?php }?>
					<span><?php echo date("Y. m. d", strtotime($row['aq_datetime']))?></span>
				</p>
			</a>
		</li>
		<?php }?>

		<?php if($total < 1){?>
		<li class="not_data">문의 내역이 없습니다.</li>
		<?php }?>
	</ul>
</div>

<div class="fix_btn_back"></div>
<div class="fix_btn_box">
	<a href="<?php echo G5_URL?>/user/board/qna_write.php" class="fix_btn on">글 쓰기</a>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>