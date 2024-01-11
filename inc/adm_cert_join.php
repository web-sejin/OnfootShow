<?php
	include_once("_common.php");

	$sql = " update g5_member set
				mb_cert = 1
				, mb_cert_datetime = now()
				where mb_no = '{$idx}'
				";
	sql_query($sql);
?>