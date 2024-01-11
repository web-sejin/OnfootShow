<?php
	include_once("_common.php");
	
	$cnt = 0;
	$sql = " SELECT * FROM a_team A, g5_member B WHERE A.mb_idx = B.mb_no AND A.at_team_name LIKE '%{$v}%' ORDER BY A.at_team_name ASC ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$hpBack = explode("-", $row['mb_hp']);

		$name_x ='*';
		$name_a = mb_substr($row['mb_name'],0,1,"UTF-8");
		$name_b = mb_substr($row['mb_name'],2,10,"UTF-8");
		$name = $name_a.$name_x.$name_b;
		$cnt++;
?>
<li class="utr_li">
	<p class="user_team_leader">
		<span><?php echo $name?></span>
		<span></span>
		<span><?php echo $hpBack[2]?></span>
	</p>
	<p class="user_team_p ver1">
		<strong>팀명</strong>
		<span><?php echo $row['at_team_name']?></span>
	</p>
	<p class="user_team_p ver2">
		<strong>지역</strong>
		<span><?php echo getSido($row['at_sido'])?> <?php echo getSigugun($row['at_sigugun'])?></span>
	</p>
	<input type="checkbox" name="team_pick[]" id="team_pick_<?php echo $row['at_idx']?>" value="<?php echo $row['at_idx']?>" onChange="teamListChk('<?php echo $row['at_idx']?>');">
	<label for="team_pick_<?php echo $row['at_idx']?>"></label>
</li>
<?php }?>
<?php if($cnt < 1){?>
<li class="not_data">검색된 팀이 없습니다.</li>
<?php }?>