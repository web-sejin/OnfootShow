<?php
	include_once("_common.php");

	$sql = " select * from a_alim_user where mb_idx = '{$member['mb_no']}' order by alim_datetime desc limit {$limitVal}, 15 ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
?>
<li>
	<div class="alim_info">
		<p class="alim_txt1"><?php echo date("Y. m. d", strtotime($row['alim_datetime']))?></p>
		<p class="alim_txt2"><?php echo $row['alim_content']?></p>
		<?php if($row['alim_content2']){?>
		<p class="alim_txt3"><?php echo $row['alim_content2']?></p>
		<?php }?>
	</div>
</li>
<?php }?>