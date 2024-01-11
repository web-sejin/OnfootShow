<?php
	include_once("_common.php");

	$sql = " update a_qna set
				aq_answer = '{$aq_answer}'
				, aq_answer_datetime = now()
				where aq_idx = '{$aq_idx}'
				";
	$row = sql_fetch($sql);

	alert("답변이 등록되었습니다.");
?>