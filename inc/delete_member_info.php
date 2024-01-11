<?php
	include_once("_common.php");

	if($type == "stadium"){
		$sql = " update a_stadium set
						as_delete_st = 0
						where 1	
							and as_idx = '{$idx}'
							and mb_idx = '{$member['mb_no']}'
						";
		sql_query($sql);

	}else if($type == "thumbnail"){
		$sql = " delete from a_futsal_img where img_idx = '{$idx}' and mb_idx = '{$member['mb_no']}'  ";
		sql_query($sql);
	}
?>