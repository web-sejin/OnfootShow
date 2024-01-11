<?php
	switch($basename){
		case "login.php":
			$nav1 = "yetMember";
		break;

		case "register.php":
			$nav1 = "yetMember";
			$subTit = "회원가입";
			if($w == "u"){
				$subTit = "마이페이지";
			}
		break;

		case "find_id.php":
			$nav1 = "yetMember";
			$subTit = "아이디 찾기";
		break;

		case "find_id_result.php":
			$nav1 = "yetMember";			
			$subTit = "아이디 찾기";
			$hdBtnType = "close";
		break;

		case "find_pw.php":
			$nav1 = "yetMember";
			$subTit = "비밀번호 찾기";
		break;

		case "schedule_write.php":
			$subTit = "경기등록";
		break;

		case "alim.php":
		case "user_alim.php":
			$subTit = "알림";
			$hdBtnType = "close";
		break;

		case "schedule.php":
			$headType = "main";
		break;

		case "reserve_list.php":
			$subTit = "예약 신청 내역";
			$headType = "main";
		break;

		case "match_res_list.php":
			$subTit = "매치 신청 내역";
			$headType = "main";
		break;

		case "user_privacy_chk.php":
			$nav1 = "yetMember";
			$subTit = "";
		break;

		case "register_member.php":
			$nav1 = "yetMember";
			$subTit = "회원가입";
		break;

		case "user_register.php":
			$nav1 = "yetMember";
			$subTit = "회원가입";
			if($w == "u"){
				$subTit = "내 정보 수정";
			}
		break;

		case "stadium_list.php":
			$subTit = "구장검색";
			$headType = "main2";
			$alimOn = "on";
			$tailOn = "on";
			$hdBtnOn = "off";
			$innerClass = "inner0";
		break;

		case "stadium_view.php":
			$headOn = "off";
			$innerClass = "inner0";
		break;

		case "stadium_res.php":
			$subTit = "시간 선택";
			$innerClass = "inner0";
		break;

		case "stadium_res_step2.php":
			$subTit = "구장 예약";
		break;

		case "notice_list.php":
			$subTit = "온풋소식";
			$alimOn = "on";
			$tailOn = "on";
			$hdBtnOn = "off";
		break;

		case "notice_view.php":
			$subTit = "온풋소식";
		break;

		case "league_list.php":
			if($cate == "1"){
				$subTit = "리그정보";
			}else if($cate == "2"){
				$subTit = "리그진행/결과";
			}else if($cate == "3"){
				$subTit = "리그 게시판";
			}
			$headType = "main";
			$alimOn = "on";
			$tailOn = "on";
			$hdBtnOn = "off";
		break;

		case "league_view.php":
			if($cate == "1"){
				$subTit = "리그정보";
			}else if($cate == "2"){
				$subTit = "리그진행/결과";
			}else if($cate == "3"){
				$subTit = "리그 게시판";
			}
		break;

		case "mypage.php":
			$subTit = "마이페이지";
			$alimOn = "on";
			$tailOn = "on";
			$hdBtnOn = "off";
		break;

		case "qna_list.php":
		case "qna_write.php":
		case "qna_view.php":
			$subTit = "1:1 문의";
		break;

		case "privacy.php":
			$subTit = "개인정보처리방침";
		break;

		case "provision.php":
			$subTit = "이용약관";
		break;

		case "user_match_score.php":
			$subTit = "내 매치 전적";
		break;

		case "user_res_list.php":
			$subTit = "구장예약 내역";			
		break;

		case "user_res_detail.php":
			$subTit = "구장예약 내역";
			$innerClass = "inner0";
		break;

		case "index.php":
			$subTit = "매치";
			$headType = "main";
		break;

		case "user_match_list.php":
			$subTit = "매치 내역";
			$headType = "main";
			$alimOn = "on";
			$tailOn = "on";
			$hdBtnOn = "off";
			$innerClass = "inner0";
		break;

		case "match_view.php":
			$subTit = "매치 정보";
		break;

		case "match_write.php":
			$subTit = "매치 등록";
			if($w == "u"){
				$subTit = "매치 수정";
			}
		break;

		case "rematch_write.php":
			$subTit = "리매치 신청";
			if($w == "u"){
				$subTit = "리매치 수정";
			}
		break;

		case "match_stadium_list.php":
			$subTit = "구장 선택";
			$innerClass = "inner0";
		break;

		case "stadium_res2.php":
			$subTit = "구장 선택";
			$innerClass = "inner0";
		break;

		case "match_reivew.php":
			$subTit = "구장 선택";
		break;
	}
?>