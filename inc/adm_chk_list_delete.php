<?
	include_once("_common.php");

	/*
		delete_type = 1 : 완전삭제
		delete_type = 2 : db에서 st_tp 값 0으로 변경
	*/	
	
	if($delete_type == "1"){		
		for($i=0; $i<count($chk_id); $i++){
			if($chk_id[$i] != ""){
				$sql = " delete from {$table_name} where {$prim} = '{$chk_id[$i]}' ";
				sql_query($sql);
			}
		}
	}else{
		for($i=0; $i<count($chk_id); $i++){
			if($chk_id[$i] != ""){
				$sql = " update {$table_name} set {$st_tp} = '0' where {$prim} = '{$chk_id[$i]}' ";
				sql_query($sql);
			}
		}

	}
?>