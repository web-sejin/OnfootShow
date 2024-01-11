<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($is_admin){
	goto_url(G5_ADMIN_URL);
}

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$basename=basename($_SERVER["PHP_SELF"]);
include_once(G5_PATH."/head.tit.php");

$mobile_agent = "/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/";

//앱인지 체크
if($app_chk == "1"){ $_SESSION['appChk'] = $app_chk; }
if($app_token){  $_SESSION["appToken"] = $app_token; }

$http_host = $_SERVER['HTTP_HOST'];
$request_uri = $_SERVER['REQUEST_URI'];
$query_string=getenv("QUERY_STRING");
$mainAppUrl = 'https://'. $http_host . $request_uri.$query_string;

if($is_member){
	// 회원아이디 세션 생성
	set_session('ss_mb_id', $member['mb_id']);
	// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
	set_session('ss_mb_key', md5($member['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']));

	//앱 회원 자동로그인 체크
	if($_SESSION["appChk"] == "1" && $_SESSION["appToken"]){ 
		if($member['mb_type'] == 1 || ($member['mb_type'] != 1 && $member['mb_login_auto'] == 1)){
			fnAutoLogin($member['mb_no'], $_SESSION["appToken"]); 
		}
	}

	//현재 로그인한 회원의 토큰값이 다른 경우 업데이트
	if($_SESSION["appChk"] == "1" && $_SESSION["appToken"] && $member['mb_token'] != $_SESSION["appToken"]){
		fnTokenUpdate($member['mb_no'], $_SESSION["appToken"]);
	}

}else{
	if($app_chk && $app_token){
		$sql = " select count(*) cnt from g5_member where mb_token = '{$app_token}' and mb_leave_status = 1 and mb_login_auto = '1' ";		
		$row = sql_fetch($sql);
		$cnt = $row['cnt'];
		
		if($cnt > 0 && !$is_member){
			$sql = " select * from g5_member where mb_token = '{$app_token}' ";
			$mb = sql_fetch($sql);
			// 회원아이디 세션 생성
			set_session('ss_mb_id', $mb['mb_id']);
			// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
			set_session('ss_mb_key', md5($mb['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']));
			$key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['SERVER_SOFTWARE'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
			if($mb['mb_type'] == 1){
				goto_url(G5_URL."/user/");
			}else if($mb['mb_type'] == 2){
				goto_url(G5_URL);
			}
		}else{
			
		}
	}
}

$app_chk = 1;
//$app_type = 1; //1:일반 , 2:구장관리자
if($is_member){
	$app_type = $member['mb_type'];
}
if($app_type == 1){
	if($is_member){
		
	}else{
		if($nav1 != "yetMember"){			
			goto_url(G5_URL."/user/member/login.php");
		}
	}	
}else{
	if(!$is_member && $nav1 != "yetMember"){
		goto_url(G5_BBS_URL."/login.php");
	}
}
?>
<link rel="stylesheet" href="<?php echo G5_CSS_URL?>/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" />
<link rel="stylesheet" href="<?=G5_CSS_URL?>/noto-sans.css" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

<link rel="stylesheet" href="<?=G5_CSS_URL?>/swiper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js"></script>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $cfg['kakao_key']?>&libraries=services"></script>

<?php if($basename != "login.php" && $headOn != "off"){?>
<header class="header">
	<div class="inner">
		<?php if($member['mb_type'] == "1"){?>
			<?php if($basename == "index.php" || $headType == "main" || $headType == "main2"){?>
				<div class="user_hd_box">
					<button type="button" class="user_hd_tab <?php echo $headType?>" <?php if($headType == "main"){?>onClick="cmPopOn('user_hd_pop');"<?php }?>>
						<?php echo $subTit?>
					</button>					
				</div>
			<?php }else{?>
				<?php if($hdBtnOn != "off"){?>
				<button type="button" class="back_btn" onClick="goback();">
					<?php if($hdBtnType == "close"){?>
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt="">
					<?php }else{?>
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_back.svg" alt="">
					<?php }?>
				</button>
				<?php }?>
				<p class="sub_title"><?php echo $subTit?></p>
			<?php }?>

			<?php if($basename == "index.php" || $alimOn == "on"){?>
				<?php if($basename != "user_match_list.php"){?>
				<button type="button" class="hd_sch" onClick="hdSchOn();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_sch2.svg" alt=""></button>
				<?php }?>
				<a href="<?php echo G5_URL?>/user/user_alim.php" class="hd_alim ver2"><img src="<?php echo G5_THEME_IMG_URL?>/ic_alim.svg" alt=""></a>
			<?php }?>
		
		<?php }else if($member['mb_type'] == "2"){?>
			<?php if($basename == "index.php" || $headType == "main"){?>
				<?php if($basename == "reserve_list.php" || $basename == "match_res_list.php"){?>
					<p class="sub_title ver2"><?php echo $subTit?></p>
				<?php }else{?>
					<ul class="main_gnb">
						<li <?php if($basename == "index.php"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>">전체 스케줄표</a></li>
						<li <?php if($basename == "schedule.php"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/schedule.php">구장별 스케줄표</a></li>
					</ul>
				<?php }?>
				<a href="<?php echo G5_URL?>/alim.php" class="hd_alim"><img src="<?php echo G5_THEME_IMG_URL?>/ic_alim.svg" alt=""></a>
				<button type="button" class="hd_ham" onClick="cmPopOn('ham_menu');"><img src="<?php echo G5_THEME_IMG_URL?>/ic_hamburger.svg" alt=""></button>
			<?php }else{?>
				<?php if($hdBtnOn != "off"){?>
				<button type="button" class="back_btn" onClick="goback();">
					<?php if($hdBtnType == "close"){?>
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt="">
					<?php }else{?>
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_back.svg" alt="">
					<?php }?>
				</button>
				<?php }?>
				<p class="sub_title"><?php echo $subTit?></p>
			<?php }?>
		<?php }else if(!$is_member){?>
			<?php if($hdBtnOn != "off"){?>
			<button type="button" class="back_btn" onClick="goback();">
				<?php if($hdBtnType == "close"){?>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt="">
				<?php }else{?>
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_back.svg" alt="">
				<?php }?>
			</button>
			<?php }?>
			<p class="sub_title"><?php echo $subTit?></p>
		<?php }?>
	</div>	
</header>

<div class="hd_sch_box">
	<div class="hd_sch_flex">
		<button type="button" class="back_btn ver2" onClick="hdSchOff();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
		<div class="people_sch_box ver2">
			<?php
				if($basename == "index.php"){
					$placeholder = "팀명 또는 풋살장 입력";
				}else if($basename == "stadium_list.php"){
					$placeholder = "구장명 입력";
				}
			?>
			<input type="text" id="sch_val" placeholder="<?php echo $placeholder?>">
			<button type="button" onClick="searchList();">
				<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
			</button>
		</div>
	</div>
</div>

<?php if($headType == "main"){?>
<div class="user_hd_pop" id="user_hd_pop">
	<p class="cm_pop_back" onClick="cmPopOff('user_hd_pop');"></p>
	<ul class="user_hd_menu">
		<?php if($basename == "league_list.php"){?>
		<li <?php if($cate == "1"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/user/board/league_list.php?cate=1">리그정보</a></li>
		<li <?php if($cate == "2"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/user/board/league_list.php?cate=2">리그진행/결과</a></li>
		<li <?php if($cate == "3"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/user/board/league_list.php?cate=3">리그 게시판</a></li>
		<?php }else if($basename == "index.php" || $basename == "user_match_list.php"){?>
		<li <?php if($basename == "index.php"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/user/">매치</a></li>
		<li <?php if($basename == "user_match_list.php"){?>class="on"<?php }?>><a href="<?php echo G5_URL?>/user/user_match_list.php">매치 내역</a></li>
		<?php }?>
	</ul>	
</div>
<?php }?>

<?php }?>

<div class="ham_menu" id="ham_menu">
	<p class="ham_menu_back" onClick="cmPopOff('ham_menu');"></p>
	<div class="ham_menu_cont">
		<button type="button" class="ham_close" onClick="cmPopOff('ham_menu');">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt="">
		</button>
		<div class="ham_gnb_box">
			<div class="ham_gnb_cont">
				<p class="ham_gnb_tit">스케줄표</p>
				<ul class="ham_gnb_ul">
					<li><a href="<?php echo G5_URL?>">스케줄표</a></li>
					<li><a href="<?php echo G5_URL?>/reserve_list.php">예약내역</a></li>
					<li><a href="<?php echo G5_URL?>/match_res_list.php">매치확정</a></li>
				</ul>
			</div>
			<div class="ham_gnb_cont">
				<ul class="ham_gnb_ul">
					<li><a href="<?php echo G5_URL?>/register.php?w=u">마이페이지</a></li>
				</ul>
			</div>
			<div class="ham_gnb_cont">
				<ul class="ham_gnb_ul">
					<li><a href="<?php echo G5_BBS_URL?>/logout.php">로그아웃</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
function hdSchOn(){
	$(".hd_sch_box").show();
}
function hdSchOff(){
	$(".hd_sch_box").hide();
	$("#sch_val").val("");
}
</script>

<?php if($basename != "index.php"){?>
<div id="sub_div">
	<div class="inner <?php echo $innerClass?>">
<?php }?>