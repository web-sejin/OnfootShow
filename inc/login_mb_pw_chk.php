<?php
	include_once("_common.php");

	$mb = get_member($id);
	if(login_password_check($mb, $pw, $mb['mb_password'])){
		echo "1111";
	}else{
		echo "0000";
	}
?>