<div id="NFBoard_Wrap" style="width:<?php echo $b_width?>;<?php echo $b_align?>">
	<div class="list-search-set">
		<?if(!$detect->isMobile()) {?>
		<div class="totalcount">
			<img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/icon_total.gif"> Total : <?php echo number_format($total)?>
		</div>
		<?}else{$select_category="";}?>
		<div class="srch">
			<div>검색영역</div>
			<form method="post" name="listSearchForm" id="listSearchForm" onsubmit="return false;">
			<input type="hidden" name="actionURL" id="actionURL" value="<?php echo NFB_HOME_URL?>/?NFPage=board-search-process" />
			<input type="hidden" name="moveURL" id="moveURL" value="<?php echo $curUrl.$link_add?>" />
			<input type="hidden" name="bname" value="<?php echo $bname?>" />
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page']?>" />
			<input type="hidden" name="cate" value="<?php echo $_REQUEST['cate']?>" />
			<input type="hidden" name="search_chk" value="1" />
			<?php echo $select_category?>
			<select name="keyfield">
				<option value="title"<?php if($_REQUEST['keyfield'] == "title") echo " selected";?>>제목</option>
				<option value="content"<?php if($_REQUEST['keyfield'] == "content") echo " selected";?>>내용</option>
				<option value="writer"<?php if($_REQUEST['keyfield'] == "writer") echo " selected";?>>작성자</option>
			</select>
			<input title="검색어" class="keyword" accesskey="s" type="text" name="keyword" id="keyword" value="<?php echo $_REQUEST['keyword']?>" style="width:120px;"/>
			<a href="javascript:listSearchSubmit();"><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/btn_search.gif" alt="검색" border="0" align="absmiddle" /></a>
			</form>
		</div>
	</div>
	<form name="listForm" method="post">
	<table class="list-table-set" style="width:100%;" border="0" cellspacing="0" summary="게시판의 글 리스트">
	<caption>게시판 리스트</caption>
	<?php 
	// 모바일, 태블릿 접속
	$tbl_cols = 2;
	if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPhone') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'Android') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false){
	?>
	<colgroup>
		<?php if($curUserPermision == 'administrator'){?><col width="30" /><?php }?>
		<col />
		<col width="85" />
	</colgroup>
	<thead>
	<tr>
		<?php 
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col"><input type="checkbox" name="list_select" id="list_select" border="0" /></th>
		<?php 
		}
		?>
		<th scope="col">제목</th>
		<th scope="col">작성일</th>
	</tr>
	<?php 
	}else{
		// PC 접속
		$tbl_cols = 3;
	?>
	<colgroup>
		<?php if($curUserPermision == 'administrator'){?><col width="30" /><?php }?>
		<col width="50" />
		<col />
		<?php if($brdSet->b_pds_use == 1){?><col width="60" /><?php }?>
		<?
		if($curUserPermision == 'administrator'){
		?>
		<col width="115" />
		<?php
		}else{
			if(empty($brdSet->b_writer_hide) || $brdSet->b_writer_hide != 1){
		?>
		<col width="115" />
		<?php 
			}
		}
		?>
		<col width="85" />
		<?php 
		if($curUserPermision == 'administrator'){
		?>
		<col width="60" />
		<?php
		}else{
			if(empty($brdSet->b_hit_hide) || $brdSet->b_hit_hide != 1){
		?>
		<col width="60" />
		<?php 
			}
		}
		?>
	</colgroup>
	<thead>
	<tr>
		<?php 
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col"><input type="checkbox" name="list_select" id="list_select" border="0" /></th>
		<?php 
		}
		?>
		<th scope="col">번호</th>
		<th scope="col">제목</th>
		<?php
		if($brdSet->b_pds_use == 1){
			$tbl_cols++;
		?>
		<th scope="col">첨부</th>
		<?php 
		}

		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col">작성자</th>
		<?php
		}else{
			if(empty($brdSet->b_writer_hide) || $brdSet->b_writer_hide != 1){
				$tbl_cols++;
		?>
		<th scope="col">작성자</th>
		<?php
			}
		}
		?>
		<th scope="col">작성일</th>
		<?php
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col">조회</th>
		<?
		}else{
			if(empty($brdSet->b_hit_hide) || $brdSet->b_hit_hide != 1){
				$tbl_cols++;
		?>
		<th scope="col">조회</th>
		<?php 
			}
		}
		?>
	</tr>
	<?php 
	}
	?>
	</thead>
	<tbody>
	<?php
	if($total > 0){
		foreach($result as $i => $bData){
			if($bData->re_level > 0) $reicon = "&nbsp;<img src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/btn_reply_icon.gif' alt='답글'/>&nbsp;";
			else $reicon = "";

			if($brdSet->b_secret_use == 1 && $bData->b_secret_use > 0) $secret = "&nbsp;<img src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/icon_secret.gif' style='vertical-align:middle' alt='비밀글' />";
			else $secret = "";

			if(!empty($bData->file1) || !empty($bData->file2)) $addfile = "<img src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/icon_file.gif' alt='첨부파일' />";
			else $addfile = "";

			$blank = "";
			for($j = 1; $j < $bData->re_level; $j++){$blank = $blank."&nbsp;&nbsp;&nbsp;";}

			$w_year = substr($bData->write_date, 0, 4);
			$w_month = substr($bData->write_date, 5, 2);
			$w_day = substr($bData->write_date, 8, 2);
			$w_hour = substr($bData->write_date, 11, 2);
			$w_minute = substr($bData->write_date, 14, 2);
			if((time(0) - mktime($w_hour, $w_minute, 0, $w_month, $w_day, $w_year)) / 60 / 60 / 24 < 1) $newicon = "&nbsp;&nbsp;<img class='new' alt='새로운글' src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/icon_new.gif' />";
			else $newicon = "";
			
			if($brdSet->b_comment_use == '1'){
				$c_total = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_".$bname."_comment where parent=%s", $bData->no));

				if($c_total > 0) $total_comment = "&nbsp;<a class='comment' href='#'>[".$c_total."]</a>";
				else $total_comment = "";
			}

			$title = "";
			if(!empty($blank)) $title .= $blank;
			if(!empty($reicon)) $title .= $reicon;
			if(!empty($bData->title)) $title .= $bData->title;

			if(!empty($bData->write_date)){
				$tmp1 = explode(" ", $bData->write_date);
				$tmp2 = explode("-", $tmp1[0]);
				$tmp3 = explode(":", $tmp1[1]);
				$writetime = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
			}
			if($bData->category!="") $title = "[".$bData->category."]&nbsp;".$title;

			// 모바일, 태블릿 접속
			if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPhone') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'Android') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false){
	?>
	<tr>
		<?php if($curUserPermision == 'administrator'){?>
		<td><input type="checkbox" name="check[]" id="check[]" class="bno_chk" value="<?php echo $bData->no?>" /></td>
		<?php }?>
		<td class="title<?php if($bData->use_notice == 1) echo " bold";?>"><span style="float:left;"><?php if(!empty($secret)) echo $secret; else echo "&nbsp;&nbsp;&nbsp;";?></span><a href="<?php echo $curUrl.$link_add?><?php echo build_param($bname, 'view', $bData->no, $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate'])?>"><span style="float:left;max-width:85%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo $title?></span></a><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($newicon)) echo $newicon?></td>
		<td class="date<?php if($bData->use_notice == 1) echo " bold";?>"><?php if(!empty($writetime)) echo date("Y/m/d", $writetime); else echo "-";?></td>
	</tr>
	<?php
			}else{
	?>
	<tr>
		<?php if($curUserPermision == 'administrator'){?>
		<td><input type="checkbox" name="check[]" id="check[]" class="bno_chk" value="<?php echo $bData->no?>" /></td>
		<?php }?>
		<td class="num<?php if($bData->use_notice == 1) echo " bold";?>">
			<?php 
			if($bData->use_notice == 1) echo "<img alt='공지사항' src='".NFB_WEB."templates/board/".$brdSet->b_skin."/images/notice_icon.jpg' />";
			else echo number_format($num);
			?>
		</td>
		<td class="title<?php if($bData->use_notice == 1) echo " bold";?>"><span style="float:left;"><?php if(!empty($secret)) echo $secret; else echo "&nbsp;&nbsp;&nbsp;";?></span><a href="<?php echo $curUrl.$link_add?><?php echo build_param($bname, 'view', $bData->no, $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate'])?>"><span style="float:left;max-width:85%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo $title?></span></a><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($newicon)) echo $newicon?></td>
		<?php if($brdSet->b_pds_use == 1){?>
		<td class="frm"><?php echo $addfile?></td>
		<?php }?>
		<?php
		if($curUserPermision == 'administrator'){
		?>
		<td class="<?php if($bData->use_notice == 1) echo " bold";?>"><span style="max-width:90%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo $bData->writer?></span></td>
		<?
		}else{
			if(empty($brdSet->b_writer_hide) || $brdSet->b_writer_hide != 1){
		?>
		<td class="<?php if($bData->use_notice == 1) echo " bold";?>"><span style="max-width:90%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo $bData->writer?></span></td>
		<?php 
			}
		}
		?>
		<td class="date<?php if($bData->use_notice == 1) echo " bold";?>"><?php if(!empty($writetime)) echo date("Y/m/d", $writetime); else echo "-";?></td>
		<?php
		if($curUserPermision == 'administrator'){
		?>
		<td class="hit<?php if($bData->use_notice == 1) echo " bold";?>"><?php echo number_format($bData->hit)?></td>
		<?
		}else{
			if(empty($brdSet->b_hit_hide) || $brdSet->b_hit_hide != 1){
		?>
		<td class="hit<?php if($bData->use_notice == 1) echo " bold";?>"><?php echo number_format($bData->hit)?></td>
		<?php 
			}
		}
		?>
	</tr>
	<?php
			}
			$num--;
		}

	}else{
	?>
	<tr><td colspan="<?php echo $tbl_cols?>" align="center" style="text-align:center;">등록된 게시물이 없습니다.</td></tr>
	<?php 
	}
	?>
	</tbody>
	</table>
	</form>
	<div>
		<div class="btn_area">
			<?php if($curUserPermision == 'administrator'){?>
			<button type="button" class="btn btn-default btn-sm" <?php echo $list_move?>>이동</button>
			<button type="button" class="btn btn-default btn-sm" <?php echo $list_copy?>>복사</button>
			<button type="button" class="btn btn-default btn-sm" <?php echo $list_delete?>>삭제</button>
			<?php }?>
			<button type="button" class="btn btn-default btn-sm" <?php echo $list_write?>>글쓰기</button>
		</div>
	</div>
	<?php if($total > 0){?>
	<div class="paginate">
		<?php echo $pagelink_first?><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/btn_page_prev1.gif" title="처음" align="absmiddle" /></a>
		<?php echo $pagelink_pre?><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/btn_page_prev.gif" title="이전" align="absmiddle" /></a>
		<?php echo $pagelink_view?>
		<?php echo $pagelink_next?><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/btn_page_next.gif" title="다음" align="absmiddle" /></a>
		<?php echo $pagelink_last?><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/btn_page_next1.gif" title="맨끝" align="absmiddle" /></a>
	</div>
	<?php }?>
</div>