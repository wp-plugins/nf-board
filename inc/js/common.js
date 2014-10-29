// 팝업 띄우기
function open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable){
	toolbar_str = toolbar?'yes':'no';
	menubar_str = menubar?'yes':'no';
	statusbar_str = statusbar?'yes':'no';
	scrollbar_str = scrollbar?'yes':'no';
	resizable_str = resizable?'yes':'no';
	window.open(url, name, 'left=' + left + ',top=' + top + ',width=' + width + ',height=' + height + ',toolbar=' + toolbar_str + ',menubar=' + menubar_str + ',status=' + statusbar_str + ',scrollbars=' + scrollbar_str + ',resizable=' + resizable_str);
}

// html 사용여부
function check_use_html(obj){
	var c_n;
	if(!obj.checked){
		obj.value = 1;
	
	}else{
		c_n = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
		if(c_n) obj.value = 1;
		else obj.value = 2;
	}
}

// 주석 사용할 수 없게 하기
function tag_check(a){
	var searchfrm = a.value;
	searchfrm = " " + searchfrm;
	var search = "\<\!\-\-";
	find1 = 0; find2 = 0;
	if(searchfrm != ""){
		while(find1 >= 0){
			find1 = searchfrm.indexOf(search, find2);
			if(find1 > 0){
				alert("\<\!\-\- 는 사용할 수 없습니다!");
				find2 = find1 + 1;
				a.value = "";
				return false;
			}
		}
	}
}

// 숫자만 입력
function checkForNumber(){
	var key = event.keyCode;
	if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
		event.returnValue = false;
	}
}

// 주석사용 방지
function tag_check(a){
	var searchfrm = a.value;
	searchfrm = " " + searchfrm;
	var search = "\<\!\-\-";
	find1 = 0;
	find2 = 0;
	
	if(searchfrm != ""){
		while(find1 >= 0){
			find1 = searchfrm.indexOf(search, find2);
			if(find1 > 0){
				alert("\<\!\-\- 는 사용할 수 없습니다!");
				find2 = find1 + 1;
				a.value = "";
				return false;
			}
		}
	}
}

// 공백 체크
function ChkSpace(strValue){
	if(strValue.indexOf(" ") >= 0){
		return true;
	}else{
		return false;
	}
}

// 한글 체크
function ChkHan(strValue){
	for(i = 0; i < strValue.length; i++){
		var a = strValue.charCodeAt(i);
		if(a > 128){
			return true;
		}else{
			return false;
		}
	}
}

// 메일 형식 체크
function ChkMail(strValue){
	if(ChkSpace(strValue)){
		return false;
	}else if(strValue.indexOf("/") != -1 || strValue.indexOf(";") != -1 || ChkHan(strValue)){
		return false;
	}else if((strValue.length != 0) && (strValue.search(/(\S+)@(\S+)\.(\S+)/) == -1)){
		return false;
	}else{ 
		return true;
	}
}