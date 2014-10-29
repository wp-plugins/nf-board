function boardDelete(tNo){
	if(tNo > 0){
		if(confirm('해당 보드을 삭제하시겠습니까?   ')){
			jQuery("#delNo").val(tNo);
			jQuery("#boardList").attr("action", "admin.php?page=NFBoardList");
			jQuery("#boardList").submit();
		}
	}
}

function batchAction(sNo){
	if(jQuery("#tBatch" + sNo).val()== -1){
		alert('일괄 작업을 선택해 주세요.   ');
		jQuery("#tBatch" + sNo).focus();
		return false;
	}
	if(jQuery("input:checkbox:checked").length <= 0){
		alert('삭제할 보드을 선택해 주세요.   ');
		return false;
	}
	if(confirm('선택한 보드을 삭제하시겠습니까?   ')){
		jQuery("#boardList").attr("action", "admin.php?page=NFBoardList");
		jQuery("#boardList").submit();
	}
}
function useSEO(){
	if(jQuery("#b_seo_use").is(":checked") == true){
		jQuery("#seo_title_tr").show();
		jQuery("#seo_description_tr").show();
		jQuery("#seo_keywords_tr").show();
	}else{
		jQuery("#seo_title_tr").hide();
		jQuery("#seo_description_tr").hide();
		jQuery("#seo_keywords_tr").hide();
	}
}
function boardSubmit(dbType, action_url){
	var tMode = "chkBoardName";
	var tBoardName = jQuery("#b_name").val();
	var tBoardNo = jQuery("#b_no").val();
	var dbTypeName = "";
	var space = /\s/;
	var Alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var Digit = '1234567890';
	var namecheck = 0;
	var i;
	var munja = Alpha + Digit;

	if(dbType == 'insert') dbTypeName = "추가";
	else dbTypeName = "수정";
	
	if(!tBoardName){
		alert("보드 이름을 입력해주세요.   ");
		jQuery("#b_name").focus();
		return false;
	}
	if(tBoardName.length > 20){
		alert("보드 이름을 20자 이내로 입력해주세요.   ");
		jQuery("#b_name").focus();
		return false;
	}
	if(tBoardName == "admin"){
		alert("'admin'은 보드 이름으로 사용할수 없습니다.   ");
		jQuery("#b_name").focus();
		return false;
	}
	if(space.exec(tBoardName)){
		alert("보드 이름에는 공백을 사용할 수 없습니다.   ");
		jQuery("#b_name").focus();
		return false;
	}
	for(i = 0; i < tBoardName.length; i++){
		if(munja.indexOf(tBoardName.charAt(i)) == -1){
			namecheck = namecheck + 1;
			break;
		}
	}
	if(namecheck > 0){
		alert("보드 이름은 영문자와 숫자만 가능합니다!");
		jQuery("#b_name").focus();
		return false;
	}
	jQuery.ajax({
		url: ajax_object.ajaxurl,
		type: "post",
		dataType: "text",
		data: {
			action: "admin_board_action",
			tMode:tMode,
			tBoardName:tBoardName,
			tBoardNo:tBoardNo
		},

		success: function(data){
			var response = data; 
			if(response == "success"){
				if(!jQuery("#b_type").val()){
					alert("형태 설정을 선택해주세요.");
					jQuery("#b_type").focus();
					return false;
				}
				if(!jQuery("#b_skin").val()){
					alert("스킨 설정을 선택해주세요.");
					jQuery("#b_skin").focus();
					return false;
				}
				if(!jQuery("#b_width").val()){
					alert("가로 사이즈를 입력해주세요.");
					jQuery("#b_width").focus();
					return false;
				}
				if(jQuery("input[name='b_align']:radio:checked").length==0){
					alert("정렬 위치를 선택해주세요.");
					return false;
				}
				if(!jQuery("#b_psize").val()){
					alert("페이지당 목록 수를 입력해주세요.");
					jQuery("#b_psize").focus();
					return false;
				}
				if(confirm("보드을 " + dbTypeName + "하시겠습니까?    ")){
					jQuery("#boardForm").attr("action", action_url);
					jQuery("#boardForm").submit();
				}
			}else if(response == "existName"){
				alert("이미 등록된 테이블명입니다. 다른 테이블 명을 사용해주세요.   ");
				jQuery("#b_name").focus();
			}else if(response == "usedAdmin"){
				alert("'admin'은 보드이름으로 사용할 수 없습니다.   ");
				jQuery("#b_name").focus();
			}else if(response == "usedBlank"){
				alert("보드 이름에는 공백을 사용할 수 없습니다.   ");
				jQuery("#b_name").focus();
			}else if(response == "over20"){
				alert("보드 이름을 20자 이내로 입력해주세요.   ");
				jQuery("#b_name").focus();
			}else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
}