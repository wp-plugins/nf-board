function upload_img(formfield){
	jQuery('#upload_target_img').val(formfield);
	tb_show('', 'media-upload.php?type=image&TB_iframe=true&width=640&height=450&modal=false');
	return false;
}
function send_to_editor(html){
	var src = jQuery('img', html).attr('src');
	var uTarget = jQuery('#upload_target_img').val();
	jQuery('#' + uTarget).val(src);
	tb_remove();
	jQuery('#upload_target_img').val("");
}
function addrChange() {
	if(jQuery("input[name='use_addr']").is(":checked")==true) {

		if(jQuery("input[name='use_zipcode_api']").is(":checked")==true) {
			jQuery("input[name^='zipcode_api_module']").attr("disabled",false);
		}else{
			jQuery("input[name^='zipcode_api_module']").removeAttr("checked").attr("disabled",true);
		}
		jQuery("#addr_form1").show();
		if(jQuery("input[name='zipcode_api_module']:checked").val()=="1") {
			jQuery("#addr_form2").show();
		}else{
			jQuery("#addr_form2").hide();
		}
	}else{
		jQuery("#addr_form1").hide();
		jQuery("#addr_form2").hide();
	}
}
function saveSetup(){
	var frm = document.setupFrom;
	if(frm.use_zipcode_api.checked == true){
		if(frm.zipcode_api_module[0].checked == false && frm.zipcode_api_module[1].checked == false){
			alert("도로명주소 API 종류를 선택해주세요.");
			frm.zipcode_api_module[0].focus();
			return false;
		}else{
			if(frm.zipcode_api_module[0].checked == true){
				if(!frm.zipcode_api_key.value){
					alert("공공데이터포털 API Key를 입력해주세요.");
					frm.zipcode_api_key.focus();
					return false;
				}
			}
		}
	}
	if(confirm("정보를 수정하시겠습니까?    ")){
		jQuery("#setupFrom").submit();
	}
}
window.onload = function(){
	addrChange();
}