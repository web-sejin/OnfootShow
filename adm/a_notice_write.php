<?php
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
$sub_menu = "300900";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "온풋소식 관리";
include_once('./admin.head.php');

$sql = " select * from a_notice where 1=1 and an_idx = '{$an_idx}' ";
$row = sql_fetch($sql);

if($s_txt != ""){ $qstr .= "&s_txt=".$s_txt; }
?>
<section id="anc_bo_basic">
    <?php echo $pg_anchor ?>
	<form method="post" action="./a_notice_write.update.php" enctype="multipart/form-data" onSubmit="return fnsubmit();" autocomplete="off">
	<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
	<input type="hidden" name="an_idx" value="<?php echo $an_idx?>" readonly>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>게시판 기본 설정</caption>
        <colgroup>
            <tbody>
				<tr>
                    <th scope="row">제목</th>
                    <td>
                        <input type="tel" name="an_subject" id="an_subject" class="frm_input frm_input100" value="<?php echo $row['an_subject']; ?>" placeholder="제목을 입력해주세요.">
                    </td>
                </tr>
                <tr>
                    <th scope="row">내용</th>
                    <td>
                        <?php echo editor_html("an_content", get_text(html_purifier($row['an_content']), 0)); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
	
	
	<div class="btn_fixed_top">
		<a href="./a_notice_list.php?<?php echo $qstr?>" id="bo_add" class="btn_03 btn">목록</a>
		<button class="btn btn_01">저장</button>
	</div>

	</form>
</section>

<script>
	function fnsubmit(){
		<?php echo get_editor_js("an_content"); ?>
	}
</script>

<?php
include_once('./admin.tail.php');
?>

