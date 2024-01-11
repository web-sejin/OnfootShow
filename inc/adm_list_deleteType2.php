<?php
	include_once("_common.php");

	$sql = " update {$tb} set
					{$st_tp} = '0'
					where 1=1 
						and {$col} = '{$id}' 
					";
	sql_query($sql);
?>