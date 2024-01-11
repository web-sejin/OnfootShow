<?php
	include_once("_common.php");

	$val = "";
	$sido = "";
	$sigugun = "";

	$sql = " select * from rb_sido where sd_idx = '{$sd_idx}' ";
	$row = sql_fetch($sql);
	$sido = $row['sd_name'];

	if($si_idx != "" && $si_idx != "all"){
		$val = $sd_idx."|".$si_idx;

		$sql = " select * from rb_sigungu where si_idx = '{$si_idx}' ";
		$row = sql_fetch($sql);
		$sigugun = $row['si_name'];
	}else{
		$val = $sd_idx;
	}

	if($si_idx == "all"){
		$sigugun = "전체";
	}
?>

<li id="local_li_<?php echo $len?>">
	<input type="hidden" name="local[]" value="<?php echo $val?>">
	<span><?php echo $sido?> <?php echo $sigugun?></span>
	<button type="button" onClick="deleteLocal('<?php echo $len?>');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
	</button>
</li>