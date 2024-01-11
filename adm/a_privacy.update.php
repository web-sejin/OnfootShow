<?php
	include_once("_common.php");

	$sql = " update a_privacy set
					ap_provision = '{$ap_provision}'
					, ap_privacy = '{$ap_privacy}'
					where 1
					";
	sql_query($sql);

	alert("수정되었습니다.");
?>