<?php
$disabled_item = true;
$pageLists = get_custom_list('page');
$b_latest_page = $data->b_latest_page;
?>
<script type='text/javascript' src='<?php echo NFB_WEB?>inc/js/admin-board.js'></script>
<?php
if(empty($b_no)) $mode = "추가";
else $mode = "수정";
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>보드 <?php echo $mode?></h2>
	<?php
	if(!empty($tMode)){
		echo '<div id="message" class="updated fade"><p><strong>보드 정보를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>
	<form name="boardForm" id="boardForm" method="post">
	<input type="hidden" name="NFB_WEB" id="NFB_WEB" value="<?php echo NFB_WEB?>">
	<input type="hidden" name="NFB_HOME_URL" id="NFB_HOME_URL" value="<?php echo NFB_HOME_URL?>" />
	<input type="hidden" name="b_no" id="b_no" value="<?php if(!empty($b_no)) echo $b_no?>">
	<?php if($disabled_item==true){?>
	<input type="hidden" name="b_type" id="b_type" value="1">
	<?php }?>
	<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">기본설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:150px;">보드 이름&nbsp;</td>
						<td>
							<?php
							if(!empty($data->b_name) && !empty($b_no)){
								echo "<b>".$data->b_name."</b><input type='hidden' name='b_name' id='b_name' value='".$data->b_name."'>";
							
							}else{
								echo "<input type='text' name='b_name' id='b_name' style='width:200px;' maxlength='20' value=''><br/>영문/숫자로만 입력해주세요";
							}
							?>
						</td>
					</tr>
					<tr>
						<td>카테고리설정&nbsp;</td>
						<td>
							<input type="text" name="b_category" style="width:450px;" value="<?php if(!empty($data->b_category)) echo $data->b_category;?>">
							<p>여러 값 입력시 콤마(,)로 구분</p>
						</td>
					</tr>
					<?php if(!empty($b_no)){?>
					<tr>
						<td>최근글&nbsp;</td>
						<td>
							<?php

							?>
							<select name="b_latest_page">
								<option value=""<?php if($b_latest_page=="") echo " selected";?>>페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
									if($b_latest_page!="" && ($pageLists[$p]['id'] == $b_latest_page)) $pageSelect = " selected";
									else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
							<p>보드가 적용된 페이지를 선택해주세요</p>
						</td>
					</tr>
					<?php }?>
				</table>
			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">보드 디자인설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:150px;">스킨 설정&nbsp;</td>
						<td>
							<select name="b_skin" id="b_skin">
								<?php
								$skin_path = NFB_ABS."templates/board/"; 
								$files = array(); 
								
								if($dh = opendir($skin_path)){ 
									while(($read = readdir($dh)) !== false){
										if(is_dir($path.$read)) continue; 
										$files[] = $read; 
									} 
									closedir($dh); 
								} 
								sort($files); 

								foreach($files as $name){ 
									if($name == $data->b_skin) $sSelect = "selected style='color:#ff0000;'";
									else $sSelect = "";
									echo "<option value='$name' $sSelect>$name</option>";
								}
								?>
							</select>
						</td>
					</tr>

					<tr>
						<td>가로 사이즈&nbsp;</td>
						<td>
							<input type="text" name="b_width" id="b_width" style="width:50px;" maxlength="4" value="<?php if(empty($data->b_width)) echo "100"; else echo $data->b_width;?>" onkeydown="checkForNumber();"><span>픽셀 (100 이하일 경우 %로 적용)</span>
						</td>
					</tr>
					<tr>
						<td>정렬&nbsp;</td>
						<td>
							<label><input type="radio" name="b_align" id="b_align" value="C"<?php if(!empty($data->b_align) && $data->b_align == "C") echo " checked"?> style='border:0px;' /> 중앙</label>&nbsp;&nbsp;
							<label><input type="radio" name="b_align" id="b_align" value="L"<?php if(empty($data->b_align) || $data->b_align == "L") echo " checked"?> style='border:0px;' /> 왼쪽</label> &nbsp;&nbsp;
							<label><input type="radio" name="b_align" id="b_align" value="R"<?php if(!empty($data->b_align) && $data->b_align == "R") echo " checked"?> style='border:0px;' /> 오른쪽</label>
						</td>
					</tr>
					<tr>
						<td>페이지당 목록 수&nbsp;</td>
						<td>
							<input type="text" name="b_psize" id="b_psize" style="width:50px;" maxlength="3" value="<?php if(empty($data->b_psize)) echo "20"; else echo $data->b_psize;?>" onkeydown="checkForNumber();"><span>개 (한페이지에 보여질 목록 수)</span>
						</td>
					</tr>
					<tr>
						<td>조회수 비공개&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_hit_hide" id="b_hit_hide" value="1"<?php if(!empty($data->b_hit_hide) && $data->b_hit_hide == 1) echo " checked";?> /> 비공개</label>
						</td>
					</tr>
					<tr>
						<td>작성자 비공개&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_writer_hide" id="b_writer_hide" value="1"<?php if(!empty($data->b_writer_hide) && $data->b_writer_hide == 1) echo " checked";?> /> 비공개</label>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">보드 기능설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:150px;">댓글 기능&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_comment_use" id="b_comment_use" value="1"<?php if(!empty($data->b_comment_use) && $data->b_comment_use == 1) echo " checked";?>> 사용함</label>
						</td>
					</tr>
					<tr>
						<td>비밀글 기능&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_secret_use" id="b_secret_use" value="1" <?php if(!empty($data->b_secret_use) && $data->b_secret_use == 1) echo "checked";?>> 사용함</label>
						</td>
					</tr>
					<tr>
						<td>공지 기능&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_notice_use" id="b_notice_use" value="1" <?php if(!empty($data->b_notice_use) && $data->b_notice_use == 1) echo "checked";?>> 사용함</label>
						</td>
					</tr>
					<tr>
						<td>첨부파일 기능&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_pds_use" value="1" <?php if(!empty($data->b_pds_use) && $data->b_pds_use == 1) echo "checked";?>> 사용함</label> (2개까지 업로드 가능)
						</td>
					</tr>
					<tr>
						<td>첨부파일 용량&nbsp;</td>
						<td>
							<input type="text" name="b_filesize" id="b_filesize" style='width:50px;' maxlength="4" value="<?php if(!empty($data->b_filesize)) echo $data->b_filesize;?>" onkeydown="checkForNumber();"> <span>MB (업로드 용량제한)</span>
						</td>
					</tr>
					<tr>
						<td>비회원 개인정보<br/> 수집 및 활용동의 기능&nbsp;</td>
						<td>
							<label><input type="checkbox" name="b_agree_use" id="b_agree_use" value="1" <?php if(!empty($data->b_agree_use) && $data->b_agree_use == 1) echo "checked";?>> 사용함</label>
						</td>
					</tr>
					<tr>
						<td>에디터 설정&nbsp;</td>
						<td>
							<label><input type="radio" name="b_editor" id="b_editor" value="N" <?php if(empty($data->b_editor) || $data->b_editor == 'N') echo "checked";?>>사용안함</label>&nbsp;&nbsp;
							<label><input type="radio" name="b_editor" id="b_editor" value="W" <?php if(!empty($data->b_editor) && $data->b_editor == 'W') echo "checked";?>>워드프레스 에디터</label>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:middle;">자동글 등록방지기능&nbsp;</td>
						<td style="padding:0px;">
							<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td style="vertical-align:middle;">
									<label><input type='radio' name='b_spam' id='b_spam' value='GD' onfocus="this.blur();" <?php if(!empty($data->b_spam) && $data->b_spam == 'GD') echo "checked";?>>GD방식</label>
								</td>
								<td>
									<img src="<?php echo NFB_WEB?>inc/lib/confirm_code.php" style="border:1px solid #cccccc;">
								</td>
								<td style="vertical-align:middle;">
									<label><input type='radio' name='b_spam' id='b_spam' value='NO' onfocus="this.blur();" <?php if(empty($data->b_spam) || $data->b_spam == 'NO') echo "checked";?>>사용 안함</label>
								</td>
								<td><img src="<?php echo NFB_WEB?>img/no_csrf.gif"></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>필터링&nbsp;</td>
						<td>
							<input type="checkbox" name="b_filter_use" id="b_filter_use" value="1" <?php if(!empty($data->b_filter_use) && $data->b_filter_use == 1) echo "checked";?>> 사용함
						</td>
					</tr>
					<tr>
						<td>필터링목록&nbsp;</td>
						<td>
							<textarea name="b_filter_list" id="b_filter_list" style="width:495px;height:100px;"><?php if($data->b_filter_list) echo $data->b_filter_list; else{ echo "씹새끼,씨팔,시벌,씨벌,떠그랄,좆밥,8억,새끼,개새끼,소새끼,병신,지랄,씨팔,십팔,찌랄,지랄,쌍년,쌍놈,빙신,좆까,니기미,좆같은게,잡놈,벼엉신,바보새끼,추천인,추천id,추천아이디,추천id,추천아이디,추/천/인,쉐이,등신,싸가지,미친놈,미친넘,찌랄,죽습니다,님아,님들아,씨밸넘";}?></textarea>
							<p>여러값 등록시 콤마(,)로 구분</p>
						</td>
					</tr>
					<?php if($disabled_item!=true){?>
					<tr>
						<td>SNS&nbsp;</td>
						<td>
							<input type="checkbox" name="b_facebook_use" id="b_facebook_use" value="1"<?php if(!empty($data->b_facebook_use) && $data->b_facebook_use == 1) echo " checked";?>> Facebook
							<input type="checkbox" name="b_twitter_use" id="b_twitter_use" value="1"<?php if(!empty($data->b_twitter_use) && $data->b_twitter_use == 1) echo " checked";?>> Twitter
							<input type="checkbox" name="b_hms_use" id="b_hms_use" value="1"<?php if(!empty($data->b_hms_use) && $data->b_hms_use == 1) echo " checked";?>> HMS
						</td>
					</tr>
					<tr>
						<td>SEO&nbsp;</td>
						<td>
							<input type="checkbox" name="b_seo_use" id="b_seo_use" onclick="useSEO();" value="1"<?php if(!empty($data->b_seo_use) && $data->b_seo_use == 1) echo " checked";?>> 사용함
						</td>
					</tr>
					<tr id="seo_title_tr" style="display:<?php if(!empty($data->b_seo_use) && $data->b_seo_use == 1) echo ""; else echo "none";?>;">
						<td>SEO 타이틀&nbsp;</td>
						<td>
							<input type="text" name="b_seo_title" id="b_seo_title" style='width:495px;' value="<?php if(!empty($data->b_seo_title)) echo $data->b_seo_title;?>">
						</td>
					</tr>
					<tr id="seo_description_tr" style="display:<?php if(!empty($data->b_seo_use) && $data->b_seo_use == 1) echo ""; else echo "none";?>;">
						<td>SEO 설명&nbsp;</td>
						<td>
							<input type="text" name="b_seo_desc" id="b_seo_desc" style='width:495px;' value="<?php if(!empty($data->b_seo_desc)) echo $data->b_seo_desc;?>">
						</td>
					</tr>
					<tr id="seo_keywords_tr" style="display:<?php if(!empty($data->b_seo_use) && $data->b_seo_use == 1) echo ""; else echo "none";?>;">
						<td>SEO 키워드 (콤마 분리)&nbsp;</td>
						<td>
							<input type="text" name="b_seo_keywords" id-="b_seo_keywords" style='width:495px;' value="<?php if(!empty($data->b_seo_keywords)) echo $data->b_seo_keywords;?>">
						</td>
					</tr>
					<?php }?>
				</table>
			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">보드 권한설정</th>
		</tr>
		<tr>
			<td>
				<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
					<tr>
						<td style="width:70px;vertical-align:middle;">읽기권한&nbsp;</td>
						<td style="padding-right:50px;">
							<select name="b_read_lv" id="b_read_lv" style="width:100%;">
								<option value='all'<?php if(empty($data->b_read_lv) || $data->b_read_lv == 'all') echo " selected";?>>비회원</option>
								<option value='author'<?php if(!empty($data->b_read_lv) && $data->b_read_lv == 'author') echo " selected";?>>회원</option>
								<option value='administrator'<?php if(!empty($data->b_read_lv) && $data->b_read_lv == 'administrator') echo " selected";?>>관리자</option>
							</select>
						</td>
						<td style="width:70px;vertical-align:middle;">쓰기권한&nbsp;</td>
						<td style="padding-right:50px;">
							<select name="b_write_lv" id="b_write_lv" style="width:100%;">
								<option value='all'<?php if(empty($data->b_write_lv) || $data->b_write_lv == 'all') echo " selected";?>>비회원</option>
								<option value='author'<?php if(!empty($data->b_write_lv) && $data->b_write_lv == 'author') echo " selected";?>>회원</option>
								<option value='administrator'<?php if(!empty($data->b_write_lv) && $data->b_write_lv == 'administrator') echo " selected";?>>관리자</option>
							</select>
						</td>
						<td style="width:70px;vertical-align:middle;">댓글권한&nbsp;</td>
						<td style="padding-right:50px;">
							<select name="b_comment_lv" id="b_comment_lv" style="width:100%;">
								<option value='all'<?php if(empty($data->b_comment_lv) || $data->b_comment_lv == 'all') echo " selected";?>>비회원</option>
								<option value='author'<?php if(!empty($data->b_comment_lv) && $data->b_comment_lv == 'author') echo " selected ";?>>회원</option>
								<option value='administrator'<?php if(!empty($data->b_comment_lv) && $data->b_comment_lv == 'administrator') echo " selected";?>>관리자</option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:20px;border-top:1px solid #ddd;">
				<input type="button" class="button-primary" onclick="boardSubmit('<?php if(empty($b_no)) echo "insert"; else echo "modify";?>','<?php echo NFB_BOARD_ADD?>');" value="&nbsp;&nbsp;&nbsp;&nbsp;저장&nbsp;&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;
				<input type="button" class="button-primary" onclick="location.href='<?php echo NFB_BOARD_LIST?>';" value="&nbsp;&nbsp;&nbsp;&nbsp;목록&nbsp;&nbsp;&nbsp;&nbsp;">
			</td>
		</tr>
	</table>
	</form>
</div>