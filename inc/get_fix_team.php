<?php
	include_once("_common.php");

	$sql = " select * from a_team_fix where atf_idx = '{$idx}' ";
	$row = sql_fetch($sql);

	echo json_encode(array('name'=>$row['atf_name'], 'hp'=>$row['atf_hp'], 'team'=>$row['atf_team_name']));
?>