<?php
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$sub_menu = "300930";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "약관 관리";
include_once('./admin.head.php');

$sql = " select * from a_privacy where 1=1 ";
$row = sql_fetch($sql);
?>
<section id="anc_bo_basic">
	<form method="post" action="./a_privacy.update.php" enctype="multipart/form-data" onSubmit="return fnsubmit();" autocomplete="off">
		<div class="tbl_frm01 tbl_wrap">
			<table>
				<caption>게시판 기본 설정</caption>
				<tbody>
					<tr>
						<th scope="row">서비스 이용약관</th>
						<td>
							<?php echo editor_html("ap_provision", get_text(html_purifier($row['ap_provision']), 0)); ?>
						</td>
					</tr>	
					<tr>
						<th scope="row">개인정보처리방침</th>
						<td>
							<?php echo editor_html("ap_privacy", get_text(html_purifier($row['ap_privacy']), 0)); ?>
						</td>
					</tr>	
				</tbody>
			</table>
		</div>		

		<div class="btn_fixed_top">
			<button class="btn btn_01">확인</button>
		</div>

	</form>
</section>

<script>
	function fnsubmit(){
		<?php echo get_editor_js("ap_provision"); ?>
		<?php echo get_editor_js("ap_privacy"); ?>
	}
</script>

<?php
include_once('./admin.tail.php');
?>