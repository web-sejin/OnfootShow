<?php
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
$sub_menu = "300910";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "리그 관리";
include_once('./admin.head.php');

$sql = " select * from a_league where 1=1 and al_idx = '{$al_idx}' ";
$row = sql_fetch($sql);

if($s_txt != ""){ $qstr .= "&s_txt=".$s_txt; }
?>
<link rel="stylesheet" href="<?php echo G5_CSS_URL?>/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<section id="anc_bo_basic">
    <?php echo $pg_anchor ?>
	<form method="post" action="./a_league_write.update.php" enctype="multipart/form-data" onSubmit="return fnsubmit();" autocomplete="off">
	<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
	<input type="hidden" name="al_idx" value="<?php echo $al_idx?>" readonly>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>게시판 기본 설정</caption>
        <colgroup>
            <tbody>				
				<tr>
					<th scope="row">분류</th>
					<td>
						<select name="al_cate" id="al_cate" class="frm_input">
							<option value="1" <?php if($row['al_start'] == 1){ echo "selected"; }?>>리그정보</option>
							<option value="2" <?php if($row['al_start'] == 2){ echo "selected"; }?>>리그진행/결과</option>
							<option value="3" <?php if($row['al_start'] == 3){ echo "selected"; }?>>리그 게시판</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">대회 시작일</th>
					<td>
						<?php echo help("※ 리그 게시판에는 적용되지 않습니다.");?>
						<input type="text" name="al_start" id="al_start" class="frm_input" readonly value="<?php echo $row['al_start']?>">
					</td>
				</tr>
				<tr>
					<th scope="row">대회 종료일</th>
					<td>
						<?php echo help("※ 작성하지 않을 경우 [시작일~] 형식으로 노출됩니다.<br>※ 리그 게시판에는 적용되지 않습니다.");?>
						<input type="text" name="al_end" id="al_end" class="frm_input" readonly value="<?php echo $row['al_end']?>">
					</td>
				</tr>
				<tr>
                    <th scope="row">제목</th>
                    <td>
                        <input type="tel" name="al_subject" id="al_subject" class="frm_input frm_input100" value="<?php echo $row['al_subject']; ?>" placeholder="제목을 입력해주세요.">
                    </td>
                </tr>
                <tr>
                    <th scope="row">내용</th>
                    <td>
                        <?php echo editor_html("al_content", get_text(html_purifier($row['al_content']), 0)); ?>
                    </td>
                </tr>
				<tr>
					<th scope="row">썸네일</th>
					<td>
						<?php echo help("※ 권장 비율 : 1050px X 480px");?>
						<?php echo help("※ 리그 게시판에는 적용되지 않습니다.");?>
						<input type="file" name="al_file" id="al_file" class="frm_input frm_input100" accept="image/*">

						<?php if($row['al_file_af']){?>
						<p class="curr_thum">
							현재 등록된 썸네일 :
							<img src="<?php echo G5_DATA_URL."/leagueFile/".$row['al_file_af']?>" alt="" style="width:150px">
						</p>
						<?php }?>
					</td>
				</tr>
            </tbody>
        </table>
    </div>
	
	
	<div class="btn_fixed_top">
		<a href="./a_league_list.php?<?php echo $qstr?>" id="bo_add" class="btn_03 btn">목록</a>
		<button class="btn btn_01">저장</button>
	</div>

	</form>
</section>
<div class="datepicker_back"></div>

<script>
	function fnsubmit(){
		<?php echo get_editor_js("al_content"); ?>
	}

	$(function(){
		$("#al_start").datepicker({ 
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
			yearRange: 'c-100:c+100', 
			showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
			currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
			closeText: '닫기', // 닫기 버튼 패널 
			dateFormat: "yy-mm-dd", // 텍스트 필드에 입력되는 날짜 형식. 
			showAnim: "fade", //애니메이션을 적용한다. 
			showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
			monthNames : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'], 
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			beforeShow:function(){
				$(".datepicker_back").fadeIn();
			},
			onSelect: function (date) {
				var endDate = $('#al_end');
				var startDate = $(this).datepicker('getDate');
				var minDate = $(this).datepicker('getDate');
				endDate.datepicker('setDate', minDate);
				startDate.setDate(startDate.getDate() + 36500);
				endDate.datepicker('option', 'maxDate', startDate);
				endDate.datepicker('option', 'minDate', minDate);
				$(".datepicker_back").fadeOut();
			},
			onClose:function(){
				$(".datepicker_back").fadeOut();
			},	
		});
		$('#al_end').datepicker({
			changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다. 
			changeYear: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다. 
			yearRange: 'c-5:c+5', 
			showButtonPanel: true, // 캘린더 하단에 버튼 패널을 표시한다. 
			currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널 
			closeText: '닫기', // 닫기 버튼 패널 
			dateFormat: "yy-mm-dd", // 날짜의 형식
			showAnim: "fade", //애니메이션을 적용한다. 
			showMonthAfterYear: false , // 월, 년순의 셀렉트 박스를 년,월 순으로 바꿔준다. 
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'], // 요일 
			monthNames : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'], 
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			beforeShow:function(){
				$(".datepicker_back").fadeIn();
			},
			onSelect:function(dateText, inst){
				$(".datepicker_back").fadeOut();
			},
			onClose:function(){
				$(".datepicker_back").fadeOut();
			},		
		});

		if($("#w").val() == "u"){
			$("#sd_idx").val("<?php echo $row['sd_idx']?>");
			$("#sd_idx").change();
			$("#si_idx").val("<?php echo $row['si_idx']?>");
		}
	});
</script>

<?php
include_once('./admin.tail.php');
?>

