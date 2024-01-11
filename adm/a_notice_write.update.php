<?php
	include_once("_common.php");

	if(!$w || $w == ""){
		$sql = " insert into a_notice set
						an_subject = '{$an_subject}'
						, an_content = '{$an_content}'
						, an_datetime = now()
						";
		sql_query($sql);
		alert("글이 작성되었습니다.", "./a_notice_list.php");

	}else if($w == "u"){

		$sql = " update a_notice set
						an_subject = '{$an_subject}'
						, an_content = '{$an_content}'
						, an_updatetime = now()
						where an_idx = '{$an_idx}'
						";
		sql_query($sql);
		alert("수정되었습니다.");
	}
?>