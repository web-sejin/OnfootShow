<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select * from a_notice where an_idx = '{$idx}' ";
	$row = sql_fetch($sql);
?>

<div class="notice_view cm_padd4">
	<p class="notice_subject"><?php echo $row['an_subject']?></p>
	<p class="notice_date"><?php echo date("Y. m. d", strtotime($row['an_datetime']))?></p>
	<div class="notice_cont"><?php echo $row['an_content']?></div>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>