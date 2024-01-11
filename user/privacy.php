<?php
	include_once('../common.php');
	include_once(G5_PATH."/_head.php");

	$sql = " select * from a_privacy where 1 ";
	$row = sql_fetch($sql);
?>

<div class="poll_area cm_padd4">
	<?php echo $row['ap_privacy']?>
</div>

<?php
	include_once(G5_PATH."/_tail.php");
?>