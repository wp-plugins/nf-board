function listSearchSubmit(){
	jQuery("#listSearchForm").attr("action", jQuery("#actionURL").val());
	jQuery("#listSearchForm").ajaxForm(
		function(data, state){
			if(state == "success"){
				location.href = jQuery("#moveURL").val() + data;
			}else{
				alert("게시물 검색에 실패하였습니다.  ");
				return false;
			}
		}
	); 
	jQuery("#listSearchForm").submit(); 
}
function listAction(url, mode){
	var i, chked = 0;
	if(mode == "move") var mode_txt = "이동";
	else if(mode == "copy") var mode_txt = "복사";
	for(i = 0; i < document.getElementsByName("check[]").length; i++){
		if(document.getElementsByName("check[]")[i].type == 'checkbox'){
			if(document.getElementsByName("check[]")[i].checked){
				chked = 1;
			}
		}
	}

	if(chked){
		checkvalue = '';
		for(i = 0; i < document.getElementsByName("check[]").length; i++){
			if(document.getElementsByName("check[]")[i].type == 'checkbox'){
				if(document.getElementsByName("check[]")[i].checked){
					checkvalue = document.getElementsByName("check[]")[i].value + '_' + checkvalue;
				}
			}
		}	
		location.href = url + "&check=" + checkvalue + "&mode=" + mode;
	}else{	
		alert(mode_txt + "할 게시물을 선택해주세요.");
	}
}
function listDelete(url){
	var i, j = 0, k = 0;
	for(i = 0; i < document.getElementsByName("check[]").length; i++){
		if(document.getElementsByName("check[]")[i].checked) k++;
	}
	if(k < 1){
		alert("삭제하실 게시물을 선택해 주세요");
		return false;
	
	}else{
		if(confirm("삭제하시겠습니까?")){
			document.listForm.action = url;      
			document.listForm.submit();
			return true;
		}else{
			return false;
		}
	}
}
jQuery(function() {
	jQuery("#keyword").keypress(function() {
		if(event.keyCode == 13) listSearchSubmit();
	});
	jQuery("#list_select").bind('click', function() {
		if(jQuery(this).is(":checked") == true) {
			jQuery(".bno_chk").prop("checked",true);
		}else{
			jQuery(".bno_chk").prop("checked",false);
		}
	});
});