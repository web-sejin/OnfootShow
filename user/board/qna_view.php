<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select * from a_qna where aq_idx = '{$idx}' ";
	$row = sql_fetch($sql);
?>

<div class="qna_view cm_padd4">
	<p class="qna_subject">
		<?php if($row['aq_answer']){?><span class="on">[답변완료]</span><?php }else{?><span>[답변대기]</span><?php }?><?php echo $row['aq_subject']?>
	</p>
	<p class="qna_date"><?php echo date("Y. m. d", strtotime($row['aq_datetime']))?></p>
	<div class="qna_cont">
		<?php echo nl2br($row['aq_question'])?>

		<?php
			$sql2 = " select * from a_qna_img where aq_idx = '{$idx}' ";
			$result2 = sql_query($sql2);
			for($i=0; $img=sql_fetch_array($result2); $i++){
		?>
		<p class="qna_img"><img src="<?php echo G5_DATA_URL?>/qnaFile/<?php echo $img['aqi_img_af']?>" alt=""></p>
		<?php }?>
	</div>

	<?php if($row['aq_answer']){?>
	<div class="qna_answer">
		<p class="qna_answer_info">
			<strong>[관리자 답변]</strong>
			<span><?php echo date("Y. m. d", strtotime($row['aq_answer_datetime']))?></span>
		</p>
		<div class="qna_answer_txt">
			<?php echo nl2br($row['aq_answer'])?>
		</div>
	</div>
	<?php }?>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>