<?php
$disabled_item = true;
$pageLists = get_custom_list('page');
$login_page = get_option("NFB_login_page");
$join_page = get_option("NFB_join_page");
$id_find_page = get_option("NFB_id_find_page");
$pw_find_page = get_option("NFB_pw_find_page");
$leave_page = get_option("NFB_leave_page");
?>
<script type='text/javascript' src='<?php echo NFB_WEB?>inc/js/admin-setup.js'></script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>환경설정</h2>
	<?php
	if(!empty($edit_mode) && $edit_mode == "edit"){
		echo '<div id="message" class="updated fade"><p><strong>정보를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>
	<form method="post" id="setupFrom" name="setupFrom" enctype="multipart/form-data" action="<?php echo $EDIT_CONFIG_URL?>">
	<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
	<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">페이지설정</th>
		</tr>
		<tr>
			<td>
				<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
					<colgroup>
						<col width="200"><col width="200"><col width="">
					</colgroup>
					<thead>
					<tr>
						<th>페이지명</th>
						<th>Shortcode</th>
						<th>Shortcode 적용된 페이지를 선택하세요</th>
					</tr>
					</head>
					<tbody>
					<tr class="alternate">
						<td>로그인</td>
						<td><font style="color:#929292;">[NFB_LOGIN]</font></td>
						<td>
							<select name="login_page">
								<option value=""<?php if(empty($login_page)) echo " selected";?>>적용 페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
									if(!empty($login_page) && ($pageLists[$p]['id'] == $login_page)) $pageSelect = " selected";
									else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>회원가입/수정</td>
						<td><font style="color:#929292;">[NFB_JOIN]</font></td>
						<td>
							<select name="join_page">
								<option value=""<?php if(empty($join_page)) echo " selected";?>>적용 페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
									if(!empty($join_page) && ($pageLists[$p]['id'] == $join_page)) $pageSelect = " selected";
									else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="alternate">
						<td>아이디찾기</td>
						<td><font style="color:#929292;">[NFB_ID_FIND]</font></td>
						<td>
							<select name="id_find_page">
								<option value=""<?php if(empty($id_find_page)) echo " selected";?>>적용 페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
									if(!empty($id_find_page) && ($pageLists[$p]['id'] == $id_find_page)) $pageSelect = " selected";
									else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>비밀번호찾기</td>
						<td><font style="color:#929292;">[NFB_PW_FIND]</font></td>
						<td>
							<select name="pw_find_page">
								<option value=""<?php if(empty($pw_find_page)) echo " selected";?>>적용 페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
									if(!empty($pw_find_page) && ($pageLists[$p]['id'] == $pw_find_page)) $pageSelect = " selected";
									else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="alternate">
						<td>회원탈퇴</td>
						<td><font style="color:#929292;">[NFB_LEAVE]</font></td>
						<td>
							<select name="leave_page">
								<option value=""<?php if(empty($leave_page)) echo " selected";?>>적용 페이지 선택</option>
								<?php 
								for($p = 0; $p < sizeof($pageLists); $p++){
								if(!empty($leave_page) && ($pageLists[$p]['id'] == $leave_page)) $pageSelect = " selected";
								else $pageSelect = "";
								?>
								<option value="<?php echo $pageLists[$p]['id']?>"<?php echo $pageSelect?>><?php echo $pageLists[$p]['name']?></option>
								<?	
								}
								?>
							</select>
						</td>
					</tr>
					</tbody>
				</table>

			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">회원 디자인설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:150px;">스킨</td>
						<td>
							<select name="skinname">
								<?php
								$skin_path = NFB_ABS."templates/member/"; 
								$files = array(); 
								
								if($dh = opendir($skin_path)){ 
									while(($read = readdir($dh)) !== false){
										if( "." == $read || ".." == $read ) continue;
										if(is_dir($skin_path.$read)==true) $files[] = $read; 
									} 
									closedir($dh); 
								} 
								sort($files); 

								foreach($files as $name){ 
									if($name == $config['skinname']) $sSelect = " selected style='color:#ff0000;'";
									else $sSelect = "";
									echo "<option value='$name'$sSelect>$name</option>";
								}
								?>
							</select>
						</td>
					</tr>

					<tr>
						<td>가로 사이즈</td>
						<td>
							<input type="text" name="table_width" value="<?php echo $config['table_width'];?>" style="width:50px;" onkeydown="checkForNumber();" maxlength="4"><span>%</span>
						</td>
					</tr>
					<tr>
						<td>정렬</td>
						<td>
							<input type="radio" name="table_align" value="C"<?php if(!empty($config['table_align']) && $config['table_align'] == "C") echo " checked"?> style='border:0px;' /> 중앙 &nbsp;
							<input type="radio" name="table_align" value="L"<?php if(empty($config['table_align']) || $config['table_align'] == "L") echo " checked"?> style='border:0px;' /> 왼쪽 &nbsp;
							<input type="radio" name="table_align" value="R"<?php if(!empty($config['table_align']) && $config['table_align'] == "R") echo " checked"?> style='border:0px;' /> 오른쪽
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">회원가입 항목설정</th>
		</tr>
		<tr>
			<td>

				<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
					<colgroup>
						<col width="200"><col width=""><col width="">
					</colgroup>
					<thead>
					<tr>
						<th>항목</th>
						<th>사용여부</th>
						<th>필수여부</th>
					</tr>
					</head>
					<tbody>
					<tr class="alternate">
						<td>이름</td>
						<td>
							<input type="checkbox" name="use_name" value="1"<?php if($config['use_name'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_name" value="1"<?php if($config['validate_name'] == 1) echo " checked"?> />
						</td>
					</tr>
					<tr>
						<td>주소</td>
						<td>
							<input type="checkbox" name="use_addr" value="1"<?php if($config['use_addr'] == 1) echo " checked"?> onclick="addrChange();"/>
						</td>
						<td>
							<input type="checkbox" name="validate_addr" value="1"<?php if($config['validate_addr'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					<tr id="addr_form1" style="display:<?php echo ($config['use_addr'] == 1)?"":"none"?>">
						<td>도로명주소 API 종류</td>
						<td colspan="2">
							<label><input type="checkbox" name="use_zipcode_api" value="1" onclick="addrChange();"<?php if($config['use_zipcode_api'] == 1) echo " checked"?> /> 도로명주소검색 사용</label>
							&nbsp;&nbsp;
							<label><input type="radio" name="zipcode_api_module" value="1" onclick="addrChange();"<?php if(!empty($config['zipcode_api_module']) && $config['zipcode_api_module'] == 1) echo " checked"?> /> 공공데이터포털 API</label>
							&nbsp;&nbsp;
							<label><input type="radio" name="zipcode_api_module" value="2" onclick="addrChange();"<?php if(!empty($config['zipcode_api_module']) && $config['zipcode_api_module'] == 2) echo " checked"?> /> Daum 우편번호 API</label>
						</td>
					</tr>
					<tr id="addr_form2" style="display:<?php echo ($config['use_addr'] == 1 && $config['zipcode_api_module'] == 1)?"":"none"?>">
						<td>공공데이터포털 API Key</td>
						<td colspan="2">
							<input type="text" name="zipcode_api_key" id="zipcode_api_key" style="width:400px;" value="<?php echo $config['zipcode_api_key']?>" /> 
							<a href="https://www.data.go.kr/#/L2NvbW0vY29tbW9uU2VhcmNoL29wZW5hcGkkQF4wMjIkQF5wYmxvbnNpcFJlc3JjZVBrPXVkZGk6YmRhNzM0ZWYtZWZjYi00YzhiLWIxYmItOTUwN2RlMjM3N2NmJEBec2tpcFJvd3M9MCRAXm1heFJvd3M9MTA=" target="_blank"><input type="button" class="button-secondary" value="활용신청 바로가기"></a>
						</td>
					</tr>
					<tr class="alternate">
						<td>생년월일</td>
						<td>
							<input type="checkbox" name="use_birth" value="1"<?php if($config['use_birth'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_birth" value="1"<?php if($config['validate_birth'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					<tr>
						<td>전화번호</td>
						<td>
							<input type="checkbox" name="use_phone" value="1"<?php if($config['use_phone'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_phone" value="1"<?php if($config['validate_phone'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					<tr class="alternate">
						<td>휴대전화번호</td>
						<td>
							<input type="checkbox" name="use_hp" value="1"<?php if($config['use_hp'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_hp" value="1"<?php if($config['validate_hp'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					<tr>
						<td>성별</td>
						<td>
							<input type="checkbox" name="use_sex" value="1"<?php if($config['use_sex'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_sex" value="1"<?php if($config['validate_sex'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					<tr class="alternate">
						<td>직업</td>
						<td>
							<input type="checkbox" name="use_job" value="1"<?php if($config['use_job'] == 1) echo " checked"?> />
						</td>
						<td>
							<input type="checkbox" name="validate_job" value="1"<?php if($config['validate_job'] == 1) echo " checked"?> /><br />
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">기타 환경설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:150px;">아이디 최소길이</td>
						<td>
							<input type="text" name="id_min_len" style="width:30px;" maxlength="2" value="<?php echo $config['id_min_len'];?>" onkeydown="checkForNumber();"><span>자 이상</span>
						</td>
					</tr>
					<tr>
						<td>비밀번호 최소길이</td>
						<td>
							<input type="text" name="pass_min_len" style="width:30px;" maxlength="2" value="<?php echo $config['pass_min_len'];?>" onkeydown="checkForNumber();"><span>자 이상</span>
						</td>
					</tr>
					<tr>
						<td>가입불가 아이디</td>
						<td>
							<input type="text" name="join_not_id" style="width:500px;" value="<?php echo $config['join_not_id']?>" /><br />
							여러 아이디 등록시 콤마(,)로 구분하여 입력하세요.
						</td>
					</tr>
					<tr>
						<td>가입완료 페이지 URL</td>
						<td>
							<input type="text" name="join_redirect" style="width:500px;" value="<?php echo $config['join_redirect']?>" /><br />
							회원가입후 이동할 페이지 경로를 입력해주세요.
						</td>
					</tr>
					<tr>
						<td>회원가입 완료메일</td>
						<td>
							<label><input type="checkbox" name="use_join_email" value="1"<?php if($config['use_join_email'] == 1) echo " checked"?> /> 사용함</label>
						</td>
					</tr>
					<tr>
						<td>메일설정</td>
						<td>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td>메일 로고</td>
									<td>
										<input type='text' id='mail_logo' name='mail_logo' value='<?php $config['mail_logo']?>' style='width:400px;' readonly />&nbsp;<input class='button-secondary' onclick="upload_img('mail_logo');" type='button' value='찾아보기'  />
										<span style="display:inline-block;padding-top:5px;">
											<?php
											if(!empty($config['mail_logo'])) $img_view = "<img src='".$config['mail_logo']."' class='fileimg'>";
											else $img_view = "";
											?>
											<?php echo $img_view?>
											<?php echo ($img_view)?"<input type='checkbox' name='mail_logo_del' id='mail_logo_del' value='1' style='border:0px;'><label for='mail_logo_del'>삭제 </label>":""?>
										</span>
									</td>
								</tr>
								<tr>
									<td>보낸사람 이메일</td>
									<td>
										<input type="text" name="from_email" style="width:400px;" value="<?php if(!empty($config['from_email'])) echo $config['from_email'];else echo get_option('admin_email');?>" />
									</td>
								</tr>
								<tr>
									<td>보낸사람 이름</td>
									<td>
										<input type="text" name="from_name" style="width:400px;" value="<?php if(!empty($config['from_name'])) echo $config['from_name'];else echo get_option('blogname');?>" />
									</td>
								</tr>
								<tr>
									<td>메일 제목</td>
									<td>
										<input type="text" name="join_email_title" style="width:400px;" value="<?php if(!empty($config['join_email_title'])) echo $config['join_email_title'];else echo get_option('blogname')." - 회원가입을 축하드립니다";?>" />
									</td>
								</tr>
								<tr>
									<td>메일 내용</td>
									<td>
										<textarea name="join_email_content" style="width:400px;height:100px;"><?php if(!empty($config['join_email_content'])) echo $config['join_email_content'];else echo "{USER_ID}님, {SITE_NAME}에 회원가입을 축하드립니다.\n\n감사합니다.";?></textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0" style="width:500px">
											<colgroup>
												<col width=""><col width="">
											</colgroup>
											<thead>
											<tr>
												<th>치환코드</th>
												<th>내용</th>
											</tr>
											</head>
											<tbody>
											<tr class="alternate">
												<td>{SITE_NAME}</td>
												<td>홈페이지 이름</td>
											</tr>
											<tr>
												<td>{USER_ID}</td>
												<td>회원 아이디</td>
											</tr>
											<tr class="alternate">
												<td>{USER_NAME}</td>
												<td>회원 이름</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>이용약관</td>
						<td>
							<textarea name="join_agreement" style="width:500px;height:100px;"><?php echo $config['join_agreement']?></textarea>
						</td>
					</tr>
					<tr>
						<td>개인정보취급방침</td>
						<td>
							<textarea name="join_private" style="width:500px;height:100px;"><?php echo $config['join_private']?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php if($disabled_item!=true){?>
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">SSL설정</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td>사용설정</td>
						<td>
							<input type="checkbox" name="use_ssl" id="use_ssl" value="1"<?php if($config['use_ssl'] == 1) echo " checked"?> /> 사용함
						</td>
					</tr>
					<tr>
						<td>적용 도메인</td>
						<td>
							http:// <input type="text" name="ssl_domain" id="ssl_domain" style="width:350px;" value="<?php echo $config['ssl_domain']?>" /><br />
							적용 도메인과 SSL 적용 도메인이 다를 경우, 오류가 발생합니다.<br />
							SSL 도메인의 기본 포트번호는 '443' 입니다.<br />
							http://서버접속주소/~계정아이디 형식은 적용되지 않습니다.
						</td>
					</tr>
					<tr>
						<td>포트번호</td>
						<td>
							<input type="text" name="ssl_port" id="ssl_port" style="width:120px;" value="<?php echo $config['ssl_port']?>" onkeydown="checkForNumber();" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td style="text-align:center;padding:20px;border-top:1px solid #ddd;">
				<input type="button" class="button-primary" onclick="saveSetup();" value="&nbsp;&nbsp;&nbsp;&nbsp;저장&nbsp;&nbsp;&nbsp;&nbsp;">
			</td>
		</tr>
	</table>
	</form>
</div>