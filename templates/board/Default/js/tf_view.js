function NFB_ShareTwitter(){
	var left = (screen.width / 2) - 200;
	var top = (screen.height / 2) - 180;
	var content = jQuery("#share_title").val();
	var link = jQuery("#share_link").val();
	var popOption = "width=400, height=360, top=" + top + ", left=" + left + ", resizable=no, scrollbars=no, status=no;";

	var wp = window.open("http://twitter.com/share?url=" + encodeURIComponent(link) + "&text=" + encodeURIComponent(content), 'share_twitter', popOption); 
	if(wp){
		wp.focus();
	}     
}
function NFB_ShareFacebook(){
	var left = (screen.width / 2) - 200;
	var top = (screen.height / 2) - 180;
	var link = jQuery("#share_link").val();
	var popOption = "width=400, height=360, top=" + top + ", left=" + left + ", resizable=no, scrollbars=no, status=no;";
	var wp = window.open("http://www.facebook.com/share.php?u=" + encodeURIComponent(link), 'share_facebook', popOption); 
	if(wp){
		wp.focus();
	}
}
function NFB_ShareHMS(){
	var left = (screen.width / 2) - 310;
	var top = (screen.height/2) - 300;
	var link = jQuery("#share_link").val();
	var popOption = "width=620, height=600, top=" + top + ", left=" + left + ", resizable=no, scrollbars=no, status=no;";

	var wp = window.open("http://hyper-message.com/hmslink/sendurl?url=" + encodeURIComponent(link), 'share_hms', popOption); 
	if(wp){
		wp.focus();
	}     
}
function commentWrite(mode){
	if(mode != "reply") jQuery("#cno").val("");
	else jQuery("#cno").val(jQuery("#sub_cno").val());
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "board_comment_write",
			formData: jQuery('#commentForm').serialize()
		},
		success: function (data) {
			var data_arr = data.split("|||");
			var res = data_arr[0];
			var type = data_arr[1];
			if(type == 1){
				var error_box = "#error_box";
				var cname = "#cname";
				var cpass = "#cpass";
				var cstring = "#string";
				var cmemo = "#cmemo";
			}else if(type == 2){
				var error_box = "#reply_error_box";
				var cname = "#reply_cname";
				var cpass = "#reply_cpass";
				var cstring = "#reply_string";
				var cmemo = "#reply_cmemo";
			}
			if(res == "success"){	
				jQuery("#passForm").submit();
			}else if(res == "not permission"){
				jQuery(error_box).show().html("댓글쓰기 권한이 없습니다.");
			}else if(res == "fail string"){
				jQuery(error_box).show().html("입력된 인증번호가 유효하지 않습니다.");
			}else if(res == "empty cname"){
				jQuery(error_box).show().html("작성자를 입력해주세요.");
				jQuery(cname).focus();
			}else if(res == "long cname"){
				jQuery(error_box).show().html("작성자는 16자 이하로 입력해주세요.");
				jQuery(cname).focus();
			}else if(res == "empty cpass"){
				jQuery(error_box).show().html("비밀번호를 입력해주세요.");
				jQuery(cpass).focus();
			}else if(res == "long cpass"){
				jQuery(error_box).show().html("비밀번호는 16자 이하로 입력해주세요.");
				jQuery(cpass).focus();
			}else if(res == "empty string"){
				jQuery(error_box).show().html("스팸글 방지를 위해 숫자를 입력해주세요.");
				jQuery(cstring).focus();
			}else if(res == "empty cmemo"){
				jQuery(error_box).show().html("내용을 입력해주세요.");
				jQuery(cmemo).focus();
			}else{
				jQuery(error_box).show().html("댓글 저장에 실패하였습니다.");
			}
		},
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
		}
	});
}
function fieldCheck(id){
	if(id == 1){
		jQuery('#error_box').empty().hide();
	}else if(id == 2){
		jQuery('#reply_error_box').empty().hide();
	}
}
function deleteConfirm(obj, cno){
	jQuery("#cno").val(cno);
	jQuery("#sub_cno").val(cno);
	obj.parent().parent().children("p").children("div").remove();
	obj.parent().parent().siblings("li").children("p").children("div").remove();
	obj.parent().append("<div class='del_input'><div class='pass_input'><span class='tit'>비밀번호입력 </span><input title='비밀번호입력' type='password' name='comment_del_pass' id='comment_del_pass' value='' onkeydown=\"jQuery('#meg').empty().hide();\" /><button type=\"button\" class=\"btn btn-default btn-xs\" onclick=\"commentAllDelete();\">확인</button></div><p class='meg' id='meg' style='display:none;'></p></div>");
}
function commentAllDelete(){
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "board_comment_delete",
			bname:jQuery("#bname").val(),
			cno:jQuery("#cno").val(),
			pwd:jQuery("#comment_del_pass").val()
		},
		success: function(data){
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res == "success"){
				jQuery("#passForm").submit();
			}else if(res == "empty password"){
				jQuery('#meg').show().html('비밀번호를 입력해주세요.');
			}else if(res == "password error"){
				jQuery('#meg').show().html('비밀번호가 일치하지 않습니다.');
			}else{
				jQuery('#meg').show().html('삭제에 실패하였습니다.');
			}
		},
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
		}
	});
}
function commentDeleteCheck(cno, obj){
	jQuery("#cno").val(cno);
	jQuery("#sub_cno").val(cno);

	if(confirm("삭제 하시겠습니까?")){

		jQuery.ajax({
			url: ajax_object.ajaxurl,
			type: "post",
			dataType: "text",
			data: {
				action: "board_comment_delete",
				bname:jQuery("#bname").val(),
				cno:jQuery("#cno").val()
			},
			success: function(data){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					jQuery("#passForm").submit();
				}else{
					obj.parent().parent().children("p").children("div").remove();
					obj.parent().parent().siblings("li").children("p").children("div").remove();
					obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>삭제에 실패하였습니다.</p></div>");
				}
			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}
}
function commentFail(obj, mode){
	obj.parent().parent().children("p").children("div").remove();
	obj.parent().parent().siblings("li").children("p").children("div").remove();
	if(mode == "reply") var error_msg = "댓글의 댓글까지만 등록 가능합니다.";
	else if(mode == "delete") var error_msg = "삭제권한이 없습니다.";
	else if(mode == "child") var error_msg = "댓글이 있을 경우 삭제가 불가능합니다.";
	obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>" + error_msg + "</p></div>");
}