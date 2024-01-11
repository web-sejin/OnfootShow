<?php
	include_once('../../common.php');
	include_once(G5_PATH."/_head.php");

	//수정, 삭제작업 안되어 있음.
?>

<div class="qna_wrt cm_padd2">
	<form name="qna_frm" id="qna_frm" method="post" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
		<input type="hidden" name="aq_idx" id="aq_idx" value="<?php echo $aq_idx?>" readonly>

		<ul class="regi_ul">
			<li class="regi_li">
				<p class="regi_th">제목</p>
				<div class="regi_td">
					<input type="text" name="aq_subject" id="aq_subject" class="regi_ipt req_ipt" placeholder="문의 제목을 입력해 주세요." value="<?php echo $row['aq_subject']?>">
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">내용</p>
				<div class="regi_td">
					<textarea name="aq_question" id="aq_question" class="regi_ipt regi_txtarea2 req_ipt" placeholder="문의 내용을 입력해 주세요."><?php echo $row['aq_question']?></textarea>
				</div>
			</li>
			<li class="regi_li">
				<p class="regi_th">이미지 등록</p>				
				<div class="regi_td">
					<div class="pic_box">
						<ul class="pic_list" id="pic_list">
							<?php if($is_member){?>
								<?php
									$thumCnt = 1;
									$sql_thum = " select * from a_futsal_img where mb_idx = '{$member['mb_no']}' order by img_od asc ";
									$result_thum = sql_query($sql_thum);
									for($i=0; $thum=sql_fetch_array($result_thum); $i++){
										$thumCnt++;
										$od = $thum['img_od'];
								?>
								<li id="pic_li_<?php echo $od?>" class="pic_li_org" data-idx="<?php echo $thum['img_idx']?>">									
									<input type="file" name="pic_img_<?php echo $od?>" id="pic_img_<?php echo $od?>" onchange="readURL(this, 'pic', '<?php echo $od?>');" accept="image/*"> 									
									<label for="pic_img_<?php echo $od?>">
										<img id="pic_<?php echo $od?>" src="<?php echo G5_DATA_URL?>/footsalFile/<?php echo $thum['img_af']?>" />
										<strong><span><?php echo $od?></span> / 5</strong>
									</label>
									<button type="button" class="pic_<?php echo $od?>_del" onClick="picDel('pic', '<?php echo $od?>');" style="display:block;">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
								<?php }?>
								<li id="pic_li_<?php echo $thumCnt?>">
									<input type="file" name="pic_img_<?php echo $thumCnt?>" id="pic_img_<?php echo $thumCnt?>" onchange="readURL(this, 'pic', '<?php echo $thumCnt?>');" accept="image/*"> 
									<label for="pic_img_<?php echo $thumCnt?>">
										<img id="pic_<?php echo $thumCnt?>" />
										<strong><span><?php echo $thumCnt?></span> / 5</strong>
									</label>
									<button type="button" class="pic_<?php echo $thumCnt?>_del" onClick="picDel('pic', '<?php echo $thumCnt?>');">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
							<?php }else{?>
								<li id="pic_li_1">
									<input type="file" name="pic_img_1" id="pic_img_1" onchange="readURL(this, 'pic', '1');" accept="image/*"> 
									<label for="pic_img_1">
										<img id="pic_1" />
										<strong><span>1</span> / 5</strong>
									</label>
									<button type="button" class="pic_1_del" onClick="picDel('pic', '1');">
										<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">
									</button>
								</li>
							<?php }?>
						</ul>
					</div>
				</div>
			</li>
		</ul>

		<div class="fix_btn_back"></div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn" id="submit_button" onClick="qnaWrite();">등록</button>
		</div>
	</form>
</div>

