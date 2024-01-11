<?php
	include_once("_common.php");
	include_once(G5_PATH."/_head.php");
?>
<style>
	.regi_ipt {width:100%;height:55px;background:#fff;padding-left:20px;border:1px solid #E6E6E6;font-size:14px;}
	.regi_ipt.ver2 {padding-right:125px;}

	.filebox {width:100%;position:relative;}
	.filebox input[type="file"] {position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0;}
	.filebox label {padding:10px;background:#fff;position:absolute;right:5px;top:50%;transform:translateY(-50%);cursor:pointer;}

	.cm_btn_box {display:flex;justify-content:center;}
	.cm_btn {display:flex;align-items:center;justify-content:center;max-width:100%;width:400px;height:60px;background:#000;border:none;border-radius:50px;font-size:16px;color:#fff;}
	.cm_btn_cancel {background:#fff;border:1px solid #000;color:#000;}
	
	.cm_btn_box2 {margin-top:50px;}
	.cm_btn_box2 .cm_btn {width:250px;height:48px;}
	.cm_btn_box2 .cm_btn + .cm_btn {margin-left:8px;}


	.board_sch_box {padding:70px 0;margin:60px 0;border-top:1px solid #000;border-bottom:1px solid #000;display:flex;align-items:center;justify-content:center;}
	.board_sch_box .regi_select {width:180px;}	
	.board_sch_ipt_box {width:680px;position:relative;margin-left:14px;}	
	.board_sch_ipt_box button {width:54px;height:100%;background:none;border:none;position:absolute;top:0;right:0;}

	.cm_tb {display:flex;width:100%;}
	.cm_tb > li {display:flex;align-items:center;justify-content:center;}
	.cm_tb_head > li {background:#000;padding:20px 5px;font-size:18px;line-height:1.1;font-weight:500;color:#fff;}
	.cm_tb_body > li {padding:25px 5px;font-size:16px;line-height:1.1;border-bottom:1px solid #E5E5E5;}
	.cm_tb_body > li a {display:inline-block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

	.cm_tb_li1 {width:11%;}
	.cm_tb_li2 {width:11%;}
	.cm_tb_li3 {width:65%;}
	.cm_tb_li4 {width:13%;}

	.bo_w_ul {}
	.bo_w_ul li + li {margin-top:24px;}
</style>

<div style="width:1400px;margin:0 auto;padding:100px 0;">
	<!--
	<form>
		<div class="board_sch_box">
			<select name="" id="" class="regi_ipt regi_select">
				<option value="">제목+내용</option>
			</select>
			<div class="board_sch_ipt_box">
				<input type="text" name="" id="" class="regi_ipt" placeholder="직접입력">
				<button>icon</button>
			</div>			
		</div>
	</form>

	<ul class="cm_tb cm_tb_head">
		<li class="cm_tb_li1">번호</li>
		<li class="cm_tb_li2">구분</li>
		<li class="cm_tb_li3">제목</li>
		<li class="cm_tb_li4">등록일</li>
	</ul>
	<?php for($i=0; $i<10; $i++){?>
	<ul class="cm_tb cm_tb_body">
		<li class="cm_tb_li1"><?php echo (10-$i)?></li>
		<li class="cm_tb_li2">이벤트</li>
		<li class="cm_tb_li3"><a href="">이벤트관련 제목이 표시됩니다.</a></li>
		<li class="cm_tb_li4">2023-11-01</li>
	</ul>
	<?php }?>
	-->

	<ul class="bo_w_ul">
		<li>
			<select name="" id="" class="regi_ipt regi_select">
				<option value="">분류를 선택하세요.</option>
			</select>
		</li>
		<li><input type="text" name="" id="" class="regi_ipt" placeholder="제목을 입력해 주세요." value=""></li>
		<li>
			<div class="filebox">
				<input class="upload-name regi_ipt ver2" value="" placeholder="첨부파일은 JPG, PNG 파일만 가능" disabled="disabled">
				<label for="file_ipt"><img src="http://k2bike.a-server.kr/theme/basic/img/icon_file_add.svg" alt="">add</label> 
				<input type="file" name="" id="file_ipt" class="upload-hidden" accept=".jpg, .png">
			</div>
		</li>
		<li><input type="text" name="" id="" class="regi_ipt" placeholder="파일 설명을 입력해 주세요." value=""></li>
	</ul>
	<div class="cm_btn_box cm_btn_box2">
		<button type="button" class="cm_btn cm_btn_cancel" onClick="">취소</button>
		<button class="cm_btn" onClick="">작성완료</button>
	</div>
</div>

<script>
   $(function() {
			 var fileTarget = $('.filebox .upload-hidden');
			fileTarget.on('change', function(){  // 값이 변경되면
				if(window.FileReader){  // modern browser
					var filename = $(this)[0].files[0].name;
				} 
				else {  // old IE
					var filename = $(this).val().split('/').pop().split('\\').pop();  // 파일명만 추출
				}
				
				// 추출한 파일명 삽입
				$(this).siblings('.upload-name').val(filename);
			});
   });
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>