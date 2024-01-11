<?php
include_once('./_common.php');

$sub_menu = "300910";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "리그 관리";
include_once('./admin.head.php');

$tb = "a_league";

$add_query = "";
if($s_txt != ""){ 
	$add_query .= " and al_subject like '%{$s_txt}%' "; 
	$qstr .= "&s_txt=".$s_txt;
}
//if($s_start != ""){ $add_query .= " and at_start_date >= '{$s_start}' "; }
//if($s_end != ""){ $add_query .= " and at_end_date <= '{$s_end}' "; }

$sql_common = " from {$tb} ";
$sql_search = " where 1 {$add_query} ";
$sql_order = " order by al_datetime desc ";

$sql = " select count(distinct al_idx) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 5;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함'


$limit = " limit {$from_record}, {$rows} ";

$sql = "select * {$sql_common} {$sql_search} {$sql_order} {$limit} ";
$result = sql_query($sql);

$colspan = 15;
$now = date("Y-m-d H:i");
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!--div class="local_ov01 local_ov">
	<span class="btn_ov01">
		<span class="ov_txt">전체 </span>
		<span class="ov_num">  <?php echo $total_count; ?>건</span>
	</span>
</div-->

<section id="">
	<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
		<!--div class="filter_box">
			<p class="filter_label">진행일</p>
			<div class="filter_list">
				<input type="text" name="s_start" id="s_start" class="frm_input frm_input150" value="<?php echo $s_start?>" readonly>
				<span class="date_line_bar">~</span>
				<input type="text" name="s_end" id="s_end" class="frm_input frm_input150" value="<?php echo $s_end?>" readonly>
			</div>
		</div-->
		<div class="filter_box">
			<p class="filter_label">검색어</p>
			<div class="filter_list">
				<input type="text" name="s_txt" value="<?php echo $s_txt ?>" id="s_txt" class="frm_input" placeholder="제목을 입력해주세요.">
				<button type="button" class="btn_submit" onClick="fnSearch();"></button>
			</div>
		</div>
	</form>

	<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="table_name" value="<?php echo $tb?>">
		<input type="hidden" name="prim" value="al_idx">
		<input type="hidden" name="delete_type" value="1">

		<div class="tbl_head01 tbl_wrap">
			<table>
			<caption><?php echo $g5['title']; ?> 목록</caption>
			<colgroup>
				<col width="5%">
				<col width="8%">
				<col width="15%">
				<col width="37%">
				<col width="20%">
				<col width="10%">
			</colgroup>
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" id="all_checkbox"></th>
				<th scope="col">NO</th>
				<th scope="col">분류</th>
				<th scope="col">제목</th>
				<th scope="col">등록일</th>
				<th scope="col">관리</th>
			</tr>
			</thead>
			<tbody id="t_body" class="ui-sortable">
			<?php
			for ($i=0; $row=sql_fetch_array($result); $i++) {
				$one_update = '<a href="./board_form.php?w=u&amp;bo_table='.$row['bo_table'].'&amp;'.$qstr.'" class="btn btn_03">수정</a>';
				$one_copy = '<a href="./board_copy.php?bo_table='.$row['bo_table'].'" class="board_copy btn btn_02" target="win_board_copy">복사</a>';
				
				$bg = 'bg'.($i%2);
			?>

			<tr class="<?php echo $bg; ?>">
				<td><input type="checkbox" name="chk_id[]" value="<?php echo $row['al_idx']?>"></td>
				<td><?php echo ($page-1)*$rows+$i+1?></td>
				<td>
					<?php 
						if($row['al_cate'] == 1){
							echo "리그정보";
						}else if($row['al_cate'] == 2){
							echo "리그진행/결과";
						}else if($row['al_cate'] == 3){
							echo "리그 게시판";
						}
					?>
				</td>				
				<td>
						<?php 
							$subject = $row['al_subject'];
							if($row['al_cate'] != 3){
								$subject .= " (".date("Y. m. d", strtotime($row['al_start']))." ~";
								if($row['al_end']){
									$subject .= " ".date("Y. m. d", strtotime($row['al_end']));
								}
							}

							$subject .= ")";

							echo $subject;
						?>
				</td>				
				<td><?php echo $row['al_datetime']?></td>				
				<td class="td_mng td_mng_m">
					<a href="a_league_write.php?w=u&al_idx=<?php echo $row['al_idx']?>&<?php echo $qstr?>" class="btn btn_03">수정</a>
					<button type="button" class="btn btn_02" onClick="fnDeleteType('<?php echo $tb?>', 'al_idx', '<?php echo $row['al_idx']?>');">삭제</button>
				</td>
			</tr>
			<?php
			}
			if ($i == 0)
			echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
			?>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<?php if ($is_admin == 'super') { ?>
			<button type="button" onClick="fnDelete();" class="btn_02 btn">선택삭제</button>
			<a href="./a_league_write.php" id="bo_add" class="btn_01 btn">등록</a>
			<?php } ?>
		</div>

	</form>
</section>

<?php	
	echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');
?>

<script>
$("#all_checkbox").change(function(){
	if($(this).is(":checked") == true){
		$("input:checkbox[name='chk_id[]']").prop("checked", true);
	}else{
		$("input:checkbox[name='chk_id[]']").prop("checked", false);
	}
});

function fnDelete(){
	var idx = $("input:checkbox[name='chk_id[]']");
	if(idx.is(":checked") == false){
		alert("삭제할 게시물을 하나 이상 선택해주세요.");
		return false;
	}

	if(confirm("삭제하시겠습니까?") == true){
		var string = $("form[name=fboardlist").serialize();

		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/adm_chk_list_delete.php",
			data: string,
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				alert("삭제되었습니다.");
				location.reload();
			}
		});
		return false;
	}
}

function fnDeleteType(a, b, c){
	if(confirm("해당 게시물을 삭제하시겠습니까?") === true){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/adm_list_deleteType1.php",
			data: {tb:a, col:b, id:c},
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				alert("삭제되었습니다.");
				location.reload();
			}
		});
		return false;
	}
}
</script>


<?php
include_once('./admin.tail.php');
?>
