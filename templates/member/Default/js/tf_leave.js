function leaveSubmit(){
	jQuery(".leave-btn").addClass("disabled").hide();
	jQuery("#result_msg").removeClass("alert-danger").addClass("alert-info").show();
	jQuery("#result_msg").html("처리중입니다. 잠시만 기다려주세요.");
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "leave_action",
			formData: jQuery('#leaveForm').serialize()
		},
		success: function(data) {
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res != "success") jQuery("#result_msg").removeClass("alert-info").addClass("alert-danger");
			if(res == "success"){
				if(data_arr[2] != "") jQuery("#NFBoard_Content").empty().html(data_arr[2]);
			}else if(res == "not login"){
				jQuery("#NFBoard_Content").empty().html("로그인 후 이용해주세요.");
			}else if(res == "empty pass"){
				jQuery("#result_msg").show().html("비밀번호를 입력해주세요.");
				jQuery("#pass").focus();
			}else if(res == "empty repass"){
				jQuery("#result_msg").show().html("비밀번호 확인을 입력해주세요.");
				jQuery("#repass").focus();
			}else if(res == "password mismatch"){
				jQuery("#result_msg").show().html("비밀번호가 일치하지 않습니다.");
			}
			jQuery(".leave-btn").removeClass("disabled").show();

		},
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
			jQuery("#result_msg").hide();
			jQuery(".leave-btn").removeClass("disabled").show();
		}
	});
}
jQuery(function() {
	jQuery("#pass").keydown(function() {
		if(jQuery("#pass").val() != ''){jQuery('#result_msg').empty().hide();}
		if(event.keyCode == 13) leaveSubmit();
	});
	jQuery("#repass").keydown(function() {
		if(jQuery("#repass").val() != ''){jQuery('#result_msg').empty().hide();}
		if(event.keyCode == 13) leaveSubmit();
	});
	jQuery(".leave-btn").click(function() {
		leaveSubmit();
	});
});