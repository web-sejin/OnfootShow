<?php
	include_once("_common.php");

	$idx = "";
	for($i=0; $i<count($team_pick); $i++){
		if($i != 0){ $idx .= ",";  }
		$idx .= "'".$team_pick[$i]."'";
	}

	$sql = " SELECT * FROM a_team A, g5_member B WHERE A.mb_idx = B.mb_no AND A.at_idx IN ({$idx}) ORDER BY A.at_team_name ASC ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$hpBack = explode("-", $row['mb_hp']);

		$name_x ='*';
		$name_a = mb_substr($row['mb_name'],0,1,"UTF-8");
		$name_b = mb_substr($row['mb_name'],2,10,"UTF-8");
		$name = $name_a.$name_x.$name_b;
?>
<li class="utr_li">	
	<input type="hidden" name="team_idx[]" value="<?php echo $row['at_idx']?>">
	<input type="hidden" name="team_type[]" value="1">
	<input type="hidden" name="team_name[]" value="<?php echo $row['at_team_name']?>">
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
	<button type="button">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_remove.svg" alt="">
	</button>
</li>
<?php }?>