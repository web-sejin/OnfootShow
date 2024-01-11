<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if( ! $config['cf_social_login_use']) {     //소셜 로그인을 사용하지 않으면
    return;
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_JS_URL.'/remodal/remodal.css">', 11);
add_stylesheet('<link rel="stylesheet" href="'.G5_JS_URL.'/remodal/remodal-default-theme.css">', 12);
add_stylesheet('<link rel="stylesheet" href="'.get_social_skin_url().'/style.css?ver='.G5_CSS_VER.'">', 13);
add_javascript('<script src="'.G5_JS_URL.'/remodal/remodal.js"></script>', 10);

$email_msg = $is_exists_email ? '등록할 이메일이 중복되었습니다.다른 이메일을 입력해 주세요.' : '';
?>

<!-- 회원정보 입력/수정 시작 { -->
<div class="mbskin" id="register_member">

    <script src="<?php echo G5_JS_URL ?>/jquery.register_form.js"></script>
    
    <!-- 새로가입 시작 -->
	<div class="regi_area cm_padd2">

		<form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url; ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" name="w" value="<?php echo $w; ?>">
			<input type="hidden" name="url" value="<?php echo $urlencode; ?>">
			<input type="hidden" name="provider" value="<?php echo $provider_name;?>" >
			<input type="hidden" name="action" value="register">

			<input type="hidden" name="mb_id" value="<?php echo $user_id; ?>" id="reg_mb_id">
			<input type="hidden" name="mb_nick_default" value="<?php echo isset($user_nick)?get_text($user_nick):''; ?>">
			<input type="hidden" name="mb_nick" value="<?php echo isset($user_nick)?get_text($user_nick):''; ?>" id="reg_mb_nick">
			
			<input type="hidden" name="mb_email" value="<?php echo isset($user_email)?$user_email:''; ?>" id="reg_mb_email" class="frm_input email" placeholder="이메일을 입력해주세요." >
	
			<input type="hidden" name="w" id="w" value="<?php echo $w?>" readonly>
			<input type="hidden" name="app_chk" id="app_chk" value="<?php echo $_SESSION['appChk']?>" readonly>
			<input type="hidden" name="mb_token" id="mb_token" value="<?php echo $_SESSION['appToken']?>" readonly>
			<input type="hidden" id="chk_id" value="<?php if($is_member){?>1<?php }else{?>0<?php }?>" readonly>

			<?php if($is_member){?>
			<input type="hidden" id="base_hp" value="<?php echo $member['mb_hp']?>">
			<input type="hidden" id="base_email" value="<?php echo $member['mb_email']?>">
			<?php }?>

			<ul class="regi_ul">
				<li class="regi_li">
					<p class="regi_th">이름<span>*</span></p>
					<div class="regi_td">
						<input type="text" name="mb_name" id="mb_name" class="regi_ipt req_ipt" placeholder="이름을 입력해 주세요." value="<?php echo $member['mb_name']?>">
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">핸드폰 번호<span>*</span></p>
					<div class="regi_td">
						<input type="tel" name="mb_hp" id="mb_hp" class="regi_ipt req_ipt phone" placeholder="핸드폰 번호를 입력해 주세요." maxlength="13" value="<?php echo $member['mb_hp']?>">
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">주 활동지역<span>*</span></p>
					<div class="regi_td regi_td_flex">
						<select name="sd_idx" id="sd_idx" class="regi_ipt req_ipt regi_select ver5" onchange="chgSido(this.value); fnValueCount();">
							<option value="">시/도</option>
							<?php
							$sqlS = " select * from rb_sido where 1 order by sd_idx asc ";
							$resultS = sql_query($sqlS);
							$sido_selected = "";
							for($i=0; $sido=sql_fetch_array($resultS); $i++){
								//$sido_selected = ($row['dental_sido'] === $sido['sido_name']) ? "selected" : "";
							?>
							<option value="<?php echo $sido['sd_idx']?>"><?php echo $sido['sd_name']?></option>
							<?php }?>
						</select>
						<select name="si_idx" id="si_idx" class="regi_ipt req_ipt regi_select ver5" onChange="fnValueCount();">
							<option value="">시/구/군</option>
						</select>
					</div>
				</li>
				<li class="regi_li">
					<p class="regi_th">소소팀<span class="max_txt">(최대 3팀)</span><span>*</span></p>
					<div class="regi_td regi_td_flex">
						<button type="button" class="regi_ipt regi_button regi_button_1 ver5" onClick="newTeamSelectPop();">
							<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch_fff.svg" alt="">
							<span>팀 검색</span>
						</button>
						<button type="button" class="regi_ipt regi_button regi_button_2 ver5" onClick="newTeamCreatePop();">
							<img src="<?php echo G5_THEME_IMG_URL?>/ic_plus.svg" alt="">
							<span>팀 생성</span>
						</button>
					</div>
				</li>
			</ul>
			<div class="user_team_regi">
				<ul class="user_team_regi_ul" id="user_team_regi_ul"></ul>
			</div>
			<div class="fix_btn_back"></div>
			<div class="fix_btn_box">
				<button class="fix_btn" id="submit_button">회원가입</button>
			</div>
		</form>
	</div>

	<div id="new_team_pop2" class="cm_pop">
		<p class="cm_pop_back"></p>
		<div class="header">
			<button type="button" class="back_btn ver2" onClick="newTeamCancel2();"><img src="<?php echo G5_THEME_IMG_URL?>/ic_close.svg" alt=""></button>
			<div class="people_sch_box ver2">
				<input type="text" id="sch_val" placeholder="팀명을 검색하세요.">
				<button type="button" onClick="getTeamList();">
					<img src="<?php echo G5_THEME_IMG_URL?>/ic_sch.svg" alt="">
				</button>
			</div>
		</div>
		<div class="cm_pop_cont">
			<form name="new_team_frm" method="post">
			<ul class="user_team_regi_ul" id="pop_team_list"></ul>
			</form>
		</div>
		<div class="fix_btn_box">
			<button type="button" class="fix_btn on" onClick="newTeamSelect();">확인</button>
		</div>
	</div>

	<div id="new_team_pop" class="cm_pop ver2">
		<p class="cm_pop_back"></p>
		<div class="cm_pop_cont2">
			<p class="cm_pop_cont2_tit">팀 생성</p>
			<input type="text" id="new_team_name" class="cm_pop_cont2_ipt" placeholder="팀명을 입력해주세요.">
			<div class="cm_pop_cont2_btn_box">
				<button type="button" class="cm_pop_cont2_btn" onClick="newTeamCancel();">취소</button>
				<button type="button" class="cm_pop_cont2_btn on" onClick="newTeamCreate();">생성하기</button>
			</div>
		</div>
	</div>

	<script>
		function chgSido(v){
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/sigugun_list2.php",
				data: {sd_idx:v},
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					$("#si_idx").empty().append(data);
				}
			});
		}

		function newTeamSelectPop(){
			if($("#user_team_regi_ul .utr_li").length >= 3){
				showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
				return false;
			}
			cmPopOn("new_team_pop2");
		}

		function newTeamSelect(){
			const base_len = $("#user_team_regi_ul .utr_li").length;
			const new_len = $("#pop_team_list .utr_li").length;
			const chk_len = $("input[name='team_pick[]']:checked").length;
			if(new_len < 1){
				showToast("팀명을 검색해 주세요.");
				return false;
			}

			if(chk_len < 1){
				showToast("팀을 선택해 주세요.");
				return false;
			}

			console.log("base_len :: "+base_len);
			console.log("chk_len :: "+chk_len);

			if(base_len == 3 || (base_len == 2 && chk_len >= 2) || (base_len == 1 && chk_len >= 3) || chk_len >= 3){
				showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
				return false;
			}
			
			var new_string = $("form[name=new_team_frm]").serialize();
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/newTeamSelect.php",
				data: new_string, 
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					//console.log(data);
					$("#user_team_regi_ul").append(data);
					newTeamCancel2();
					listReset();
				}
			});
		}

		function newTeamCreatePop(){
			if($("#user_team_regi_ul .utr_li").length >= 3){
				showToast("소속팀은 최대 3개까지 등록할 수 있습니다.");
				return false;
			}
			cmPopOn("new_team_pop");
		}

		function newTeamCancel2(){
			cmPopOff("new_team_pop2");
			$("#sch_val").val("");
			$("#pop_team_list").empty();
		}

		function getTeamList(){
			if($("#sch_val").val() == ""){
				showToast("팀명을 검색해 주세요.");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/getTeamList.php",
				data: {v:$("#sch_val").val()}, 
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					//console.log(data);
					$("#pop_team_list").empty().append(data);
				}
			});
		}

		function newTeamCancel(){
			cmPopOff("new_team_pop");
			$("#new_team_name").val("");
		}

		function newTeamCreate(){
			if($("#new_team_name").val() == ""){ showToast("팀명을 입력해 주세요."); return false; }

			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/newTeamCreate.php",
				data: {teamName:$("#new_team_name").val()}, 
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					newTeamCancel();
					$("#user_team_regi_ul").append(data);
					listReset();
				}
			});
		}

		function teamRemove(v){
			$("#utr_li_"+v).remove();
			listReset();
		}

		function listReset(){
			let cnt = 0;
			$("#user_team_regi_ul .utr_li").each(function(i){
				$(this).attr("id", "utr_li_"+i);
				$(this).children("button").attr("onClick", "teamRemove('"+i+"')");;
				cnt++;
			});
			if(cnt > 0){
				$(".user_team_regi").addClass("on");
			}else{
				$(".user_team_regi").removeClass("on");
			}
			fnValueCount();
		}

		function teamListChk(at_idx){
			$("#user_team_regi_ul .utr_li").each(function(){
				const this_idx = $(this).children("input[name='team_idx[]']").val();
				if(this_idx == at_idx){
					$("#team_pick_"+at_idx).prop("checked", false);
					showToast("이미 선택된 팀입니다.");
				}
			})
		}

		function fnValueCount(){
			let reqCnt = ($(".req_ipt").length)+1;
			let reqCurCnt = 0;
			$(".req_ipt").each(function(){
				if($(this).val() != ""){
					reqCurCnt++;
				}
			});

			if($("#user_team_regi_ul .utr_li").length > 0){ reqCurCnt++;  }

			if($("#sd_idx").val() == "36"){
				reqCnt = ($(".req_ipt").length);
			}
			//console.log(reqCurCnt+"//"+reqCnt);

			if(reqCurCnt >= reqCnt){
				$("#submit_button").addClass("on");
			}else{
				$("#submit_button").removeClass("on");
			}
		}

		$(".req_ipt").keyup(function(e) {
			fnValueCount();
		});

		function fregisterform_submit(){
			const name = document.getElementById("mb_name");	
			const hp = document.getElementById("mb_hp");
			const sd_idx = document.getElementById("sd_idx");
			const si_idx = document.getElementById("si_idx");
			const teamCnt = $("#user_team_regi_ul .utr_li").length;

			if(name.value == ""){ showToast("이름을 입력해 주세요."); return false; remove_active(); }
			if(hp.value == ""){ showToast("핸드폰 번호를 입력해 주세요."); return false; remove_active(); }
			if((hp.value).length != 13){ showToast("핸드폰 번호를 정확히 입력해 주세요."); remove_active(); return false; }
			let hpStatus = fnidChk(hp.value, "mb_hp", "1");
			if(w == "u" && $("#base_hp").val() == hp.value){ hpStatus = true; }
			if(!hpStatus){ showToast("이미 사용중인 핸드폰 번호 입니다."); remove_active(); return false; }
			if(sd_idx.value == ""){ showToast("풋살장 지역(시/도)을 선택해 주세요."); remove_active(); return false; }
			if(sd_idx.value == "36"){ 
			}else{
				if(si_idx.value == ""){ showToast("풋살장 지역(시/구/군)을 선택해 주세요."); remove_active(); return false; }
			}
			if(teamCnt < 1){ showToast("소속팀을 1개 이상 생성해 주세요,"); remove_active(); return false; }
			
			/*var string = $("form[name=regi_frm]").serialize();
			$.ajax({
				type: "POST",
				url: "<?php echo G5_URL?>/inc/user_register_update.php",
				data: string, 
				cache: false,
				async: false,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				success: function(data) {
					location.href = "<?php echo G5_URL?>/user";
				}
			});*/
		}
	</script>
	
	
	<script>
    function flogin_submit(f)
    {
        var mb_id = $.trim($(f).find("input[name=mb_id]").val()),
            mb_password = $.trim($(f).find("input[name=mb_password]").val());

        if(!mb_id || !mb_password){
            return false;
        }

        return true;
    }

    jQuery(function($){
        if( jQuery(".toggle .toggle-title").hasClass('active') ){
            jQuery(".toggle .toggle-title.active").closest('.toggle').find('.toggle-inner').show();
        }
        jQuery(".toggle .toggle-title .right_i").click(function(){

            var $parent = $(this).parent();
            
            if( $parent.hasClass('active') ){
                $parent.removeClass("active").closest('.toggle').find('.toggle-inner').slideUp(200);
            } else {
                $parent.addClass("active").closest('.toggle').find('.toggle-inner').slideDown(200);
            }
        });
        // 모두선택
        $("input[name=chk_all]").click(function() {
            if ($(this).prop('checked')) {
                $("input[name^=agree]").prop('checked', true);
            } else {
                $("input[name^=agree]").prop("checked", false);
            }
        });
    });
    </script>

</div>
<!-- } 회원정보 입력/수정 끝 -->