<?php
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
$sub_menu = "300920";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "1:1문의 관리";
include_once('./admin.head.php');

$sql = " select * from a_qna where 1=1 and aq_idx = '{$aq_idx}' ";
$row = sql_fetch($sql);

if($s_txt != ""){ $qstr .= "&s_txt=".$s_txt; }
?>
<section id="anc_bo_basic">
    <?php echo $pg_anchor ?>
	<form method="post" action="./a_qna_write.update.php" enctype="multipart/form-data" onSubmit="return fnsubmit();" autocomplete="off">
	<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
	<input type="hidden" name="aq_idx" value="<?php echo $aq_idx?>" readonly>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>게시판 기본 설정</caption>
        <colgroup>
            <tbody>
				<tr>
                    <th scope="row">제목</th>
                    <td>
                        <input type="tel" name="aq_subject" id="aq_subject" class="frm_input frm_input100" value="<?php echo $row['aq_subject']; ?>" placeholder="제목을 입력해주세요." readonly>
                    </td>
                </tr>
                <tr>
                    <th scope="row">질문 내용</th>
                    <td>
						<textarea name="aq_question" id="aq_question" class="frm_input frm_input100" readonly><?php echo $row['aq_question']?></textarea>
                    </td>
                </tr>
				<tr>
                    <th scope="row">첨부파일</th>
                    <td>
						<ul class="qna_file">
							<?php
								$sql2 = " select * from a_qna_img where aq_idx = '{$aq_idx}' ";
								$result2 = sql_query($sql2);
								for($i=0; $img=sql_fetch_array($result2); $i++){
							?>
							<li><img src="<?php echo G5_DATA_URL?>/qnaFile/<?php echo $img['aqi_img_af']?>" alt=""></li>
							<?php }?>
						</ul>
                    </td>
                </tr>
				 <tr>
                    <th scope="row">답변</th>
                    <td>
						<textarea name="aq_answer" id="aq_answer" class="frm_input frm_input100"><?php echo $row['aq_answer']?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
	
	
	<div class="btn_fixed_top">
		<a href="./a_qna_list.php?<?php echo $qstr?>" id="bo_add" class="btn_03 btn">목록</a>
		<button class="btn btn_01">저장</button>
	</div>

	</form>
</section>

<script>
	function fnsubmit(){

	}
</script>

<?php
include_once('./admin.tail.php');
?>

