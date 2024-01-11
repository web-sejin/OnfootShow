<?php
	include_once("_common.php");

	$sql = " select * from a_alim where mb_idx = '{$member['mb_no']}' order by alim_datetime desc limit {$limitVal}, 15 ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
?>
<li>
	<div class="alim_info">
		<p class="alim_txt1"><?php echo $row['alim_info']?></p>
		<p class="alim_txt2"><?php echo $row['alim_content']?></p>
	</div>
	<p class="alim_date"><?php echo date("Y. m. d", strtotime($row['alim_datetime']))?></p>
</li>
<?php }?>