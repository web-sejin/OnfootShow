<?php
	include_once("_common.php");

	$app_chk = $_POST['app_chk'];
	$mb_token = $_POST['mb_token'];
	$mb_fs_lat = $_POST['mb_fs_lat'];
	$mb_fs_lng = $_POST['mb_fs_lng'];	
	$mb_id = $_POST['mb_id'];
	$mb_password = $_POST["mb_password"];		
	$mb_name = $_POST['mb_name'];
	$mb_hp = $_POST['mb_hp'];
	$mb_email = $_POST['mb_email'];
	$mb_fs_name = $_POST['mb_fs_name'];
	$mb_fs_tel = $_POST['mb_fs_tel'];
	$mb_fs_zip = $_POST['mb_fs_zip'];
	$mb_fs_addr1 = $_POST['mb_fs_addr1'];
	$mb_fs_addr2 = $_POST['mb_fs_addr2'];
	$mb_fs_addr3 = $_POST['mb_fs_addr3'];
	$mb_fs_use1 = $_POST['mb_fs_use1'];
	$mb_fs_use2 = $_POST['mb_fs_use2'];
	$mb_fs_use3 = $_POST['mb_fs_use3'];
	$mb_fs_start = $_POST['mb_fs_start'];
	$mb_fs_end = $_POST['mb_fs_end'];
	$mb_fs_content = $_POST['mb_fs_content'];
	$mb_fs_refund = $_POST['mb_fs_refund'];

	$mb_fs_bs = $_FILES["mb_fs_bs"];
	$pic_img_1 = $_FILES["pic_img_1"];
	$pic_img_2 = $_FILES["pic_img_2"];
	$pic_img_3 = $_FILES["pic_img_3"];
	$pic_img_4 = $_FILES["pic_img_4"];
	$pic_img_5 = $_FILES["pic_img_5"];

	$add_insert = "";
	if($app_chk == "1"){
		$add_insert .= " , mb_token = '{$mb_token}' ";
	}

	$mb_id = preg_replace('/\s+/', '', $mb_id);

	@mkdir(G5_DATA_PATH.'/footsalFile', G5_DIR_PERMISSION);
	$uploads_dir = G5_DATA_PATH.'/footsalFile/';
	$allowed_ext = array('jpg','jpeg','png','gif','svg','pdf');

	$file_query = "";

	if($_FILES['mb_fs_bs']['name']){
		// 변수 정리
		$error = $_FILES['mb_fs_bs']['error'];
		$beforeThum = $_FILES['mb_fs_bs']['name'];
		$ext = array_pop(explode('.', $beforeThum));

		$afterThum = "business_".date("YmdHisB").".".strtolower($ext);
		move_uploaded_file( $_FILES['mb_fs_bs']['tmp_name'], "$uploads_dir$afterThum");
		$file_query .= " , mb_fs_bs_bf = '{$beforeThum}' , mb_fs_bs_af = '{$afterThum}' ";
	}	
	
	if(!$w || $w == ""){
		$sql = " insert into g5_member set 
						mb_id = '{$mb_id}'
						, mb_password = '".get_encrypt_string($mb_password)."'
						, mb_name = '{$mb_name}'
						, mb_nick = '{$mb_name}'
						, mb_hp = '{$mb_hp}'
						, mb_email = '{$mb_email}'
						, mb_today_login = '".G5_TIME_YMDHIS."'
						, mb_datetime = '".G5_TIME_YMDHIS."'
						, mb_level = '{$config['cf_register_level']}'
						, mb_ip = '{$_SERVER['REMOTE_ADDR']}'
						, mb_login_ip = '{$_SERVER['REMOTE_ADDR']}'
						, mb_open_date = '".G5_TIME_YMD."'
						, mb_fs_lat = '{$mb_fs_lat}'
						, mb_fs_lng = '{$mb_fs_lng}'
						, sd_idx = '{$sd_idx}'
						, si_idx = '{$si_idx}'
						, do_idx = '{$do_idx}'
						, mb_fs_name = '{$mb_fs_name}'
						, mb_fs_tel = '{$mb_fs_tel}'
						, mb_fs_zip = '{$mb_fs_zip}'
						, mb_fs_addr1 = '{$mb_fs_addr1}'
						, mb_fs_addr2 = '{$mb_fs_addr2}'
						, mb_fs_addr3 = '{$mb_fs_addr3}'
						, mb_fs_use1 = '{$mb_fs_use1}'
						, mb_fs_use2 = '{$mb_fs_use2}'
						, mb_fs_use3 = '{$mb_fs_use3}'
						, mb_fs_start = '{$mb_fs_start}'
						, mb_fs_end = '{$mb_fs_end}'
						, mb_fs_content = '{$mb_fs_content}'
						, mb_fs_refund = '{$mb_fs_refund}'
						, mb_type = 2
						, mb_cert = 0
						{$add_insert}
						{$file_query}
						";
		sql_query($sql);
		$mb_idx = sql_insert_id();

	}else if($w == "u") {
		$mb_idx = $member['mb_no'];
		$sql_password = "";
		if ($mb_password){
			$sql_password = " , mb_password = '".get_encrypt_string($mb_password)."' ";
		}
		$sql = " update g5_member set 
						mb_name = '{$mb_name}'
						, mb_nick = '{$mb_name}'
						, mb_hp = '{$mb_hp}'
						, mb_email = '{$mb_email}'
						, mb_fs_lat = '{$mb_fs_lat}'
						, mb_fs_lng = '{$mb_fs_lng}'
						, sd_idx = '{$sd_idx}'
						, si_idx = '{$si_idx}'
						, do_idx = '{$do_idx}'
						, mb_fs_name = '{$mb_fs_name}'
						, mb_fs_tel = '{$mb_fs_tel}'
						, mb_fs_zip = '{$mb_fs_zip}'
						, mb_fs_addr1 = '{$mb_fs_addr1}'
						, mb_fs_addr2 = '{$mb_fs_addr2}'
						, mb_fs_addr3 = '{$mb_fs_addr3}'
						, mb_fs_use1 = '{$mb_fs_use1}'
						, mb_fs_use2 = '{$mb_fs_use2}'
						, mb_fs_use3 = '{$mb_fs_use3}'
						, mb_fs_start = '{$mb_fs_start}'
						, mb_fs_end = '{$mb_fs_end}'
						, mb_fs_content = '{$mb_fs_content}'
						, mb_fs_refund = '{$mb_fs_refund}'
						{$add_insert}
						{$file_query}
						{$sql_password}
						where mb_no = '{$member['mb_no']}'
						";
		sql_query($sql);
	}

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
			$sql = " delete from a_futsal_img where mb_idx = '{$mb_idx}' and img_od = '{$i}' ";
			sql_query($sql);

			$sql = " insert into a_futsal_img set
						mb_idx = '{$mb_idx}'
						, img_bf = '{$beforeThum}'
						, img_af = '{$afterThum}'
						, img_od = {$i}
						";
			sql_query($sql);
		}
	}

	//운영시간
	/*
	for($i=$mb_fs_start; $i<=$mb_fs_end; $i++){
		$sql = " insert into a_futsal_runtime set
						mb_idx = '{$mb_idx}'
						, time_val = '{$i}'
						";
		sql_query($sql);
	}
	*/

	//구장 생성
	for($i=0; $i<count($rg_st_name); $i++){
		$price1 = str_replace(',','', $st_price[$i]);
		$price2 = str_replace(',','', $st_price2[$i]);

		$sql = " insert into a_stadium set
						mb_idx = '{$mb_idx}'
						, as_name = '{$rg_st_name[$i]}'
						, as_size = '{$rg_st_size[$i]}'
						, as_to = '{$rg_st_to[$i]}'
						, as_sort = '{$rg_st_sort[$i]}'
						, as_floor = '{$rg_st_floor[$i]}'
						, as_price = '{$price1}'
						, as_price2 = '{$price2}'
						, as_datetime = now()
						";
		sql_query($sql);
	}

	$sql = " select * from a_stadium where mb_idx = '{$mb_idx}' and as_delete_st = 1 order by as_price asc limit 1 ";
	$price = sql_fetch($sql);

	$sql = " update g5_member set
					mb_fs_row_price = '{$price['as_price']}'
					where mb_no = '{$mb_idx}'
					";
	sql_query($sql);

	echo "1111";
?>