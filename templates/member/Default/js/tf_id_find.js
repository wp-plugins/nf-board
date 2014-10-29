function idFindSubmit(){
	jQuery(".id-find-btn").addClass("disabled").hide();
	jQuery("#result_msg").removeClass("alert-danger").addClass("alert-info").show();
	jQuery("#result_msg").html("처리중입니다. 잠시만 기다려주세요.");
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "id_find_action",
			formData: jQuery('#idfindForm').serialize()
		},
		success: function(data) {
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res != "success") jQuery("#result_msg").removeClass("alert-info").addClass("alert-danger");
			if(res == "success"){
				if(data_arr[2] != "") jQuery("#NFBoard_Content").empty().html(data_arr[2]);
			}else if(res == "empty email"){
				jQuery("#result_msg").show().html("이메일을 입력해주세요.");
				jQuery("#email").focus();
			}else if(res == "not form email"){
				jQuery("#result_msg").show().html("이메일 형식이 올바르지 않습니다.");
				jQuery("#email").focus();
			}
			jQuery(".id-find-btn").removeClass("disabled").show();
		},
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
			jQuery("#result_msg").hide();
			jQuery(".id-find-btn").removeClass("disabled").show();
		}
	});
}
jQuery(function() {
	jQuery("#email").keydown(function() {
		if(jQuery("#email").val() != ''){jQuery('#result_msg').empty().hide();}
		if(event.keyCode == 13) idFindSubmit();
	});

	jQuery(".id-find-btn").click(function() {
		idFindSubmit();
	});
});