<?php
	include_once("_common.php");

	if($si_idx != "all"){
	$sql = " select * from rb_dongli where si_idx = '{$si_idx}' group by do_name order by do_name asc ";
	$result = sql_query($sql);
?>
	<option value="all">전체</option>
	<?php for($i=0; $row=sql_fetch_array($result); $i++){ ?>
	<option value="<?php echo $row['do_idx']?>"><?php echo $row['do_name']?></option>
<?php }}else{?>
	<option value="">전체</option>
<?php }?>
