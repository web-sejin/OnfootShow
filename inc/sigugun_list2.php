<?php
	include_once("_common.php");

	$sql = " select * from rb_sigungu where sd_idx = '{$sd_idx}' order by si_name asc ";
	$result = sql_query($sql);
?>
<?php if($sd_idx == "36"){?>
	<option value="">없음</option>
<?php }else{?>
	<option value="">시/구/군</option>
	<option value="all">전체</option>
	<?php for($i=0; $row=sql_fetch_array($result); $i++){ ?>
	<option value="<?php echo $row['si_idx']?>"><?php echo $row['si_name']?></option>
	<?php }?>
<?php }?>
