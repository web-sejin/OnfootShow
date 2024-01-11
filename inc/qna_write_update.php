<?php
	include_once("_common.php");

	$tb = "a_qna";

	$w = $_POST['w'];
	$aq_idx = $_POST['aq_idx'];
	$aq_subject = $_POST['aq_subject'];
	$aq_question = $_POST['aq_question'];	

	$pic_img_1 = $_FILES["pic_img_1"];
	$pic_img_2 = $_FILES["pic_img_2"];
	$pic_img_3 = $_FILES["pic_img_3"];
	$pic_img_4 = $_FILES["pic_img_4"];
	$pic_img_5 = $_FILES["pic_img_5"];

	if(!$w || $w == ""){
		$sql = " insert into {$tb} set
						mb_idx = '{$member['mb_no']}'
						, aq_subject = '{$aq_subject}'
						, aq_question = '{$aq_question}'
						, aq_datetime = now()
						";
		sql_query($sql);
		$aq_idx = sql_insert_id();

	}else if($w == "u"){

	}

	@mkdir(G5_DATA_PATH.'/qnaFile', G5_DIR_PERMISSION);
	$uploads_dir = G5_DATA_PATH.'/qnaFile/';
	$allowed_ext = array('jpg','jpeg','png','gif','svg','pdf');

	//구장 사진
	for($i=1; $i<=5; $i++){
		if($_FILES['pic_img_'.$i]['name']){
			// 변수 정리
			$error = $_FILES['pic_img_'.$i]['error'];
			$beforeThum = $_FILES['pic_img_'.$i]['name'];
			$ext = array_pop(explode('.', $beforeThum));

			$afterThum = "pic_".$i."_".date("YmdHisB").".".strtolower($ext);
			move_uploaded_file( $_FILES['pic_img_'.$i]['tmp_name'], "$uploads_dir$afterThum");
			
			//$file_query .= " , mb_company_license_thumb_bf = '{$beforeThum}' , mb_company_license_thumb_af = '{$afterThum}' ";		
			$sql = " delete from a_qna_img where aq_idx = '{$aq_idx}' and aqi_od = '{$i}' ";
			sql_query($sql);

			$sql = " insert into a_qna_img set
						aq_idx = '{$aq_idx}'
						, aqi_img_bf = '{$beforeThum}'
						, aqi_img_af = '{$afterThum}'
						, aqi_od = {$i}
						";
			sql_query($sql);
		}
	}

	echo "1111";
?>