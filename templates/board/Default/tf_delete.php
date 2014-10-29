<?php
@session_start();
$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_".$_GET['bname']."_board where no=%s", $_GET['no']));

if(empty($rows->no)){
	echo "
		<script type='text/javascript'>
		alert('정상적인 접근이 아닙니다.');
		history.back();
		</script>";
	exit;
}

if($_GET['wrong'] == "1") $message = "비밀번호를 바르게 입력해주세요.";
else if($_GET['wrong'] == "2") $message = "비밀번호가 일치하지 않습니다.";

$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $_GET['bname']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko-KR">
<head>
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='viewport' content='width=device-width' />
<title>게시물 삭제</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
<style>
body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button,select{margin:0;padding:0}
body,input,textarea,select,button,table{font-family:'돋움',Dotum,AppleGothic,sans-serif;font-size:12px;color:#545454;}
img,fieldset{border:0}
ul,ol{list-style:none}
em,address{font-style:normal}
a{text-decoration:none;color:#666;}
a:hover,a:active,a:focus{text-decoration:underline;color:#666;}

.pw_box{position:relative;width:280px;margin:0;border:3px solid #dcdcdc;background:#f9f9f9;padding:0px;margin:0 auto;}
.pw_box fieldset{margin:0;padding:0;border:0}
.pw_box legend{visibility:hidden;position:absolute;top:0;left:0;width:1px;height:1px;font-size:0;line-height:0}
.pw_box h2 {background-color:#e5e5e5;width:100%;text-align:center;border-bottom:1px dotted #ccc;padding:10px 0}
.pw_box .item{position:relative;}
.pw_box .i_label{display:none;display:block;position:static;top:9px;font:bold 11px Tahoma}
.pw_box .i_text{display:none;display:block;border:1px solid #b7b7b7;border-right-color:#e1e1e1;border-bottom-color:#e1e1e1;background:#fff;font:14px "돋움",Tahoma;padding:5px;color:#767676;margin:10px;vertical-align:middle;text-align:center;width:245px;}
.pw_box .open_alert {margin:15px;padding:5px;text-align:center;font-size:11px;color:#ed1c24;background:#ffeeee;border:1px solid #ed1c24;font-weight:bold;}
.pw_btn_w {margin:15px 0;text-align:center;}
.pw_box_btn {
	cursor:pointer;
	font-size:14px;
	font-family:돋움;
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:gradient( linear, left top, left bottom, color-stop(0.05, #f0f0f0), color-stop(1, #e0e0e0) );
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f0f0f0), color-stop(1, #e0e0e0) );
	background:-moz-linear-gradient( center top, #f0f0f0 5%, #e0e0e0 100% );
	background:-ms-linear-gradient( #f0f0f0 5%, #e0e0e0 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f0f0f0', endColorstr='#e0e0e0');
	background-color:#f0f0f0;
	-webkit-border-top-left-radius:3px;
	-moz-border-radius-topleft:3px;
	border-top-left-radius:3px;
	-webkit-border-top-right-radius:3px;
	-moz-border-radius-topright:3px;
	border-top-right-radius:3px;
	-webkit-border-bottom-right-radius:3px;
	-moz-border-radius-bottomright:3px;
	border-bottom-right-radius:3px;
	-webkit-border-bottom-right-radius:3px;
	-moz-border-radius-bottomleft:3px;
	border-bottom-left-radius:3px;
	text-indent:0;
	border:1px solid #e0e0e0;
	display:inline-block;
	color:#666;
	font-weight:bold;
	font-style:normal;
	height:30px;
	line-height:30px;
	width:70px;
	margin-right:2px;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #fff;
}
.pw_box_btn:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #e0e0e0), color-stop(1, #f0f0f0) );
	background:-moz-linear-gradient( center top, #e0e0e0 5%, #f0f0f0 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e0e0e0', endColorstr='#f0f0f0');
	background:-ms-linear-gradient( #e0e0e0 5%, #f0f0f0 100% );
	background-color:#e0e0e0;
.pw_box_btn:active {
	position:relative;
	top:1px;
}
</style>
<script type="text/javascript" src="<?php echo includes_url()?>js/jquery/jquery.js"></script>
</head>
<body>
<?php if($rows->memnum == 0 && $curUserPermision != "administrator"){?>
<script type="text/javascript">
function check(){
	if(!jQuery("#pwd").val()){
		jQuery("#error_box").show().html("비밀번호를 입력해주세요.");
		jQuery("#pwd").focus();
		return false;
	}
	if(confirm("삭제하시겠습니까?")){
		document.pwd_frm.submit();
	}
}
</script>

<form name="pwd_frm" method="post" action="<?php echo NFB_HOME_URL?>/?NFPage=board-delete-view-process">
<input type="hidden" name="page_id" value="<?php echo $_GET['page_id']?>" />
<input type="hidden" name="bname" value="<?php echo $_GET['bname']?>" />
<input type="hidden" name="skin" value="<?php echo $brdSet->b_skin?>" />
<input type="hidden" name="mode" value="<?php echo $_GET['mode']?>" />
<input type="hidden" name="no" value="<?php echo $_GET['no']?>" />
<input type="hidden" name="page" value="<?php echo $_GET['page']?>" />
<input type="hidden" name="keyfield" value="<?php echo $_GET['keyfield']?>" />
<input type="hidden" name="keyword" value="<?php echo $_GET['keyword']?>" />
<input type="hidden" name="search_chk" value="<?php echo $_GET['search_chk']?>" />
<div style="padding-top:30px;">
	<div class="pw_box">
		<h2 class="tit">비밀번호 확인</h2>
		<fieldset>
			<legend>password</legend>
			<div class="item"><label><input name="pwd" id="pwd" type="password" class="i_text upw" value="" onfocus="this.value='';return true;" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').hide();}"/></label></div>
			<p id="error_box" class="open_alert" <?php echo (empty($_GET['wrong']))?"style='display:none;'":""?>><?php if(!empty($_GET['wrong'])) echo $message;?></p>
			<p class="pw_btn_w"><a href="javascript:;" onclick="check();"><input title="확인" class="pw_box_btn" type="button" value="확인" /></a>&nbsp;<a href="javascript:;" onclick="jQuery('#cancel_frm').submit();"><input title="취소" class="pw_box_btn" type="button" value="취소" /></a></p>
		</fieldset>
	</div>
</div>
</form>
<form name="cancel_frm" id="cancel_frm" method="post" action="<?php echo NFB_SITE_URL?>?page_id=<?php echo $_GET['page_id']?>&<?php echo build_param($_GET['bname'], "view", $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])?>">
<input type="hidden" name="passcheck" value="1" />
</form>
<?php }else{?>
<form name="pwd_frm" method="post" action="<?php echo NFB_HOME_URL?>/?NFPage=board-delete-view-process">
<input type="hidden" name="page_id" value="<?php echo $_GET['page_id']?>" />
<input type="hidden" name="bname" value="<?php echo $_GET['bname']?>" />
<input type="hidden" name="skin" value="<?php echo $brdSet->b_skin?>" />
<input type="hidden" name="mode" value="<?php echo $_GET['mode']?>" />
<input type="hidden" name="no" value="<?php echo $_GET['no']?>" />
<input type="hidden" name="page" value="<?php echo $_GET['page']?>" />
<input type="hidden" name="keyfield" value="<?php echo $_GET['keyfield']?>" />
<input type="hidden" name="keyword" value="<?php echo $_GET['keyword']?>" />
<input type="hidden" name="search_chk" value="<?php echo $_GET['search_chk']?>" />
</form>
<script type="text/javascript">
if(confirm("삭제하시겠습니까?")){
	document.pwd_frm.submit();	
}else{
	location.href = "<?php echo NFB_SITE_URL?>?page_id=<?php echo $_GET['page_id']?>&<?php echo build_param($_GET['bname'], $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])?>";
}
</script>
<?php }?>
</body>
</html>