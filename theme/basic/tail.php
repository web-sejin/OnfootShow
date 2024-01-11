<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
    return;
}

if($basename != "index.php"){
?>
	</div>
</div>
<?php }?>

<?php if($member['mb_type'] == "1"){?>
<?php if($basename == "index.php" || $tailOn == "on"){?>
<div class="user_footer_ht"></div>
<footer class="user_footer">
	<a href="<?php echo G5_URL?>/user" <?php if($basename == "index.php" || $basename == "user_match_list.php"){?>class="on"<?php }?>>
		<strong><img src="<?php echo G5_THEME_IMG_URL?>/ft_1_<?php if($basename == "index.php" || $basename == "user_match_list.php"){ echo "on";  }else{ echo "off";  }?>.svg"></strong>
		<span>매치</span>
	</a>
	<a href="<?php echo G5_URL?>/user/stadium_list.php" <?php if($basename == "stadium_list.php"){?>class="on"<?php }?>>
		<strong><img src="<?php echo G5_THEME_IMG_URL?>/ft_2_<?php if($basename == "stadium_list.php"){ echo "on";  }else{ echo "off";  }?>.svg"></strong>
		<span>구장검색</span>
	</a>
	<a href="<?php echo G5_URL?>/user/board/notice_list.php" <?php if($basename == "notice_list.php"){?>class="on"<?php }?>>
		<strong><img src="<?php echo G5_THEME_IMG_URL?>/ft_3_<?php if($basename == "notice_list.php"){ echo "on";  }else{ echo "off";  }?>.svg"></strong>
		<span>온풋소식</span>
	</a>
	<a href="<?php echo G5_URL?>/user/board/league_list.php?cate=1" <?php if($basename == "league_list.php"){?>class="on"<?php }?>>
		<strong><img src="<?php echo G5_THEME_IMG_URL?>/ft_4_<?php if($basename == "league_list.php"){ echo "on";  }else{ echo "off";  }?>.svg"></strong>
		<span>리그정보</span>
	</a>
	<a href="<?php echo G5_URL?>/user/member/mypage.php" <?php if($basename == "mypage.php"){?>class="on"<?php }?>>
		<strong><img src="<?php echo G5_THEME_IMG_URL?>/ft_5_<?php if($basename == "mypage.php"){ echo "on";  }else{ echo "off";  }?>.svg"></strong>
		<span>마이페이지</span>
	</a>
</footer>
<?php }?>
<?php }?>

<div class="datepicker_back"></div>
<div class="toast_box">
	<p class="toast_cont"></p>
</div>

<div class="indicator">
	<p><img src="<?php echo G5_THEME_IMG_URL?>/indicator.gif" alt=""></p>
</div>

<script>
	var geocoder = new kakao.maps.services.Geocoder();
	var lat = "";
	var lng = "";

	window.addEventListener('load', function(){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				lat = position.coords.latitude; // 위도
				lng = position.coords.longitude; // 경도            			
				console.log("A :"+lat+"//"+lng);		
				saveSession(lat, lng);
			}, function(error){
				//alert(error.message);
				console.log("B :"+lat+"//"+lng);
				saveSession(lat, lng);		
			});		
		} else {		
			console.log("C :"+lat+"//"+lng);
			saveSession(lat, lng);		
		}
	});

	//좌표 세션 저장
	function saveSession(lat, lng){
		$.ajax({
			type: "POST",
			url: "<?php echo G5_URL?>/inc/save_session.php",
			data: {lat:lat, lng:lng}, 
			cache: false,
			async: false,
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success: function(data) {
				if(lat == "" || lng == ""){
					if("<?php echo $basename?>" == "stadium_list.php"){
						$(".ust_km").html("위치권한없음");
					}
				}
			}
		});
	}

	$(".info_window").hover(function(){
		$(this).parent().siblings(".regi_th_desc").show();
	},function(){
		$(this).parent().siblings(".regi_th_desc").hide();
	});

	function pageChange(link){
		location.href = link;
	}

	function cmPopOn(pop){ 
		$(`#${pop}`).show();
		bodyLock(); 

		if (window.ReactNativeWebView) {
			window.ReactNativeWebView.postMessage(
			  JSON.stringify({data:"popup", pop_id:pop, type:true})
			);
		}
	}

	function cmPopOff(pop){ 
		$(`#${pop}`).hide(); 
		bodyUnlock(); 

		if (window.ReactNativeWebView) {
			window.ReactNativeWebView.postMessage(
			  JSON.stringify({data:"popup", pop_id:pop, type:false})
			);
		}
	}

	function rnMessage(phone){
		if (window.ReactNativeWebView) {
			window.ReactNativeWebView.postMessage(
			  JSON.stringify({data:"message", phone:phone})
			);
		}
	}
</script>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");