<script>
	function readURL(input, type, v) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById(type+'_'+v).src = e.target.result;
				$("."+type+"_"+v+"_del").show();
				addPic((v*1)+1);
			};
			reader.readAsDataURL(input.files[0]);
			if($("#w").val() == "u"){
				if(v == "1"){ $("#first_img").val(1); }
			}
		} else {
			document.getElementById(type+'_'+v).src = "";
			$("."+type+"_"+v+"_del").hide();		
			if($("#w").val() == "u"){
				if(v == "1"){ $("#first_img").val(0); }
			}
		}
		fnValueCount();
	}

	function picDel(type, v){
		const has = $(`#pic_li_${v}`).hasClass("pic_li_org");
		if(has){
			const img_idx = $(`#pic_li_${v}`).attr("data-idx");
			$("#del_cofirm_btn").attr("onClick", `deleteOk('${img_idx}', 'thumbnail', '${v}');`);
			cmPopOn("delete_confirm_pop");
		}else{
			if(v == "1"){
				document.getElementById(type+'_'+v).src = "";
				$("#"+type+"_img_"+v).val("");
				$("."+type+"_"+v+"_del").hide();
			}else{
				$("#"+type+"_li_"+v).remove();
				picRefresh();
			}
		}
	}

	function addPic(v){
		const pic_len = $("#pic_list li").length;
		if(v > pic_len){
			let cont = "";
			cont += `<li id="pic_li_${v}">`;
			cont += `<input type="file" name="pic_img_${v}" id="pic_img_${v}" onchange="readURL(this, 'pic', '${v}');"> `;
			cont += `<label for="pic_img_${v}">`;
			cont += `<img id="pic_${v}" />`;
			cont += `<strong><span>${v}</span> / 5</strong>`;
			cont += `</label>`;
			cont += `<button type="button" class="pic_${v}_del" onClick="picDel('pic', '${v}');">`;
			cont += `<img src="<?php echo G5_THEME_IMG_URL?>/ic_delete.png" alt="">`;
			cont += `</button>`;
			cont += `</li>`;

			$("#pic_list").append(cont);
		}
	}

	function picRefresh(){
		$("#pic_list li").each(function(i){
			const od = i+1;
			$(this).attr("id", "pic_li_"+od);
			$(this).children("input[type=file]").attr("name", `pic_img_${od}`);
			$(this).children("input[type=file]").attr("id", `pic_img_${od}`);
			$(this).children("input[type=file]").attr("onchange", `readURL(this, 'pic', '${od}');`);
			$(this).children("label").attr("for", `pic_img_${od}`);
			$(this).children("label").children("img").attr("id", `pic_${od}`);
			$(this).children("label").children("strong").html(`<span>${od}</span> / 5`);
			$(this).children("button").attr("class", `pic_${od}_del`);
			$(this).children("button").attr("onClick", `picDel('pic', '${od}');`);
		})
	}

	$(".req_ipt").keyup(function(e) {
		fnValueCount();
	});
	
	function fnValueCount(){
		let reqCnt = ($(".req_ipt").length);
		let reqCurCnt = 0;
		$(".req_ipt").each(function(){
			if($(this).val() != ""){
				reqCurCnt++;
			}
		});

		if(reqCurCnt >= reqCnt){
			$("#submit_button").addClass("on");
		}else{
			$("#submit_button").removeClass("on");
		}
	}

	function qnaWrite(){
		if($("#aq_subject").val() == ""){ showToast("제목을 입력해 주세요."); remove_active(); return false; }
		if($("#aq_question").val() == ""){ showToast("내용을 입력해 주세요."); remove_active(); return false; }

		const form = $("#qna_frm")[0];
		const formData = new FormData(form);
		
		formData.append("w", $("#w").val());
		formData.append("aq_idx", $("#aq_idx").val());
		formData.append("aq_subject", $("#aq_subject").val());
		formData.append("aq_question", $("#aq_question").val());

		if ($('#pic_img_1').length) {
			formData.append("pic_img_1", $('[name="pic_img_1"]')[0].files[0]);
		}
		if ($('#pic_img_2').length) {
			formData.append("pic_img_2", $('[name="pic_img_2"]')[0].files[0]);
		}
		if ($('#pic_img_3').length) {
			formData.append("pic_img_3", $('[name="pic_img_3"]')[0].files[0]);
		}
		if ($('#pic_img_4').length) {
			formData.append("pic_img_4", $('[name="pic_img_4"]')[0].files[0]);
		}
		if ($('#pic_img_5').length) {
			formData.append("pic_img_5", $('[name="pic_img_5"]')[0].files[0]);
		}

		$.ajax({
			url: '/inc/qna_write_update.php',
			type: "post",
			processData: false,  // file전송시 필수
			contentType: false,  // file전송시 필수
			data: formData
		})
		.done(function(data) {
			console.log(data);
			if(data == "1111"){		
				if($("#w").val() == "u"){
					showToast("정보수정이 완료되었습니다.");	
				}else{
					location.href="<?php echo G5_URL?>/user/board/qna_list.php";
				}
			}else{
				showToast("잠시후 다시 이용해 주세요.");
			}
		});	
	}
</script>

<?php
	include_once(G5_PATH."/_tail.php");
?>