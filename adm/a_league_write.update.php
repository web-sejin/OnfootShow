<?php
	include_once("_common.php");

	@mkdir(G5_DATA_PATH.'/leagueFile', G5_DIR_PERMISSION);
	$uploads_dir = G5_DATA_PATH.'/leagueFile/';
	$allowed_ext = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF','svg');

	$file_query = "";

	if($_FILES['al_file']['name']){
		// 변수 정리
		$error = $_FILES['al_file']['error'];
		$bf = $_FILES['al_file']['name'];
		$ext = array_pop(explode('.', $bf));

		// 확장자 확인
		if( !in_array(strtolower($ext), $allowed_ext) ) {
			alert("허용되지 않는 확장자입니다.");
		}

		$af = "league_".date("YmdHisB").".".strtolower($ext);
		move_uploaded_file( $_FILES['al_file']['tmp_name'], "$uploads_dir$af");
		$file_query .= " , al_file_bf = '{$bf}' , al_file_af= '{$af}' ";
	}

	if(!$w || $w == ""){
		$sql = " insert into a_league set
						al_cate = '{$al_cate}'
						, al_start = '{$al_start}'
						, al_end = '{$al_end}'
						, al_subject = '{$al_subject}'
						, al_content = '{$al_content}'
						, al_datetime = now()
						{$file_query}
						";
		sql_query($sql);
		alert("글이 작성되었습니다.", "./a_league_list.php");

	}else if($w == "u"){

		$sql = " update a_league set
						al_cate = '{$al_cate}'
						, al_start = '{$al_start}'
						, al_end = '{$al_end}'
						, al_subject = '{$al_subject}'
						, al_content = '{$al_content}'
						, al_updatetime = now()
						{$file_query}
						where an_idx = '{$al_idx}'
						";
		sql_query($sql);
		alert("수정되었습니다.");
	}
?>