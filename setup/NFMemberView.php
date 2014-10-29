<?php if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){  /* Daum 우편번호 API */?>
<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.js"></script>
<?php }?>
<script type='text/javascript' src='<?php echo NFB_WEB?>inc/js/admin-member.js'></script>
<?php
if(!empty($result->jumin)) $jumin_arr = explode("-", $result->jumin);
if(!empty($result->birth)) $birth_arr = explode("-", $result->birth);
if(!empty($result->phone)) $phone_arr = explode("-", $result->phone);
if(!empty($result->hp)) $hp_arr = explode("-", $result->hp);
?>
<div class="wrap">

	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>회원정보 수정</h2>
	<?php if($edit_mode == "edit") echo '<div id="message" class="updated fade"><p><strong>정보를 정상적으로 저장하였습니다.</strong></p></div>';?>
	<form method="post" id="joinForm" name="joinForm" enctype="multipart/form-data">
	<input type="hidden" name="uno" id="uno" value="<?php echo $_GET['uno']?>" />
	<input type="hidden" name="mode" id="mode" value="edit" />
	<input type="hidden" name="actionURL" id="actionURL" value="<?php echo $EDIT_USER_URL?>" />
	<input type="hidden" name="NFB_WEB" id="NFB_WEB" value="<?php echo NFB_WEB?>" />
	<input type="hidden" name="pass_min_len" id="pass_min_len" value="<?php echo $config['pass_min_len']?>" />
	<input type="hidden" name="use_zipcode_api" id="use_zipcode_api" value="<?php echo $config['use_zipcode_api']?>" />
	<input type="hidden" name="zipcode_api_module" id="zipcode_api_module" value="<?php echo $config['zipcode_api_module']?>" />
	<input type="hidden" name="require_name" id="require_name" value="<?php echo $config['use_name'].$config['validate_name']?>" />
	<input type="hidden" name="require_birth" id="require_birth" value="<?php echo $config['use_birth'].$config['validate_birth']?>" />
	<input type="hidden" name="require_sex" id="require_sex" value="<?php echo $config['use_sex'].$config['validate_sex']?>" />
	<input type="hidden" name="require_addr" id="require_addr" value="<?php echo $config['use_addr'].$config['validate_addr']?>" />
	<input type="hidden" name="require_phone" id="require_phone" value="<?php echo $config['use_phone'].$config['validate_phone']?>" />
	<input type="hidden" name="require_hp" id="require_hp" value="<?php echo $config['use_hp'].$config['validate_hp']?>" />
	<input type="hidden" name="require_job" id="require_job" value="<?php echo $config['use_job'].$config['validate_job']?>" />
	<input type="hidden" name="skinname" id="skinname" value="<?php echo get_option("NFB_skin")?>">


	<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
		<tr class="alternate">
			<th style="border-bottom:1px dotted #bbb;font-weight:bold;">회원정보</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="width:100px;">아이디</td>
						<td><?php echo $result->user_id?></td>
					</tr>
					<tr>
						<td>비밀번호</td>
						<td><input type="password" name="pass" id="pass" style="width:180px;" value="" /> <span>(변경시에만 입력)</span></td>
					</tr>
					<tr>
						<td>비밀번호 확인</td>
						<td><input type="password" name="repass" id="repass" style="width:180px;" value="" /></td>
					</tr>
					<tr>
						<td>이름</td>
						<td><input type="text" name="name" id="name" style="width:180px;" value="<?php echo $result->name?>" /></td>
					</tr>
					<tr>
						<td>생년월일</td>
						<td>
							<input type="text" name="birth_year" id="birth_year" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $birth_arr[0]?>" /> <span>년<span> &nbsp;
							<input type="text" name="birth_month" id="birth_month" style="width:30px;" maxlength="2" onkeydown="checkForNumber();" value="<?php echo $birth_arr[1]?>" /> <span>월</span> &nbsp;
							<input type="text" name="birth_day" id="birth_day" style="width:30px;" maxlength="2" onkeydown="checkForNumber();" value="<?php echo $birth_arr[2]?>" /> <span>일</span> &nbsp;
						</td>
					</tr>
					<tr>
						<td>성별</td>
						<td>
							<label><input type="radio" name="sex" value="1"<?php if($result->sex == 1 || empty($result->sex)) echo " checked";?>> 남자</label>&nbsp;&nbsp;
							<label><input type="radio" name="sex" value="2"<?php if($result->sex == 2) echo " checked";?>> 여자</label>
						</td>
					</tr>
					<tr>
						<td>우편번호</td>
						<td>
							<?php if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){?>
							<input type="text" name="zipcode" id="zipcode" value="<?php if(!empty($result->zipcode)) echo $result->zipcode?>" style="width:70px;text-align:center;"/> <a href="javascript:;" onclick="openDaumPostcode();"><input class='button-secondary' type='button' value='우편번호찾기' /></a>
							<?php }else{?>
							<input type="text" name="zipcode" id="zipcode" value="<?php if(!empty($result->zipcode)) echo $result->zipcode?>" style="width:70px;text-align:center;"/> <a href="javascript:;" onclick="zipcodePopup();"><input class='button-secondary' type='button' value='우편번호찾기' /></a>
							<?php }?>
						</td>
					</tr>
					<tr>
						<td>주소</td>
						<td>
							<?php if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){?>
							<input type="text" name="addr1" id="addr1" style="width:350px;" value="<?php if(!empty($result->addr1)) echo $result->addr1?>" />
							<input type="text" name="addr2" id="addr2" style="width:350px;" value="<?php if(!empty($result->addr2)) echo $result->addr2?>" />
							<?php }else{?>
							<input type="text" name="addr1" id="addr1" style="width:350px;" value="<?php if(!empty($result->addr1)) echo $result->addr1?>" />
							<input type="text" name="addr2" id="addr2" style="width:350px;" value="<?php if(!empty($result->addr2)) echo $result->addr2?>" />
							<?php }?>
						</td>
					</tr>
					<tr>
						<td>전화번호</td>
						<td>
							<input type="text" name="phone_1" id="phone_1" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[0]?>" /> - 
							<input type="text" name="phone_2" id="phone_2" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[1]?>" /> - 
							<input type="text" name="phone_3" id="phone_3" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[2]?>" />
						</td>
					</tr>
					<tr>
						<td>휴대전화번호</td>
						<td>
							<input type="text" name="hp_1" id="hp_1" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[0]?>" /> - 
							<input type="text" name="hp_2" id="hp_2" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[1]?>" /> - 
							<input type="text" name="hp_3" id="hp_3" style="width:50px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[2]?>" />
						</td>
					</tr>
					<tr>
						<td>SMS수신여부</td>
						<td>
							<label><input type="radio" name="sms_reception" value="1"<?php if($result->sms_reception == "1") echo " checked";?>> 수신</label>&nbsp;&nbsp;
							<label><input type="radio" name="sms_reception" value="0"<?php if(empty($result->sms_reception) || $result->sms_reception == "0") echo " checked";?>>수신안함</label>
						</td>
					</tr>
					<tr>
						<td>이메일</td>
						<td>
							<input type="text" name="email" id="email" style="width:350px;" value="<?php echo $result->email?>" />
						</td>
					</tr>
					<tr>
						<td>직업</td>
						<td>
							<input type="text" name="job" id="job" style="width:180px;" value="<?php echo $result->job?>" />
						</td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:20px;border-top:1px solid #ddd;">
				<input type="button" class="button-primary" onclick="adminModifySubmit();" value="&nbsp;&nbsp;&nbsp;&nbsp;저장&nbsp;&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;
				<input type="button" class="button-primary" onclick="location.href = '<?php echo NFB_MEMBER_LIST?>';" value="&nbsp;&nbsp;&nbsp;&nbsp;목록&nbsp;&nbsp;&nbsp;&nbsp;">
			</td>
		</tr>
	</table>
	</form>
</div>