<?php 
@session_start();

if(empty($_POST['bname'])){
	echo "fail";exit;	
}else{
	if(empty($_POST['page'])) $_POST['page'] = 1;
	if(empty($_POST['keyfield'])) $_POST['keyfield'] = "";
	if(empty($_POST['keyword'])) $_POST['keyword'] = "";
	if(empty($_POST['search_chk'])) $_POST['search_chk'] = "";
	if(empty($_POST['cate'])) $_POST['cate'] = "";
	echo build_param($_POST['bname'], 'list', '', $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate']);exit;
}
?>