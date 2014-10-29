function zipcodePopup(){
	window.open(jQuery("#NFB_HOME_URL").val()+"/?NFPage=zipcode", "zipcode_search", "width=400,height=400,scrollbars=yes");
}
function openDaumPostcode(){
	new daum.Postcode({
		oncomplete: function(data){
			document.getElementById('zipcode').value = data.postcode1 + "-" + data.postcode2;
			document.getElementById('addr1').value = data.address;
			document.getElementById('addr2').focus();
		}
	}).open();
}
function id_change(){
	var frm = document.joinForm;
	frm.id_checked.value = "";
	jQuery('#error_user_id').removeClass("label-info").addClass("label-danger").hide().html("");
}

function id_check(){
	var id_check = document.joinForm.user_id.value;
	var Alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var Digit = "1234567890";
	var i;
	
	jQuery('#error_user_id').show();

	if(id_check == ""){
		jQuery('#error_user_id').html("아이디를 입력해주세요.");
		document.joinForm.user_id.focus();
		return;
	}
	if(parseInt(jQuery("#id_min_len").val()) > 0){
		if(id_check.length < parseInt(jQuery("#id_min_len").val())){
			jQuery('#error_user_id').html("아이디는 "+jQuery("#id_min_len").val()+"자 이상으로 입력해주세요.");
			document.joinForm.user_id.focus();
			return false; 
		}
	}
	if(id_check.length > 16){
		jQuery('#error_user_id').html("아이디는 16자 이하로 입력해주세요.");
		document.joinForm.user_id.focus();
		return false; 
	}

	munja = Alpha + Digit;
	if(munja.length > 1){
		for(i = 0; i < id_check.length; i++){
			if(Alpha.indexOf(id_check.charAt(0)) == -1){
				jQuery('#error_user_id').html("아이디의 첫글자는 영문자만 가능합니다.");
				document.joinForm.user_id.value = "";
				document.joinForm.user_id.focus();
				return;
			}
			if(munja.indexOf(id_check.charAt(i)) == -1){
				jQuery('#error_user_id').html("영문자와 숫자만 가능합니다.");
				document.joinForm.user_id.value = "";
				document.joinForm.user_id.focus();
				return;
			}
		}
	}
	
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "id_check_action",
			user_id: id_check
		},
		success: function(data){
			var result = data.split("|||");
			var response = result[0];  
			if(response == "success"){
				if(result[1] == "y") {
					jQuery('#id_checked').val("y");
					jQuery('#error_user_id').removeClass("label-danger").addClass("label-info");
				}else{
					jQuery('#id_checked').val("n");
					jQuery('#error_user_id').removeClass("label-info").addClass("label-danger");
				}
				jQuery('#error_user_id').html(result[2]);
			}else if(response == "empty id"){  
				jQuery('#error_user_id').html("아이디를 입력해주세요.");
			}else if(response == "short id"){  
				jQuery('#error_user_id').html("아이디는 "+jQuery("#id_min_len").val()+"자 이상으로 입력해주세요.");
			}else if(response == "long id"){  
				jQuery('#error_user_id').html("아이디는 16자 이하로 입력해주세요.");
			}else if(response == "error id"){  
				jQuery('#error_user_id').html("아이디는 영문 또는 숫자만 사용해주세요.");
			}else if(response == "join not id"){  
				jQuery('#error_user_id').html("가입이 불가능한 아이디입니다.");
			}else{
				jQuery('#error_user_id').html("서버와의 통신이 실패했습니다.");
			}
		},
		error: function(data, status, err){
			jQuery('#error_user_id').html("서버와의 통신이 실패했습니다.");
		}
	});
}
function success_msg(){
	jQuery("#success_box").show();
	jQuery("#success_box").html("정보를 정상적으로 저장하였습니다.");
	jQuery("span[id^='error_']").hide();
	scroll(0, 0);
}
function writeSubmit(){
	jQuery(".join-btn").addClass("disabled").hide();
	jQuery("#result_msg").removeClass("alert-danger").addClass("alert-info").html("처리중입니다. 잠시만 기다려 주세요.").show();

	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "join_action",
			formData: jQuery('#joinForm').serialize()
		},
		success: function(data) {
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res == "success"){
				if(jQuery("#mode").val() == "write"){
					jQuery("#success_id").val(data_arr[1]);
					jQuery("#success_hp").val(data_arr[2]);
					jQuery("#sms_send").val(data_arr[3]);
					if(jQuery("#join_redirect").val()!="") {
						location.href=jQuery("#join_redirect").val();
					}else{
						jQuery("#success_frm").submit();
					}
				}else{
					setTimeout(function(){success_msg()}, 100);
				}
			}else if(res == "exist email"){
				jQuery("#error_email").show().html("입력하신 이메일주소는 이미 사용중입니다.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "empty id"){
				jQuery("#error_user_id").show().html("아이디를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "short id"){
				jQuery("#error_user_id").show().html("아이디는 "+jQuery("#id_min_len").val()+"자 이상으로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "long id"){
				jQuery("#error_user_id").show().html("아이디는 16자 이하로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "exist id"){
				jQuery("#error_user_id").show().html("입력하신 아이디는 이미 사용중인 아이디입니다.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "check id"){
				jQuery("#error_user_id").show().html("아이디 중복확인을 해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "error id"){
				jQuery("#error_user_id").show().html("아이디는 영문 또는 숫자만 사용해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "join not id"){
				jQuery("#error_user_id").show().html("입력하신 아이디는 가입이 불가능한 아이디입니다.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "empty pass"){
				jQuery("#error_pass").show().html("비밀번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "short pass"){
				jQuery("#error_pass").show().html("비밀번호는 "+jQuery("#pass_min_len").val()+"자 이상으로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "long pass"){
				jQuery("#error_pass").show().html("비밀번호는 16자 이하로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "empty repass"){
				jQuery("#error_repass").show().html("비밀번호 확인을 위해 다시 한번더 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_repass)").hide();
				jQuery("#success_box").hide();
				jQuery("#repass").focus();
			}else if(res == "password mismatch"){
				jQuery("#error_pass").show().html("비밀번호가 일치하지 않습니다.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#repass").val("");
				jQuery("#pass").val("").focus();
				jQuery("#success_box").hide();
			}else if(res == "empty name"){
				jQuery("#error_name").show().html("이름을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_name)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_name").focus();
			}else if(res == "empty birth_year"){
				jQuery("#error_birth").show().html("생년월일(년)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_year").focus();
			}else if(res == "incorrect birth_year"){
				jQuery("#error_birth").show().html("생년월일(년)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_year").focus();
			}else if(res == "empty birth_month"){
				jQuery("#error_birth").show().html("생년월일(월)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_month").focus();
			}else if(res == "incorrect birth_month"){
				jQuery("#error_birth").show().html("생년월일(월)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_month").focus();
			}else if(res == "empty birth_day"){
				jQuery("#error_birth").show().html("생년월일(일)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_day").focus();
			}else if(res == "incorrect birth_day"){
				jQuery("#error_birth").show().html("생년월일(일)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_day").focus();
			}else if(res == "empty sex"){
				jQuery("#error_sex").show().html("성별을 선택해주세요.");
				jQuery("span[id^='error_']:not(#error_sex)").hide();
				jQuery("#success_box").hide();
				frm.sex[0].focus();
			}else if(res == "empty zipcode"){
				jQuery("#error_addr").show().html("우편번호를 선택해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
			}else if(res == "empty addr1"){
				jQuery("#error_addr").show().html("주소를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
				jQuery("#addr1").focus();
			}else if(res == "empty addr2"){
				jQuery("#error_addr").show().html("나머지주소를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
				jQuery("#addr2").focus();
			}else if(res == "empty phone_1"){
				jQuery("#error_phone").show().html("전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_1").focus();
			}else if(res == "empty phone_2"){
				jQuery("#error_phone").show().html("전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_2").focus();
			}else if(res == "empty phone_3"){
				jQuery("#error_phone").show().html("전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_3").focus();
			}else if(res == "empty hp_1"){
				jQuery("#error_hp").show().html("휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_1").focus();
			}else if(res == "empty hp_2"){
				jQuery("#error_hp").show().html("휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_2").focus();
			}else if(res == "empty hp_3"){
				jQuery("#error_hp").show().html("휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_3").focus();
			}else if(res == "empty sms_reception"){
				jQuery("#error_sms_reception").show().html("SMS 수신여부를 선택해주세요.");
				jQuery("span[id^='error_']:not(#error_sms_reception)").hide();
				jQuery("#success_box").hide();
				frm.sms_reception[1].focus();
			}else if(res == "empty email"){
				jQuery("#error_email").show().html("이메일을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "not form email"){
				jQuery("#error_email").show().html("이메일 형식이 올바르지 않습니다.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "empty job"){
				jQuery("#error_job").show().html("직업을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_job)").hide();
				jQuery("#success_box").hide();
				jQuery("#job").focus();
			}else if(res == "empty agree_check1"){
				jQuery("#error_agree1").show().html("이용약관에 동의해주세요.");
				jQuery("span[id^='error_']:not(#error_agree1)").hide();
				jQuery("#success_box").hide();
				jQuery("#agree_check1").focus();
			}else if(res == "empty agree_check2"){
				jQuery("#error_agree2").show().html("개인정보취급방침에 동의해주세요.");
				jQuery("span[id^='error_']:not(#error_agree2)").hide();
				jQuery("#success_box").hide();
				jQuery("#agree_check2").focus();
			}else if(res == "nonData"){
				alert("정상적인 접근이 아닙니다.");
				location.href = jQuery("#NFB_WEB").val();
			}
			jQuery("#result_msg").hide();
			jQuery(".join-btn").removeClass("disabled").show();


		},
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
			jQuery("#result_msg").hide();
			jQuery(".join-btn").removeClass("disabled").show();
		}
	});
}

jQuery(function(){
	jQuery("span[id^='error_']").hide();

	jQuery(".join-btn").click(function() {
		writeSubmit();
	});
	jQuery("input[type='text']").keydown(function() {
		var item = jQuery(this).attr('name');
		if(jQuery(this).val() != ""){
			if(item=="birth_year" || item=="birth_month" || item=="birth_day") {
				jQuery('#error_birth').empty().hide();
				checkForNumber();
				return;
			}else if(item=="phone_1" || item=="phone_2" || item=="phone_3") {
				jQuery('#error_phone').empty().hide();
				checkForNumber();
				return;
			}else if(item=="hp_1" || item=="hp_2" || item=="hp_3") {
				jQuery('#error_hp').empty().hide();
				checkForNumber();
				return;
			}else{
				jQuery('#error_'+item).empty().hide();
			}
		}
	});
	jQuery("input[type='password']").keydown(function() {
		jQuery('#error_'+jQuery(this).attr('name')).empty().hide();
	});
	jQuery("#agree1").keydown(function() {
		jQuery('#error_agree1').empty().hide();
	});
	jQuery("#agree2").keydown(function() {
		jQuery('#error_agree2').empty().hide();
	});
});