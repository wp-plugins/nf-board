jQuery(document).ready(function() {

	function loginSubmit(){

		jQuery(".login-btn").addClass("disabled").hide();
		jQuery("#result_msg").removeClass("alert-danger").addClass("alert-info").show();
		jQuery("#result_msg").html("처리중입니다. 잠시만 기다려주세요.");

		jQuery.ajax({
			url: ajax_object.ajaxurl,
			type: "post",
			dataType: "text",
			data: {
				action: "login_action",
				formData: jQuery('#loginForm').serialize()
			},
			success: function(res) {

				if(res != "success") jQuery("#result_msg").removeClass("alert-info").addClass("alert-danger");
				if(res == "success"){
					location.href = jQuery("#moveURL").val();
				}else if(res == "empty id"){
					jQuery("#result_msg").show().html("아이디를 입력해주세요.");
					jQuery("#uid").focus();
				}else if(res == "id_fail"){
					jQuery("#result_msg").show().html("아이디가 올바르지 않습니다.");
					jQuery("#uid").focus();
				}else if(res == "empty pass"){
					jQuery("#result_msg").show().html("비밀번호를 입력해주세요.");
					jQuery("#upass").focus();
				}else if(res == "pass_fail"){
					jQuery("#result_msg").show().html("비밀번호가 올바르지 않습니다.");
					jQuery("#upass").focus();
				}else if(res == "login_fail"){
					jQuery("#result_msg").show().html("로그인 오류입니다.");
					jQuery("#uid").focus();
				}
				jQuery(".login-btn").removeClass("disabled").show();

			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
				jQuery("#result_msg").hide();
				jQuery(".login-btn").removeClass("disabled").show();
			}
		});

	}
	jQuery(".login-btn").click(function() {
		loginSubmit();
	});

	jQuery("#uid").keydown(function() {
		if(jQuery("#uid").val() != ''){jQuery('#result_msg').empty().hide();}
		if(event.keyCode == 13) loginSubmit();
	});
	jQuery("#upass").keypress(function() {
		if(jQuery("#upass").val() != ''){jQuery('#result_msg').empty().hide();}
		if(event.keyCode == 13) loginSubmit();
	});
	jQuery("#upass").focus(function() {
		jQuery("#upass").val('');
		return false;
	});

});