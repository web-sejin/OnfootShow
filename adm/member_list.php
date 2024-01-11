<?php
$sub_menu = "200100";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_point' :
            $sql_search .= " ({$sfl} >= '{$stx}') ";
            break;
        case 'mb_level' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        case 'mb_tel' :
        case 'mb_hp' :
            $sql_search .= " ({$sfl} like '%{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if($s_type != ""){
	if($s_type == "2"){
		$sql_search .= " and mb_type = 0 and membership_use = 1 ";
	}else if($s_type == "3"){
		$sql_search .= " and mb_type = 0 and membership_use = 0 ";
	}else{
		$sql_search .= " and mb_type = '{$s_type}' ";
	}
	$qstr .= "&s_type=".$s_type;
}

if($s_state != ""){
	if($s_state == "1"){
		$sql_search .= " and mb_cert = 1 and mb_leave_status = 1 ";
	}else if($s_state == "2"){
		$sql_search .= " and mb_type = 0 and mb_cert = 1 ";
	}else if($s_state == "3"){
		$sql_search .= " and mb_type = 0 and mb_cert = 0 ";
	}else if($s_state == "4"){
		$sql_search .= " and mb_leave_status = 0 ";
	}
	$qstr .= "&s_state=".$s_state;
}

if ($is_admin != 'super')
    $sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
    $sst = "mb_datetime";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$g5['title'] = '회원관리';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan = 16;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총회원수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>명 </span></span>
	<span class="btn_ov01"><span class="ov_txt">탈퇴 </span><span class="ov_num"> <?php echo number_format($leave_count) ?>명 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
	<div class="filter_box">
		<p class="filter_label">회원 유형</p>
		<div class="filter_list">
			<select name="s_type" id="s_type" class="frm_input frm_input_id">
				<option value="">전체</option>
				<option value="1" <?php echo get_selected($s_type, "1"); ?>>일반회원</option>
				<option value="0" <?php echo get_selected($s_type, "2"); ?>>구장관리자</option>
			</select>
		</div>
	</div>
	<div class="filter_box">
		<p class="filter_label">회원 상태</p>
		<div class="filter_list">
			<select name="s_state" id="s_state" class="frm_input frm_input_id">
				<option value="">전체</option>
				<option value="1" <?php echo get_selected($s_state, "1"); ?>>정상</option>
				<option value="2" <?php echo get_selected($s_state, "2"); ?>>가입승인(환경업체)</option>
				<option value="3" <?php echo get_selected($s_state, "3"); ?>>가입대기(환경업체)</option>
				<option value="4" <?php echo get_selected($s_state, "4"); ?>>탈퇴</option>
			</select>
		</div>
	</div>
	<div class="filter_box">
		<p class="filter_label">검색어</p>
		<div class="filter_list">
			<select name="sfl" id="sfl">
				<option value="mb_id"<?php echo get_selected($sfl, "mb_id"); ?>>아이디</option>
				<option value="mb_company_name"<?php echo get_selected($sfl, "mb_company_name"); ?>>업체명</option>
				<option value="mb_name"<?php echo get_selected($sfl, "mb_name"); ?>>담당자(이름)</option>
				<!--option value="mb_email"<?php echo get_selected($sfl, "mb_tel"); ?>>회사 전화번호</option>
				<option value="mb_email"<?php echo get_selected($sfl, "mb_hp"); ?>>담당자 전화번호</option>
				<option value="mb_email"<?php echo get_selected($sfl, "mb_email"); ?>>이메일</option-->
			</select>
			<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
			<button class="btn_submit"></button>
		</div>
	</div>
</form>

