<?php
if(empty($rows->user_id)) $mode = "write";
else $mode = "edit";

if(!empty($rows->birth)) $birth_arr = explode("-", $rows->birth);
if(!empty($rows->phone)) $phone_arr = explode("-", $rows->phone);
if(!empty($rows->hp)) $hp_arr = explode("-", $rows->hp);

if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){
?>
<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.js"></script>
<?php
}
if($config->table_align == "C") $table_align = "margin:0 auto;";
else if($config->table_align == "L") $table_align = "float:left;";
else if($config->table_align == "R") $table_align = "float:right;";
?>
<?php if(empty($_POST['success_mode'])){?>
<div id="NFBoard_Content" style="width:<?php echo $config->table_width?>%;<?php echo $table_align?>">

	<div class="alert alert-info center-block" style="display:none;" role="alert" id="success_box"></div>
	<form class="form-horizontal" role="form" id="joinForm" name="joinForm">
		<input type="hidden" name="NFB_HOME_URL" id="NFB_HOME_URL" value="<?php echo NFB_HOME_URL?>" />
		<input type="hidden" name="NFB_WEB" id="NFB_WEB" value="<?php echo NFB_WEB?>" />
		<input type="hidden" name="moveURL" id="moveURL" value="<?php if(!empty($_GET['moveURL'])) echo $_GET['moveURL']; else echo NFB_SITE_URL?>" />
		<input type="hidden" name="zipcode_api_module" id="zipcode_api_module" value="<?php echo $config->zipcode_api_module?>" />
		<input type="hidden" name="id_min_len" id="id_min_len" value="<?php echo $config->id_min_len?>" />
		<input type="hidden" name="pass_min_len" id="pass_min_len" value="<?php echo $config->pass_min_len?>" />
		<input type="hidden" name="join_redirect" id="join_redirect" value="<?php echo $config->join_redirect?>" />
		<input type="hidden" name="mode" id="mode" value="<?php echo $mode?>" />
		<input type="hidden" name="uno" id="uno" value="<?php echo $rows->uno?>" />
		<input type="hidden" name="id_checked" id="id_checked" value="" />
		<input type="hidden" name="skinname" id="skinname" value="<?php echo get_option("NFB_skin")?>">
		<div class="form-group">
			<label for="user_id" class="col-sm-2 control-label">아이디 <span class="require-field">*</span></label>
			<div class="col-sm-10 text-left">
				<?php if(empty($rows->user_id)){?>
				<div class="input-group">
					<input type="text" class="form-control" name="user_id" id="user_id" onkeydown="id_change();" maxlength="16">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="id_check();">중복확인</button>
					</span>
				</div>
				<h4><span class="label label-danger" id="error_user_id" style="display:none;"></span></h4>
				<?php }else{?>
				<p class="form-control-static"><?php echo $rows->user_id?></p>
				<?php }?>
			</div>
		</div>
		<?php if($config->use_name == "1"){?>
		<div class="form-group">
			<label for="user_name" class="col-sm-2 control-label">이름 <?php if($config->validate_name == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="user_name" id="user_name" value="<?php if(!empty($rows->name)) echo $rows->name?>" maxlength="20">
				<h4><span class="label label-danger" id="error_name" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">이메일 <span class="require-field">*</span></label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="email" id="email" value="<?php if(!empty($rows->email)) echo $rows->email?>">
				<h4><span class="label label-danger" id="error_email" style="display:none;"></span></h4>
			</div>
		</div>
		<div class="form-group">
			<label for="pass" class="col-sm-2 control-label">비밀번호 <span class="require-field">*</span></label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="pass" id="pass" maxlength="16">
				<h4><span class="label label-danger" id="error_pass" style="display:none;"></span></h4>
			</div>
		</div>
		<div class="form-group">
			<label for="repass" class="col-sm-2 control-label">비밀번호 확인 <span class="require-field">*</span></label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="repass" id="repass" maxlength="16">
				<h4><span class="label label-danger" id="error_repass" style="display:none;"></span></h4>
			</div>
		</div>
		<?php if($config->use_birth == "1"){?>
		<div class="form-group">
			<label for="birth_year" class="col-sm-2 control-label">생년월일 <?php if($config->validate_birth == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<div class="form-inline">
					<input type="text" class="form-control" name="birth_year" id="birth_year" value="<?php if(!empty($birth_arr[0])) echo $birth_arr[0]?>" maxlength="4" style="min-width:70px;width:10%;float:left;"><div style="float:left;padding-top:6px;">&nbsp;년&nbsp;&nbsp;</div>
					<input type="text" class="form-control" name="birth_month" id="birth_month" value="<?php if(!empty($birth_arr[1])) echo $birth_arr[1]?>" maxlength="2" style="min-width:70px;width:10%;float:left;"><span style="float:left;padding-top:6px;">&nbsp;월&nbsp;&nbsp;</span>
					<input type="text" class="form-control" name="birth_day" id="birth_day" value="<?php if(!empty($birth_arr[2])) echo $birth_arr[2]?>" maxlength="2" style="min-width:70px;width:10%;float:left;"><span style="float:left;padding-top:6px;">&nbsp;일</span>
					<h4 style="padding-top:10px;clear:both;"><span class="label label-danger" id="error_birth" style="display:none;"></span></h4>
				</div>
			</div>
		</div>
		<?php }?>
		<?php if($config->use_sex == "1"){?>
		<div class="form-group">
			<label for="sex" class="col-sm-2 control-label">성별 <?php if($config->validate_sex == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<label class="radio-inline">
					<input type="radio" name="sex" id="sex" value="1"<?php if(empty($rows->sex) || $rows->sex == "1") echo " checked";?>> 남자
				</label>
				<label class="radio-inline">
					<input type="radio" name="sex" id="sex" value="2"<?php if(!empty($rows->sex) && $rows->sex == "2") echo " checked";?>> 여자
				</label>
				<h4><span class="label label-danger" id="error_sex" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<?php if($config->use_addr == "1"){?>
		<div class="form-group">
			<label for="addr2" class="col-sm-2 control-label">주소 <?php if($config->validate_addr == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){?>
				<div class="input-group" style="width:200px;">
					<input type="text" class="form-control" name="zipcode" id="zipcode" value="<?php if(!empty($rows->zipcode)) echo $rows->zipcode?>" readonly>
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="openDaumPostcode();">우편번호찾기</button>
					</span>
				</div>
				<div style="padding-top:5px;"><input type="text" class="form-control" name="addr1" id="addr1" value="<?php if(!empty($rows->addr1)) echo $rows->addr1?>" readonly></div>
				<div style="padding-top:5px;"><input type="text" class="form-control" name="addr2" id="addr2" value="<?php if(!empty($rows->addr2)) echo $rows->addr2?>"></div>
				<?php }else{?>
				<div class="input-group" style="width:200px;">
					<input type="text" class="form-control" name="zipcode" id="zipcode" value="<?php if(!empty($rows->zipcode)) echo $rows->zipcode?>" readonly>
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="zipcodePopup();">우편번호찾기</button>
					</span>
				</div>
				<div style="padding-top:5px;"><input type="text" class="form-control" name="addr1" id="addr1" value="<?php if(!empty($rows->addr1)) echo $rows->addr1?>" readonly></div>
				<div style="padding-top:5px;"><input type="text" class="form-control" name="addr2" id="addr2" value="<?php if(!empty($rows->addr2)) echo $rows->addr2?>"></div>
				<?php }?>
				<h4><span class="label label-danger" id="error_addr" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<?php if($config->use_phone == "1"){?>
		<div class="form-group">
			<label for="phone_1" class="col-sm-2 control-label">전화번호 <?php if($config->validate_phone == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<div class="form-inline">
					<input type="text" class="form-control" name="phone_1" id="phone_1" value="<?php if(!empty($phone_arr[0])) echo $phone_arr[0]?>" maxlength="4"style="min-width:70px;width:10%;float:left;"><div style="float:left;padding-top:6px;">&nbsp;-&nbsp;</div>
					<input type="text" class="form-control" name="phone_2" id="phone_2" value="<?php if(!empty($phone_arr[1])) echo $phone_arr[1]?>" maxlength="4"style="min-width:70px;width:10%;float:left;"><div style="float:left;padding-top:6px;">&nbsp;-&nbsp;</div>
					<input type="text" class="form-control" name="phone_3" id="phone_3" value="<?php if(!empty($phone_arr[2])) echo $phone_arr[2]?>" maxlength="4" style="min-width:70px;width:10%;float:left;">
				</div>
				<h4 style="clear:both;padding-top:10px;"><span class="label label-danger" id="error_phone" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<?php if($config->use_hp == "1"){?>
		<div class="form-group">
			<label for="hp_1" class="col-sm-2 control-label">휴대전화번호 <?php if($config->validate_hp == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<div class="form-inline">
					<input type="text" class="form-control" name="hp_1" id="hp_1" value="<?php if(!empty($phone_arr[0])) echo $phone_arr[0]?>" maxlength="4"style="min-width:70px;width:10%;float:left;"><div style="float:left;padding-top:6px;">&nbsp;-&nbsp;</div>
					<input type="text" class="form-control" name="hp_2" id="hp_2" value="<?php if(!empty($phone_arr[1])) echo $phone_arr[1]?>" maxlength="4" style="min-width:70px;width:10%;float:left;"><div style="float:left;padding-top:6px;">&nbsp;-&nbsp;</div>
					<input type="text" class="form-control" name="hp_3" id="hp_3" value="<?php if(!empty($phone_arr[2])) echo $phone_arr[2]?>" maxlength="4" style="min-width:70px;width:10%;float:left;">
				</div>
				<h4 style="clear:both;padding-top:10px;"><span class="label label-danger" id="error_hp" style="display:none;"></span></h4>
			</div>
		</div>
		<div class="form-group">
			<label for="sms_reception" class="col-sm-2 control-label">SMS(문자)수신여부 <?php if($config->validate_hp == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<label class="radio-inline">
					<input type="radio" name="sms_reception" id="sms_reception1" value="1"<?php if(!empty($rows->sms_reception) && $rows->sms_reception == "1") echo " checked";?>> 수신
				</label>
				<label class="radio-inline">
					<input type="radio" name="sms_reception" id="sms_reception1" value="0"<?php if(empty($rows->sms_reception) || $rows->sms_reception == "0") echo " checked";?>> 수신안함
				</label>
				<h4><span class="label label-danger" id="error_sms_reception" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<?php if($config->use_job == "1"){?>
		<div class="form-group">
			<label for="phone_1" class="col-sm-2 control-label">직업 <?php if($config->validate_job == 1){?><span class="require-field">*</span><?php }?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="job" id="job" value="<?php if(!empty($rows->job)) echo $rows->job?>">
				<h4><span class="label label-danger" id="error_job" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<?php if($mode == "write"){?>
		<div class="form-group">
			<label class="col-sm-2 control-label">이용약관</label>
			<div class="col-sm-10">
				<div class="agreement1"><?php if(!empty($config->join_agreement)) echo nl2br($config->join_agreement)?></div>
				<input type="checkbox" name="agree_check1" id="agree_check1" value="1"/> <label for="agree_check1">이용약관에 동의합니다.</label>
				<h4><span class="label label-danger" id="error_agree1" style="display:none;"></span></h4>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">개인정보취급방침</label>
			<div class="col-sm-10">
				<div class="agreement2"><?php if(!empty($config->join_private)) echo nl2br($config->join_private)?></div>
				<input type="checkbox" name="agree_check2" id="agree_check2" value="1"/> <label for="agree_check2">개인정보취급방침에 동의합니다.</label>
				<h4><span class="label label-danger" id="error_agree2" style="display:none;"></span></h4>
			</div>
		</div>
		<?php }?>
		<div class="alert alert-danger center-block" style="display:none;" role="alert" id="result_msg"></div>

		<div class="text-center">
			<button type="button" class="btn btn-primary join-btn"><?php if($mode == "write") echo "회원가입"; else if($mode == "edit") echo "회원정보수정";?></button>
			<?php if($mode == "edit"){?>		
			&nbsp;&nbsp;
			<a href="<?php echo get_permalink(get_option('NFB_leave_page'))?>" class="btn btn-danger" role="button">회원탈퇴</a>
			<?php }?>
		</div>

	</form>

<?php 
}else if($_POST['success_mode'] == "join"){
?>
<div id="NFBoard_Content">
	<div class="panel panel-info center-block" style="max-width:500px;">
		<div class="panel-heading text-center">
			<h3 class="panel-title">회원가입이 완료되었습니다.</h3>
		</div>
		<div class="panel-body text-center">
			<p>아이디 : <strong><?php echo $_POST['success_id']?></strong></p>
			<a href="<?php echo get_permalink(get_option('NFB_login_page'))?>" class="btn btn-default" role="button">로그인</a>
		</div>
	</div>
</div>
<?php 
}
?>
<form method="post" name="success_frm" id="success_frm">
<input type="hidden" name="success_mode" id="success_mode" value="join" />
<input type="hidden" name="success_id" id="success_id" value="" />
<input type="hidden" name="success_hp" id="success_hp" value="" />
<input type="hidden" name="sms_send" id="sms_send" value="" />
</form>