function memberDelete(tNo){
	if(tNo > 0){
		if(confirm('해당 회원을 삭제하시겠습니까?   ')){
			jQuery("#delNo").val(tNo);
			jQuery("#joinForm").attr("action", "admin.php?page=NFMemberList");
			jQuery("#joinForm").submit();
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
		alert('삭제할 회원을 선택해 주세요.   ');
		return false;
	}
	if(confirm('선택한 회원을 삭제하시겠습니까?   ')){
		jQuery("#joinForm").attr("action", "admin.php?page=NFMemberList");
		jQuery("#joinForm").submit();
	}
}
function adminModifySubmit(){
	var today = new Date();
	if(jQuery("#pass").val() != ""){
		if(parseInt(jQuery("#pass_min_len").val()) > 0) {
			if(jQuery("#pass").val().length < parseInt(jQuery("#pass_min_len").val())){
				alert("비밀번호는 "+jQuery("#pass_min_len").val()+"자 이상으로 입력해주세요.");
				jQuery("#pass").focus();
				return false; 
			}
		}
		if(jQuery("#pass").val().length > 16){
			alert("비밀번호는 16자 이하로 입력해주세요.");
			jQuery("#pass").focus();
			return false; 
		}
		if(jQuery("#repass").val() == ""){
			alert("비밀번호 확인을 위해 다시 한번더 입력해주세요.");
			jQuery("#repass").focus();
			return false; 
		}
		if(jQuery("#repass").val() != jQuery("#pass").val()){
			alert("비밀번호가 일치하지 않습니다.");
			jQuery("#repass").focus();
			return false; 
		}
	}
	if(jQuery("#require_name").val()=="11") {
		if(!jQuery("#name").val()){
			alert("이름을 입력해주세요.");
			jQuery("#name").focus();
			return false;
		}
	}
	if(jQuery("#require_birth").val()=="11") {
		if(!jQuery("#birth_year").val()){
			alert("생년월일(년)을 입력해주세요.");
			jQuery("#birth_year").focus();
			return false;
		}
		if(parseInt(jQuery("#birth_year").val()) < 1900 || parseInt(jQuery("#birth_year").val()) > today.getFullYear()){
			alert("생년월일(년)을 바르게 입력해주세요.");
			jQuery("#birth_year").focus();
			jQuery("#birth_year").val("");
			return false; 
		}
		if(!jQuery("#birth_month").val()){
			alert("생년월일(월)을 입력해주세요.");
			jQuery("#birth_month").focus();
			return false;
		}
		if(parseInt(jQuery("#birth_month").val()) > 12 || parseInt(jQuery("#birth_month").val()) < 1){
			alert("생년월일(월)을 바르게 입력해주세요.");
			jQuery("#birth_month").focus();
			jQuery("#birth_month").val("");
			return false; 
		}
		if(!jQuery("#birth_day").val()){
			alert("생년월일(일)을 입력해주세요.");
			jQuery("#birth_day").focus();
			return false;
		}
		if(parseInt(jQuery("#birth_day").val()) > 31 || parseInt(jQuery("#birth_day").val()) < 1){
			alert("생년월일(일)을 바르게 입력해주세요.");
			jQuery("#birth_day").focus();
			jQuery("#birth_day").val("");
			return false; 
		}
	}
	if(jQuery("#require_sex").val()=="11") {
		if(jQuery(":radio[name='sex']:checked").length == 0){
			alert("성별을 선택해주세요.");
			return false;
		}
	}
	if(jQuery("#require_addr").val()=="11") {
		if(!jQuery("#zipcode").val()){
			alert("우편번호를 선택해주세요.");
			return false;
		}
		if(!jQuery("#addr1").val()){
			alert("우편번호를 입력해주세요.");
			jQuery("#addr1").focus();
			return false;
		}
		if(!jQuery("#addr2").val()){
			alert("나머지주소를 입력해주세요.");
			jQuery("#addr2").focus();
			return false;
		}
	}
	if(jQuery("#require_phone").val()=="11") {
		if(!jQuery("#phone_1").val()){
			alert("전화번호를 입력해주세요.");
			jQuery("#phone_1").focus();
			return false;
		}
		if(!jQuery("#phone_2").val()){
			alert("전화번호를 입력해주세요.");
			jQuery("#phone_2").focus();
			return false;
		}
		if(!jQuery("#phone_3").val()){
			alert("전화번호를 입력해주세요.");
			jQuery("#phone_3").focus();
			return false;
		}
	}
	if(jQuery("#require_hp").val()=="11") {
		if(!jQuery("#hp_1").val()){
			alert("휴대전화번호를 입력해주세요.");
			jQuery("#hp_1").focus();
			return false;
		}
		if(!jQuery("#hp_2").val()){
			alert("휴대전화번호를 입력해주세요.");
			jQuery("#hp_2").focus();
			return false;
		}
		if(!jQuery("#hp_3").val()){
			alert("휴대전화번호를 입력해주세요.");
			jQuery("#hp_3").focus();
			return false;
		}
	}
	if(!jQuery("#email").val()){
		alert("이메일을 입력해주세요.");
		jQuery("#email").focus();
		return false;
	}
	if(!ChkMail(jQuery("#email").val())){
		alert("이메일 형식이 올바르지 않습니다.");
		return false;
	}
	if(jQuery("#require_job").val()=="11") {
		if(!jQuery("#job").val()){
			alert("직업을 입력해주세요.");
			jQuery("#job").focus();
			return false;
		}
	}
	if(confirm("정보를 수정하시겠습니까?    ")){
		jQuery("#joinForm").attr("action", jQuery("#actionURL").val());
		jQuery("#joinForm").submit();
	}
}
function zipcodePopup(){
	window.open(jQuery("#NFB_WEB").val()+"templates/member/"+jQuery("#skinname").val()+"/zipcode.php", "zipcodePopup", "width=400,height=400,scrollbars=yes");
}
if(jQuery("#use_zipcode_api").val() == "1" && jQuery("#zipcode_api_module").val() == "2"){
	function openDaumPostcode(){
		new daum.Postcode({
			oncomplete: function(data){
				jQuery('#zipcode').val(data.postcode1 + "-" + data.postcode2);
				jQuery('#addr1').val(data.address);
				jQuery('#addr2').focus();
			}
		}).open();
	}
}