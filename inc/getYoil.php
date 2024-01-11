<?php
	include_once("_common.php");

	echo date("Y. m. d", strtotime($dateText))." (".getYoil($dateText).")";
?>