<?php
	include_once("_common.php");

	$sql = " delete from {$tb} where 1=1 and {$col} = '{$id}' ";
	sql_query($sql);
?>