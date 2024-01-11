<?php
	include_once("_common.php");

	$sql = " select * from a_match where  am_idx = '{$am_idx}' ";
	$row = sql_fetch($sql);

	$reqTotal = 0;
	$sql_req = " select * 
								from a_match_req A, g5_member B, a_team C
								where A.mb_idx = B.mb_no 
									and B.mb_leave_status = 1 
									and  A.am_idx = '{$am_idx}' 
									and A.at_idx = C.at_idx
								order by A.amr_datetime asc 
								";
	$result_req = sql_query($sql_req);
	for($r=0; $req=sql_fetch_array($result_req); $r++){
		$reqTotal++;
?>
<li>
	<img src="<?php echo G5_THEME_IMG_URL?>/ic_ball2.svg" alt="">
	<span><?php echo $req['at_team_name']?></span>
	<?php if($row['mb_idx'] == $member['mb_no']){?>
	<div class="mv_team_confirm">
		<button type="button" onClick="rnMessage('<?php echo $req['mb_hp']?>');">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_message.svg" alt="">
			<span>문자보내기</span>
		</button>	
		<?php if($row['res_st'] == 1){?>
		<a href="<?php echo G5_URL?>/user/match_stadium_list.php?idx=<?php echo $am_idx?>&amr_idx=<?php echo $req['amr_idx']?>">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk2.svg" alt="">
			<span>확정하기</span>
		</a>
		<?php }else{?>
		<button type="button" onClick="teamConform('<?php echo $req['mb_idx']?>', '<?php echo $req['at_idx']?>', '<?php echo getTeamName($req['at_idx'])?>');">
			<img src="<?php echo G5_THEME_IMG_URL?>/ic_chk2.svg" alt="">
			<span>확정하기</span>
		</button>					
		<?php }?>
	</div>
	<?php }?>
</li>
<?php }?>
<?php if($reqTotal < 1){?>
<li class="not_data">매치 신청팀이 없습니다.</li>
<?php }?>