<div class="local_desc01 local_desc">
    <p>
        회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 아이디는 삭제하지 않고 영구 보관합니다.
    </p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
	<colgroup>
		<col width="4%">
		<col width="10%">
		<col width="10%">
		<col width="10%">
		<col width="10%">
		<col width="12%">
		<col width="20%">
		<col width="10%">
		<col width="5%">
		<col width="9%">
	</colgroup>
    <thead>
    <tr>
        <th scope="col" id="mb_list_chk">
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" id="">아이디</th>        
		<th scope="col" id="">회원유형</th>        
		<th scope="col" id="">이름</th>
		<th scope="col" id="">연락처</th>
		<th scope="col" id="">이메일</th>
		<th scope="col" id="">기본 정보</th>
		<th scope="col" id="">가입일</th>
		<th scope="col" id="">상태</th>
        <th scope="col" id="">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // 접근가능한 그룹수
        $sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
        $row2 = sql_fetch($sql2);
        $group = '';
        if ($row2['cnt'])
            $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

        if ($is_admin == 'group') {
            $s_mod = '';
        } else {
            $s_mod = '<a href="./member_form.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'" class="btn btn_03">수정</a>';
        }
        //$s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'" class="btn btn_02">그룹</a>';

        $leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
        $intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

        $mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

        $mb_id = $row['mb_id'];
        $leave_msg = '';
        $intercept_msg = '';
        $intercept_title = '';
        if ($row['mb_leave_date']) {
            $mb_id = $mb_id;
            $leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
        }
        else if ($row['mb_intercept_date']) {
            $mb_id = $mb_id;
            $intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
            $intercept_title = '차단해제';
        }
        if ($intercept_title == '')
            $intercept_title = '차단하기';

        $address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

        $bg = 'bg'.($i%2);

        switch($row['mb_certify']) {
            case 'hp':
                $mb_certify_case = '휴대폰';
                $mb_certify_val = 'hp';
                break;
            case 'ipin':
                $mb_certify_case = '아이핀';
                $mb_certify_val = '';
                break;
            case 'admin':
                $mb_certify_case = '관리자';
                $mb_certify_val = 'admin';
                break;
            default:
                $mb_certify_case = '&nbsp;';
                $mb_certify_val = 'admin';
                break;
        }
    ?>

    <tr class="<?php echo $bg; ?>">
        <td headers="mb_list_chk" class="td_chk">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo get_text($row['mb_nick']); ?>님</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td headers="mb_list_id" class="">
            <?php echo $mb_id ?>			
            <?php
            //소셜계정이 있다면
            if(function_exists('social_login_link_account')){
                if( $my_social_accounts = social_login_link_account($row['mb_id'], false, 'get_data') ){
                    
                    echo '<div class="member_social_provider sns-wrap-over sns-wrap-32">';
                    foreach( (array) $my_social_accounts as $account){     //반복문
                        if( empty($account) || empty($account['provider']) ) continue;
                        
                        $provider = strtolower($account['provider']);
                        $provider_name = social_get_provider_service_name($provider);
                        
                        echo '<span class="sns-icon sns-'.$provider.'" title="'.$provider_name.'">';
                        echo '<span class="ico"></span>';
                        echo '<span class="txt">'.$provider_name.'</span>';
                        echo '</span>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </td>
		<td>
			<?php
				if($row['mb_type'] == 1){
					echo "일반회원";
				}else if($row['mb_type'] == 2){
					echo "구단관리자";
				}else if($row['mb_type'] == 10){
					echo "최고관리자";
				}
			?>
		</td>
        <td class=""><?php echo $row['mb_name'] ?></td>		
		<td class=""><?php echo $row['mb_hp'] ?></td>		
		<td class=""><?php echo $row['mb_email'] ?></td>		
		<td class="td_left">
			<?php if($row['mb_type'] == 1){?>
				<?php echo getSido($row['sd_idx']);?> <?php echo getSigugun($row['si_idx']);?>
			<?php }else if($row['mb_type'] == 2){?>
				<?php echo $row['mb_fs_name']?><br>				
				<?php echo $row['mb_fs_tel']?><br>
				[<?php echo $row['mb_fs_zip'] ?> <?php echo $row['mb_fs_addr1']?> <?php echo $row['mb_fs_addr2']?><br>
				<a href="<?php echo G5_DATA_URL?>/footsalFile/<?php echo $row['mb_fs_bs_af']?>" download="<?php echo $row['mb_fs_bs_bf']?>" style="color:blue;">[사업자등록증 다운]</a>
			<?php }?>			
		</td>
		</td>
		<td class="td_date"><?php echo substr($row['mb_datetime'],2,8); ?></td>
		<td class="td_mbstat">
            <?php
				if ($leave_msg || $intercept_msg){
					echo $leave_msg.' '.$intercept_msg;
				}else{ 
			?>
				<?php if($row['mb_type'] == 2 && $row['mb_cert'] == 0){?>
					승인대기
					<p class="td_mng"><button type="button" class="btn btn_02" onClick="certJoin('<?php echo $row['mb_no']?>', '<?php echo $row['mb_id']?>')">가입 승인</button></p>
				<?php }else{?>
					정상
				<?php }?>
			<?php	} ?>
        </td>
        <td class="td_mng td_mng_s">
				<?php echo $s_mod ?><?php echo $s_grp ?>

				<?php if($row['mb_type'] == 0 && $row['mb_leave_status'] == 1 && $row['membership_use'] == 1){ ?>
					<?php if($row['hide_st'] == 1){?>
					<button type="button" class="btn btn_01" onClick="useHide('<?php echo $row['mb_no']?>', 'hide')">숨김</button>
					<?php }else{?>
					<button type="button" class="btn btn_02" onClick="useHide('<?php echo $row['mb_no']?>', 'open')">노출</button>
					<?php }?>
				<?php }?>

				<?php if($row['mb_type'] == 0 && $row['mb_leave_status'] == 1 && $row['membership_use'] == 0){ ?>
					<?php if($row['uncon_st'] == 1){?>
					<button type="button" class="btn btn_01" onClick="useUncon('<?php echo $row['mb_no']?>', 'hide')">숨김</button>
					<?php }else{?>
					<button type="button" class="btn btn_02" onClick="useUncon('<?php echo $row['mb_no']?>', 'open')">노출</button>
					<?php }?>
				<?php }?>
		</td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
function certJoin(idx, id){
	if(confirm(`${id}님의 가입을 승인하시겠습니까?`) !== false)	{
		$.ajax({
			type: "POST",
			url: "/inc/adm_cert_join.php",
			data: {idx:idx}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				alert("가입 승인이 정상적으로 처리되었습니다.");
				location.reload();
			}
		});
	}
}

function fmemberlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

function useHide(idx, v){
	let message = "";
	if(v == "hide"){
		message = "이용권을 구매한 회원의 숨김처리를 진행하시겠습니까?";
	}else if(v == "open"){
		message = "이용권을 구매한 회원의 노출처리를 진행하시겠습니까?";
	}
	
	if(confirm(message) == true){
		$.ajax({
			type: "POST",
			url: "/inc/member_hide.php",
			data: {idx:idx, v:v}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				location.reload();
			}
		});
	}
}

function useUncon(idx, v){
	let message = "";
	if(v == "hide"){
		message = "이용권을 구매하지 않은 회원의 숨김처리를 진행하시겠습니까?";
	}else if(v == "open"){
		message = "이용권을 구매하지 않은 회원의 노출처리를 진행하시겠습니까?";
	}
	
	if(confirm(message) == true){
		$.ajax({
			type: "POST",
			url: "/inc/member_uncon.php",
			data: {idx:idx, v:v}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				location.reload();
			}
		});
	}
}
</script>

<?php
include_once ('./admin.tail.php');