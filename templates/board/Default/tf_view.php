<?php 
if(preg_match("/\bpage_id\b/", $curUrl)) $link_add = "&";
else $link_add = "?";
$share_link = $curUrl.$link_add.build_param($bname, 'view', $_REQUEST['no'], $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate']);
?>
<div id="NFBoard_Wrap" style="width:<?php echo $b_width?>;<?php echo $b_align?>">
	<table class="view-table-set" border="1" cellspacing="0" summary="글 내용">
	<colgroup>
		<col width="120" /><col />
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">제목</th>
		<td><?php if(!empty($brdData->title)) echo $brdData->title?>  <span class='date'><?php if(!empty($brdData->write_date)) echo $brdData->write_date?></span></td>
	</tr>
	<?php if(!empty($brdSet->b_category) && !empty($brdData->category)){?>
	<tr>
		<th scope="row">카테고리</th>
		<td><?php if(!empty($brdData->category)) echo $brdData->category?></td>
	</tr>
	<?php }?>
	<?php if(!empty($file_download1) || !empty($file_download2)){?>
	<tr>
		<th scope="row">첨부파일</th>
		<td>
			<?php if(!empty($file_download1)) echo $file_download1?>
			<?php if(!empty($file_download2)) echo $file_download2?>
		</td>
	</tr>
	<?php }?>
	<?php 
	if($curUserPermision == 'administrator'){
	?>
	<tr>
		<th scope="row">작성자</th>
		<td><?php if(!empty($brdData->writer)) echo $brdData->writer?></td>
	</tr>	
	<?php 
	}else{
		if(empty($brdSet->b_writer_hide) || $brdSet->b_writer_hide != 1){
	?>
	<tr>
		<th scope="row">작성자</th>
		<td><?php if(!empty($brdData->writer)) echo $brdData->writer?></td>
	</tr>	
	<?php 
		}
	}
	?>
	<?php
	if($curUserPermision == 'administrator'){
	?>
	<tr>
		<th scope="row">조회</th>
		<td><?php echo number_format($brdData->hit)?></td>
	</tr>	
	<?php
	}else{
		if(empty($brdSet->b_hit_hide) || $brdSet->b_hit_hide != 1){
	?>
	<tr>
		<th scope="row">조회</th>
		<td><?php echo number_format($brdData->hit)?></td>
	</tr>
	<?php 
		}
	}
	?>
	<tr>
		<td class="cont" colspan="2">
			<?php if(!empty($view_file_result1)) echo $view_file_result1?><?php if(!empty($view_file_result2)) echo $view_file_result2?><?php if(!empty($content)) echo $content?>
			<p style="margin:0px;padding:0px;text-align:right;">
				<?php if(!empty($brdSet->b_facebook_use) && $brdSet->b_facebook_use == 1){?>
				<a href="javascript:;" onclick="NFB_ShareFacebook();"><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/icon_facebook.png" alt="facebook" title="facebook" /></a>
				<?php }?>
				<?php if(!empty($brdSet->b_twitter_use) && $brdSet->b_twitter_use == 1){?>
				<a href="javascript:;" onclick="NFB_ShareTwitter();"><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/icon_twitter.png" alt="twitter" title="twitter" /></a>
				<?php }?>
				<?php if(!empty($brdSet->b_hms_use) && $brdSet->b_hms_use == 1){?>
				<a href="javascript:;" onclick="NFB_ShareHMS();"><img src="<?php echo NFB_WEB?>templates/board/<?php echo $brdSet->b_skin?>/images/icon_hms.png" alt="hms" title="hms" /></a>
				<?php }?>
			</p>
		</td>
	</tr>
	</tbody>
	</table>
	<?php echo $view_delete_hidden?>
	<div>
		<div class="btn_area">
			<button type="button" class="btn btn-default btn-sm" <?php echo $view_list?>>목록</button>
			<button type="button" class="btn btn-default btn-sm" <?php echo $view_modify?>>수정</button></a>
			<button type="button" class="btn btn-default btn-sm" <?php echo $view_delete?>>삭제</button></a>
			<button type="button" class="btn btn-default btn-sm" <?php echo $view_reply?>>답변</button></a>		
			<button type="button" class="btn btn-default btn-sm" <?php echo $view_write?>>글쓰기</button></a>		
		</div>
	</div>

	<?php if(!empty($brdSet->b_comment_use) && $brdSet->b_comment_use == 1){?>
	<div class="comment_group">
		<form name="commentForm" id="commentForm" method="post" action="">
		<input type="hidden" name="actionURL" id="actionURL" value="<?php echo NFB_HOME_URL?>/?NFPage=board-comment-write-process" />
		<input type="hidden" name="share_title" id="share_title" value="<?php echo $brdData->title?>" />
		<input type="hidden" name="share_link" id="share_link" value="<?php echo $share_link?>" />
		<input type="hidden" name="page_id" id="page_id" value="<?php echo $page_id?>" />
		<input type="hidden" name="bname" id="bname" value="<?php echo $bname?>" />
		<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
		<input type="hidden" name="search_chk" id="search_chk" value="<?php echo $_REQUEST['search_chk']?>" />
		<input type="hidden" name="keyfield" id="keyfield" value="<?php echo $_REQUEST['keyfield']?>" />
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_REQUEST['keyword']?>" />
		<input type="hidden" name="cate" id="cate" value="<?php echo $_REQUEST['cate']?>" />
		<input type="hidden" name="page" id="page" value="<?php echo $_REQUEST['page']?>" />
		<input type="hidden" name="no" id="no" value="<?php echo $_REQUEST['no']?>" />
		<input type="hidden" name="cno" id="cno" value="" />
		<input type="hidden" name="sub_cno" id="sub_cno" value="" />
		<input type="hidden" name="sess_id" id="sess_id" value="<?php echo $NFB_SID?>" />
		<?php echo $cert?>

		<p class='total'>댓글 <span class="bold red"><?php echo number_format($comment_cnt)?></span>개</p>
		
		<div class="comment-set well well-sm">
			<div class="input_left">
				<?php if(empty($curUserPermision) || $curUserPermision == 'all'){?>
				<input title="작성자" class="name" type="text" name="cname" id="cname" value="" onkeyup="tag_check(this);fieldCheck(1);" placeholder="작성자" />
				<input title="비밀번호" class="name" type="password" name="cpass" id="cpass" value="" onkeyup="tag_check(this);fieldCheck(1);" placeholder="비밀번호" />
				<?php }else{?>
				<?php if(!empty($current_user->user_firstname) && !empty($current_user->user_lastname)) echo $current_user->user_lastname." ".$current_user->user_firstname; else echo $current_user->user_login;?>
				<?php }?>
			</div>
			<?php if(!empty($brdSet->b_spam) && $brdSet->b_spam != 'NO'){?>
			<div class="input_right">
				<?php if($brdSet->b_spam == 'GD'){?>
					<img src="<?php echo NFB_WEB?>inc/lib/confirm_code.php" style="float:left;width:70px;height:45px;border:1px solid #dddddd;" ondrag="this.blur();" alt="자동생성방지"/>
					<span class="txt">
						<span>스팸글 방지를 위해 왼쪽에 보이는 숫자를 입력해주세요.<span><br />
						<input title="보안번호" class="name" name="string" id="string" type="text" value="" onkeyup="tag_check(this);fieldCheck(1);" style="margin-left:5px;"/>
					</span>
				<?php }?>
			</div>
			<?php }?>
			<div class="input_comment">
				<textarea class="comment" name="cmemo" id="cmemo" rows="5" onkeyup="tag_check(this);fieldCheck(1);"></textarea><span title="등록" class="submit" onclick="commentWrite();">등록</span>
			</div>
			<p class="open_meg" id="error_box" style="display:none;"></p>
		</div>
		
		<?php if(!empty($comment_cnt) && $comment_cnt > 0){?>
		<?php echo $comment_list?>
		<?php }else{?>
		<br />
		<?php }?>

		</form>
	</div>
	<form name="passForm" id="passForm" method="post" action="">
	<input type="hidden" name="passcheck" value="1" />
	</form>
	<?php
	$comment_reply_view = "<div class='comment-set well well-sm'><div class='input_left'>";
	
	if(empty($curUserPermision) || $curUserPermision == 'all'){
		$comment_reply_view .= "<input title='작성자' class='name' type='text' name='reply_cname' id='reply_cname' value='' onkeyup='tag_check(this);fieldCheck(2);' placeholder='작성자' /><input title='비밀번호' class='name' type='password' name='reply_cpass' id='reply_cpass' value='' onkeyup='tag_check(this);fieldCheck(2);' placeholder='비밀번호' />";
	}else{
		if(!empty($current_user->user_firstname) && !empty($current_user->user_lastname)){ 
			$comment_reply_view .= $current_user->user_lastname.' '.$current_user->user_firstname; 
		}else{ 
			$comment_reply_view .= $current_user->user_login;
		}
	}
	$comment_reply_view .= "</div>";
	
	if(!empty($brdSet->b_spam) && $brdSet->b_spam != 'NO'){
		$comment_reply_view .= "<div class='input_right'>";
		if($brdSet->b_spam == 'GD'){
			$comment_reply_view .= "<img src='".NFB_WEB."inc/lib/confirm_code_comment.php' style='float:left;width:70px;height:45px;border:1px solid #dddddd;' ondrag='this.blur();' alt='자동생성방지' />";
			$comment_reply_view .= "<span class='txt'><span>스팸글 방지를 위해 왼쪽에 보이는 숫자를 입력해주세요.<span><br />";
			$comment_reply_view .= "<input title='보안번호' class='name' name='reply_string' id='reply_string' type='text' value='' onkeyup='tag_check(this);fieldCheck(2);' style='margin-left:5px;'/></span>";
		}
		$comment_reply_view .= "</div>";
	}
	$comment_reply_view .= "<div class='input_comment'><textarea class='comment' name='reply_cmemo' id='reply_cmemo' rows='5' cols='65' onkeyup='tag_check(this);fieldCheck(2);'></textarea><span title='입력' class='submit' onclick=\"commentWrite('reply');\">등록</span></div><p class='open_meg' id='reply_error_box' style='padding:5px;display:none;'></p></div>";

	$comment_reply_view = str_replace('"', '\"', $comment_reply_view);
	?>
	<script type="text/javascript">
	function commentReply(cno, obj){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);
		var reply_view = "<?php echo $comment_reply_view?>";
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();
		obj.parent().parent().append(reply_view);
	}
	</script>
	<?php }?>
	<table class="table view-table-set" border="1" cellspacing="0">
	<colgroup>
		<col width="80" /><col />
	</colgroup>
	<tbody>
	<tr>
		<th class="active">이전글</th>
		<td><?php echo $prevlink?>  <?php if(!empty($prev_wdate)){?><span class='date'><?php echo $prev_wdate?></span><?php }?></td>
	</tr>
	<tr>
		<th class="active">다음글</th>
		<td><?php echo $nextlink?>  <?php if(!empty($next_wdate)){?><span class='date'><?php echo $next_wdate?></span><?php }?></td>
	</tr>
	</tbody>
	</table>
	<br />
</div>