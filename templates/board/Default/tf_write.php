<?
if($_REQUEST['mode']=="write" && $_REQUEST['no']!="") $moveURL = $curUrl.$link_add.build_param($bname, 'view', $_REQUEST['no'], $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate'], $brdData->ref);
else $moveURL = $curUrl;
?>
<form name="writeForm" id="writeForm" method="post" enctype="multipart/form-data">
<input type="hidden" name="actionURL" id="actionURL" value="<?php echo NFB_HOME_URL?>/?NFPage=board-write-process" />
<input type="hidden" name="moveURL" id="moveURL" value="<?php echo $moveURL?>" />
<input type="hidden" name="bname" id="bname" value="<?php echo $bname?>" />
<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
<input type="hidden" name="b_filesize" id="b_filesize" value="<?php echo $brdSet->b_filesize?>" />
<input type="hidden" name="search_chk" id="search_chk" value="<?php echo $_REQUEST['search_chk']?>" />
<input type="hidden" name="keyfield" id="keyfield" value="<?php echo $_REQUEST['keyfield']?>" />
<input type="hidden" name="keyword" id="keyword" value="<?php echo $_REQUEST['keyword']?>" />
<input type="hidden" name="cate" id="cate" value="<?php echo $_REQUEST['cate']?>" />
<input type="hidden" name="page" id="page" value="<?php echo $_REQUEST['page']?>" />
<input type="hidden" name="b_editor" id="b_editor" value="<?php echo $brdSet->b_editor?>" />
<input type="hidden" name="sess_id" id="sess_id" value="<?php echo $NFB_SID?>" />
<?php if(!empty($tMode) && $tMode == "reply"){?>
<input type="hidden" name="ref" id="ref" value="<?php echo $brdData->ref?>" />
<input type="hidden" name="re_step" id="re_step" value="<?php echo $brdData->re_step?>" />
<input type="hidden" name="re_level" id="re_level" value="<?php echo $brdData->re_level?>" />
<input type="hidden" name="no" id="no" value="<?php echo $_REQUEST['no']?>" />
<?php }?>
<?php if(!empty($tMode) && $tMode == "modify"){?>
<input type="hidden" name="no" id="no" value="<?php echo $_REQUEST['no']?>" />
<?php }?>
<?php if(!empty($brdSet->b_category) && count($category_arr) > 0){?>
<input type="hidden" name="use_category" id="use_category" value="1" />
<?php }?>
<?php 
if(empty($curUserPermision) || $curUserPermision == 'all'){
	if($tMode != "reply" || ($tMode == "reply" && $brdData->use_secret != "1")){
?>
<input type="hidden" name="validate_pass" id="validate_pass" value="1" />
<?php
	}
}
?>
<div id="NFBoard_Wrap" style="width:<?php echo $b_width?>;<?php echo $table_align?>">
	<div id="success_box" style="font-size:15px;font-weight:bold;padding:10px 0 10px 0;color:#7ba8ea;display:none;"></div>
	<div class="write-table-set">
		<table border="1" cellspacing="0" summary="게시판의 글쓰기">
		<colgroup>
			<col width="120" /><col />
		</colgroup>
		<tbody>
		<tr>
			<th scope="row">제목</th>
			<td>
				<div class="item">
					<input name="title" id="title" value="<?php if(!empty($title)) echo $title?>" title="제목" class="i_text" type="text" style="width:80%;" onkeyup="tag_check(this);fieldCheck();" />
				</div>
			</td>
		</tr>
		<?php if(!empty($brdSet->b_category) && count($category_arr) > 0){?>
		<tr>
			<th scope="row">카테고리</th>
			<td>
				<div class="item">
					<fieldset class="cate">
						<legend>카테고리영역</legend>
						<?php echo $select_category?>
					</fieldset>
				</div>
			</td>
		<?php }?>
		</tr>
		<?php if((!empty($brdSet->b_secret_use) && $brdSet->b_secret_use == 1) || (!empty($brdSet->b_notice_use) && $brdSet->b_notice_use == 1)){?>
		<tr>
			<th scope="row">옵션</th>
			<td>
				<div class="item">
					<?php if(!empty($brdSet->b_secret_use) && $brdSet->b_secret_use == 1){?>
					<input class="i_check" name="use_secret" id="use_secret" type="checkbox" value="1"<?php if((!empty($brdData->use_secret) && $brdData->use_secret == 1) && ($tMode == "modify" || $tMode == "reply")) echo " checked";?> /><label for="use_secret">비밀글</label> 
					<?php }?>
					<?php if(!empty($brdSet->b_notice_use) && $brdSet->b_notice_use == 1){?>
					<input class="i_check" name="use_notice" id="use_notice" type="checkbox" value="1"<?php if(!empty($brdData->use_notice) && $brdData->use_notice == 1) echo " checked";?> /><label for="use_notice">공지</label>
					<?php }?>
				</div>
			</td>
		</tr>
		<?php }?>
		<tr>
			<th scope="row">작성자</th>
			<td>
				<div class="item">
					<input name="writer" id="writer" value="<?php if(!empty($writer)) echo $writer?>" title="작성자" class="i_text" type="text" onkeyup="tag_check(this);fieldCheck();" />
				</div>
			</td>
		</tr>
		<?php 
		if(empty($curUserPermision) || $curUserPermision == 'all'){
			if($tMode != "reply" || ($tMode == "reply" && $brdData->use_secret != "1")){
		?>
		<tr>
			<th scope="row">비밀번호</th>
			<td>
				<div class="item">
					<input name="pass" id="pass" title="비밀번호" class="i_text" type="password" onkeyup="tag_check(this);fieldCheck();" />
				</div>
			</td>
		</tr>
		<?php 
			}
		}else{
			if((!empty($curUserPermision) && $curUserPermision == 'administrator') && $tMode == "modify" && $brdData->memnum == 0){
		?>
		<tr>
			<th scope="row">비밀번호</th>
			<td>
				<div class="item">
					<input name="pass" id="pass" title="비밀번호" class="i_text" type="password" onkeyup="tag_check(this);" /> 변경시에만 입력
				</div>
			</td>
		</tr>
		<?php 
			}
		}
		?>
		<tr>
			<td colspan="2">
				<div class="item">
					<?php if(!empty($brdSet->b_editor) && $brdSet->b_editor == "N"){?>
					<textarea name="content" id="content" title="내용" class="i_text" style="width:95%;height:150px;" onkeyup="tag_check(this);fieldCheck();"><?php if(!empty($content)) echo $content?></textarea>
					<?php }else if(!empty($brdSet->b_editor) && $brdSet->b_editor == "W"){?>
					<?php
					$quicktags_settings = array('buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close');
					$editor_args = array(
						'textarea_name' => 'content',
						'textarea_rows' => 10,
						'media_buttons' => false,
						'tinymce' => true,
						'quicktags' => $quicktags_settings
					);

					$wp_content = (!empty($content))?$content:"";
					wp_editor($wp_content, 'content1', $editor_args);
					?>
					<?php }?>
				</div>
			</td>
		</tr>
		<?php if(!empty($brdSet->b_pds_use) && $brdSet->b_pds_use == 1){?>
		<tr>
			<th scope="row">첨부파일1</th>
			<td>
				<div class="item">
					<input name="file1" id="file1" title="첨부파일1" type="file" class="pds_file"/>
					<?php if(!empty($view_file1)) echo $view_file1?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">첨부파일2</th>
			<td>
				<div class="item">
					<input name="file2" id="file2" title="첨부파일2" type="file" class="pds_file"/>
					<?php if(!empty($view_file2)) echo $view_file2?>
				</div>
			</td>
		</tr>
		<?php }?>
		<?php if(!empty($brdSet->b_spam) && $brdSet->b_spam != 'NO'){?>
		<tr>
			<th scope="row">인증번호</th>
			<td>
				<div class="item">
					<?php if($brdSet->b_spam == 'GD'){?>
					<img src="<?php echo NFB_WEB?>inc/lib/confirm_code.php" style="float:left;border:1px solid #dddddd;" ondrag="this.blur();" alt="자동생성방지" height="40"/>
					<span class="f11d" style="padding-left:10px;">스팸글 방지를 위해 왼쪽에 보이는 숫자를 입력해주세요.</span><br />
					<input title="보안번호" class="i_text" type="text" name="string" id="string" value="" onfocus="this.value='';return true;" onkeyup="tag_check(this);fieldCheck();" style="margin-left:10px;"/>
					<?php }?>
				</div>
			</td>
		</tr>
		<?php }?>
		<?php if((empty($curUserPermision) || $curUserPermision == 'all') && $brdSet->b_agree_use=='1'){?>
		<tr>
			<th scope="row">개인정보 수집 및<br />활용동의</th>
			<td>
				<div class="item">
					<div class="agree1"><?php if(!empty($brdSet->join_private)) echo nl2br($brdSet->join_private)?></div>
					<input type="checkbox" name="agree1" id="agree1" value="1" class="i_check" onclick="if(this.checked == true) fieldCheck();" />&nbsp;<label for="agree1">개인정보취급방침을 읽었으며 내용에 동의합니다.</label>
				</div>
			</td>
		</tr>
		<?php }?>
		</tbody>
		</table>
	</div>
	<p class="open_meg" id="error_box" style="display:none;"></p>
	<div class="btn_area">
		<button type="button" class="btn btn-primary" onclick="writeSubmit();"><?php if(!empty($tMode) && $tMode == "modify") echo "수정";else echo "확인";?></button>&nbsp;
		<button type="button" class="btn btn-default" onclick="location.href='<?php echo $curUrl?>';">목록</button>
	</div>
</div>
</form>