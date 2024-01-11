<?php
	include_once("_common.php");

	$sql = " update g5_member set
					{$col} = NULL
					where mb_no = '{$member['mb_no']}'
					";
	sql_query($sql);
?>