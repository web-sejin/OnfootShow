<?php
	include_once("_common.php");
?>

<li class="utr_li">
	<input type="hidden" name="team_idx[]" value="0">
	<input type="hidden" name="team_type[]" value="2">
	<input type="hidden" name="team_name[]" value="<?php echo $teamName ?>">
	<p class="user_team_new">NEW</p>
	<p class="user_team_p ver1">
		<strong>팀명</strong>
		<span><?php echo $teamName ?></span>
	</p>
	<button type="button" onClick="teamRemove('3');">
		<img src="<?php echo G5_THEME_IMG_URL?>/ic_remove.svg" alt="">
	</button>
</li>