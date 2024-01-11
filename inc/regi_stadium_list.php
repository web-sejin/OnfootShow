<?php
	include_once("_common.php");
	
	$to = "";
	for($i=0; $i<count($st_to); $i++){
		if($i != 0){ $to .= "|";  }
		$to .= $st_to[$i];
	}
?>
<li class="stadium_li">
	<input type="hidden" name="rg_st_name[]" readonly value="<?php echo $st_name?>">
	<input type="hidden" name="rg_st_size[]" readonly value="<?php echo $st_size?>">
	<input type="hidden" name="rg_st_to[]" readonly value="<?php echo $to?>">
	<input type="hidden" name="rg_st_sort[]" readonly value="<?php echo $st_sort?>">
	<input type="hidden" name="rg_st_floor[]" readonly value="<?php echo $st_floor?>">
	<input type="hidden" name="st_price[]" readonly value="<?php echo $st_price?>">
	<input type="hidden" name="st_price2[]" readonly value="<?php echo $st_price2?>">
	<span><?php echo $st_name?></span>
	<button type="button" onClick="stadiumDel();">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
	</button>
</li>