<?php
	include_once("_common.php");

	$sql = " select * from a_stadium where as_idx = '{$v}' ";
	$row = sql_fetch($sql);

	echo json_encode(array('v1'=>$row['as_name'], 'v2'=>$row['as_size'], 'v3'=>$row['as_to'], 'v4'=>$row['as_sort'], 'v5'=>$row['as_floor'], 'v6'=>$row['as_price'], 'v7'=>$row['as_price2']));
?>