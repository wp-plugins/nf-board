function writeSubmit(){
	if(jQuery("#b_editor").val() == "W"){
		var ed = tinyMCE.get('content1');
		document.writeForm.content.value = ed.getContent();
	}
	jQuery('#writeForm').attr("action", jQuery("#actionURL").val());
	jQuery('#writeForm').ajaxForm({
		type:"POST",
		async:true,
		xhrFields:{withCredentials:true},
		success:function(data, state){
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res == "success"){
				location.href = jQuery("#moveURL").val();
			}else if(res == "empty title"){
				jQuery("#error_box").show().html("제목을 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#title").focus();
			}else if(res == "empty category"){
				jQuery("#error_box").show().html("카테고리를 선택해주세요.");
				jQuery("#success_box").hide();
				jQuery("#category").focus();
			}else if(res == "empty writer"){
				jQuery("#error_box").show().html("작성자를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#writer").focus();
			}else if(res == "long writer"){
				jQuery("#error_box").show().html("작성자는 16자 이하로 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#writer").focus();
			}else if(res == "empty pass"){
				jQuery("#error_box").show().html("비밀번호를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "long pass"){
				jQuery("#error_box").show().html("비밀번호는 16자 이하로 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "empty content"){
				jQuery("#error_box").show().html("내용을 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#content").focus();
			}else if(res == "empty string"){
				jQuery("#error_box").show().html("스팸글 방지를 위해 숫자를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#string").focus();
			}else if(res == "auth error"){
				jQuery("#error_box").show().html("입력된 인증번호가 유효하지 않습니다.");
				jQuery("#success_box").hide();
				jQuery("#string").focus();
			}else if(res == "empty agree1"){
				jQuery("#error_box").show().html("개인정보취급방침에 동의해주세요.");
				jQuery("#success_box").hide();
				jQuery("#agree1").focus();
			}else if(res == "filter error"){
				jQuery("#error_box").show().html("제목 또는 내용에 사용할 수 없는 단어가 포함되어 있습니다.");
				jQuery("#success_box").hide();
			}else if(res == "file type error"){
				jQuery("#error_box").show().html("선택하신 파일의 확장자는 업로드가 제한되어 있습니다. (첨부파일)");
				jQuery("#success_box").hide();
			}else if(res == "file byte error"){
				jQuery("#error_box").show().html(jQuery("#b_filesize").val() + "MB 이상의 파일은 업로드 하실 수 없습니다. (첨부파일)");
				jQuery("#success_box").hide();
			}else if(res == "password error"){
				jQuery("#error_box").show().html("비밀번호가 일치하지 않습니다.");
				jQuery("#success_box").hide();
			}else{
				jQuery("#error_box").show().html("게시물 저장에 실패하였습니다.");
				jQuery("#success_box").hide();
			}
		}
	}); 
	jQuery('#writeForm').submit(); 
}
function fieldCheck(){
	jQuery('#error_box').empty().hide();
} 