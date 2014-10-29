<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if(empty($_POST['bname'])){echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>";}
if(empty($_POST['no'])){echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>";}
if(empty($_POST['skin'])){echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>";}
if(empty($_POST['page'])) $_POST['page'] = 1;
if(empty($_POST['keyfield'])) $_POST['keyfield'] = "";
if(empty($_POST['keyword'])) $_POST['keyword'] = "";
if(empty($_POST['search_chk'])) $_POST['search_chk'] = "";

$action_url = get_permalink($_POST['page_id']);

if(preg_match("/\bpage_id\b/", $action_url)) $link_add = "&";
else $link_add = "?";

$board = $wpdb->get_row($wpdb->prepare("select * from NFB_".$_POST['bname']."_board where no=%s", $_POST['no']));

if(empty($board->no)){echo "<script type='text/javascript'>alert('해당 게시물이 존재하지 않습니다.');history.back();</script>";}

if(empty($_POST['pwd'])){
	echo "
		<script type='text/javascript'>
		location.href = '".NFB_HOME_URL."/?NFPage=board-pass-check&page_id=".$_POST['page_id']."&".build_param($_POST['bname'], $_POST['mode'], $_POST['no'], $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."&wrong=1';
		</script>";
	exit;

}else{
	$write_pass = $wpdb->get_var($wpdb->prepare("select password(%s)", $_POST['pwd']));

	if($write_pass == $board->pass){
?>
<html>
<body onload="document.passForm.submit();">
<form name="passForm" method="post" action="<?php echo $action_url.$link_add?><?php echo build_param($_POST['bname'], "view", $_POST['no'], $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])?>">
<input type="hidden" name="passcheck" value="1" />
</form>
</body>
</html>
<?
	}else{
		echo "
			<script type='text/javascript'>
			location.href = '".NFB_HOME_URL."/?NFPage=board-pass-check&page_id=".$_POST['page_id']."&".build_param($_POST['bname'], $_POST['mode'], $_POST['no'], $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."&wrong=2';
			</script>";
		exit;
	}
}
?>