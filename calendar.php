<?php
	include_once("/_common.php");
	include_once(G5_PARH."/_head.php");

	$now = date("Y-m-d");

	/********** 사용자 설정값 **********/ 
	$startYear        = date("Y"); 
	$endYear        = date( "Y" ) + 4; 

	/********** 입력값 **********/ 
	$year            = ( $_GET['toYear'] )? $_GET['toYear'] : date( "Y" ); 
	$month            = ( $_GET['toMonth'] )? $_GET['toMonth'] : date( "m" ); 
	$doms            = array( "일", "월", "화", "수", "목", "금", "토" ); 

	/********** 계산값 **********/ 
	$mktime            = mktime( 0, 0, 0, $month, 1, $year );      // 입력된 값으로 년-월-01을 만든다 
	$days            = date( "t", $mktime );                        // 현재의 year와 month로 현재 달의 일수 구해오기 
	$startDay        = date( "w", $mktime );                        // 시작요일 알아내기 

	// 지난달 일수 구하기 
	$prevDayCount    = date( "t", mktime( 0, 0, 0, $month, 0, $year ) ) - $startDay + 1; 

	$nowDayCount    = 1;                                            // 이번달 일자 카운팅 
	$nextDayCount    = 1;                                          // 다음달 일자 카운팅 

	// 이전, 다음 만들기 
	$prevYear        = ( $month == 1 )? ( $year - 1 ) : $year; 
	$prevMonth        = ( $month == 1 )? 12 : ( $month - 1 ); 
	$nextYear        = ( $month == 12 )? ( $year + 1 ) : $year; 
	$nextMonth        = ( $month == 12 )? 1 : ( $month + 1 ); 

	// 출력행 계산 
	$setRows	= ceil( ( $startDay + $days ) / 7 ); 
?>

<table style="border-collapse:collapse;" class="cal_top_table"> 
    <tr> 
        <td> 
        <button onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>?toYear=<?php echo $prevYear?>&toMonth=<?php echo $prevMonth?>'" class="btn_next btn_next2"> < </button>

        <?php echo $year?>년 <?php echo $month?>월 

        <button onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>?toYear=<?php echo $nextYear?>&toMonth=<?php echo $nextMonth?>'" class="btn_next btn_next1"> > </button>
        </td> 
    </tr> 
</table>

<table cellpadding=0 cellspacing=0 class="rsrv_tbl"> 
    <tr> 
        <?php for( $i = 0; $i < count( $doms ); $i++ ) { ?> 
        <td align="center"><?php echo $doms[$i]?></td> 
        <?php } ?> 
    </tr> 

	<?php for( $rows = 0; $rows < $setRows; $rows++ ) { ?> 
    <tr> 
        <?php for( $cols = 0; $cols < 7; $cols++ ){ 
			// 셀 인덱스 만들자 
            $cellIndex    = ( 7 * $rows ) + $cols; 
        ?> 
            <?php
            // 이번달이라면 
            if ( $startDay <= $cellIndex && $nowDayCount <= $days ) {
				$date2 = $year."-".str_pad($month, 2, "0", STR_PAD_LEFT)."-".str_pad($nowDayCount, 2, "0", STR_PAD_LEFT);				
				$font_color = "";
				if ( date( "w", mktime( 0, 0, 0, $month, $nowDayCount, $year ) ) == 6 ) { $font_color = "blue"; }
				if ( date( "w", mktime( 0, 0, 0, $month, $nowDayCount, $year ) ) == 0 ) { $font_color = "red"; }
			?>
            <td align="center" <?php if($date2 == date("Y-m-d")){?>class="now_Day"<?php }?>>
				<b><font color="<?php echo $font_color?>"><?php echo $nowDayCount++?></font></b>
            </td> 
            
            <?php // 이전달이라면 
			 } else if ( $cellIndex < $startDay ) { 
				$prevDate = $prevYear."-".sprintf("%02d", $prevMonth)."-".sprintf("%02d", $prevDayCount);
			?> 
            <td align="center">
				<font color="gray"><b><?php echo $prevDayCount++?></b></font> 
            </td> 
            
            <?php // 다음달 이라면             
				} else if ( $cellIndex >= $days ) { 
					$nextDate = $nextYear."-".sprintf("%02d", $nextMonth)."-".sprintf("%02d", $nextDayCount);
			?> 
			<td align="center">
				<font color="gray"><b><?php echo $nextDayCount++?></b></font> 
            </td> 
            <?php } ?>
		<?php } ?>
    </tr> 
    <?php } ?> 
</table>

<?php
	include_once(G5_PARH."/_tail.php");
?>