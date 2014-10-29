<div id="NFBoard_Wrap" class="table-responsive">
	
	<table class="table" summary="게시판 최근글 리스트">
	<tbody>
	<?php
	if($total > 0){
		foreach($latest_res as $i => $bData){
			if($bData->re_level > 0){
				$reicon = "&nbsp;<img src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/btn_reply_icon.gif' alt='답글'/>&nbsp;";
			}else{
				$reicon = "";
			}

			if($brdSet->b_secret_use == 1 && $bData->b_secret_use > 0){
				$secret = "&nbsp;<img src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/icon_secret.gif' style='vertical-align:middle' alt='비밀글' />";
			}else{
				$secret = "";
			}

			$blank = "";
			for($j = 1; $j < $bData->re_level; $j++){
				$blank = $blank."&nbsp;&nbsp;&nbsp;";
			}

			$w_year = substr($bData->write_date, 0, 4);
			$w_month = substr($bData->write_date, 5, 2);
			$w_day = substr($bData->write_date, 8, 2);
			$w_hour = substr($bData->write_date, 11, 2);
			$w_minute = substr($bData->write_date, 14, 2);
			if((time(0) - mktime($w_hour, $w_minute, 0, $w_month, $w_day, $w_year)) / 60 / 60 / 24 < 1){
				$newicon = "&nbsp;&nbsp;<img class='new' alt='새로운글' src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/icon_new.gif' />";
			}else{
				$newicon = "";
			}
			/*
			if($brdSet->b_comment_use == '1'){
				$c_total = $wpdb->get_var("select count(*) from NFB_".$bname."_comment where parent='".$bData->no."'");

				if($c_total > 0){
					$total_comment = "&nbsp;<a class='comment' href='#'>[".$c_total."]</a>";
				}else{
					$total_comment = "";
				}
			}
			*/
			$title = "";
			if(!empty($bData->title)) $title .= $bData->title;

			if(!empty($bData->write_date)){
				$tmp1 = explode(" ", $bData->write_date);
				$tmp2 = explode("-", $tmp1[0]);
				$tmp3 = explode(":", $tmp1[1]);
				$writetime = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
			}
			if($bData->category!="") $title = "[".$bData->category."]&nbsp;".$title;


	?>
	<tr>
		<td class="small title<?php if($bData->use_notice == 1) echo " bold";?>"><span style="float:left;"><?php if(!empty($secret)) echo $secret; else echo "&nbsp;&nbsp;&nbsp;";?></span><a href="<?php echo $curUrl.$link_add?><?php echo build_param($bname, 'view', $bData->no, $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate'])?>"><span style="float:left;max-width:85%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo $title?></span></a><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($newicon)) echo $newicon?></td>
		<td class="date" style="text-align:right;width:100px;"><small><?php if(!empty($writetime)) echo date("Y/m/d", $writetime); else echo "-";?></small></td>
	</tr>
	<?php
		}

	}else{
	?>
	<tr>
		<td colspan="2" class="text-center">등록된 게시물이 없습니다.</td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
</div>