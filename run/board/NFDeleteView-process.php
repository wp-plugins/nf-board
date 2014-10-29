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

if((empty($curUserPermision) || $curUserPermision != "administrator") && empty($_POST['pwd'])){
	echo "
		<script type='text/javascript'>
		location.href = '".NFB_WEB."templates/board/".$_POST['skin']."/tf_delete.php?page_id=".$_POST['page_id']."&".build_param($_POST['bname'], $_POST['mode'], $_POST['no'], $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."&wrong=1';
		</script>";
	exit;

}else{
	$config = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $_POST['bname']));
	$board = $wpdb->get_row($wpdb->prepare("select * from NFB_".$_POST['bname']."_board where no=%s", $_POST['no']));

	if(!empty($board->write_date)){
		$tmp1 = explode(" ", $board->write_date);
		$tmp2 = explode("-", $tmp1[0]);
		$tmp3 = explode(":", $tmp1[1]);
		$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
	}

	if($board->re_level != "0") $pict = $unique."_re";
	else $pict = $_POST['no']."_";

	if($curUserPermision == "administrator"){
		if(!empty($board->file1)){
			$filekind1 = substr($board->file1, -3);
			$filename1 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."1.".$filekind1;
			@unlink($filename1);
		}
		if(!empty($board->file2)){
			$filekind2 = substr($board->file2, -3);
			$filename2 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."2.".$filekind2;
			@unlink($filename2);
		}

		$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_board where no=%s", $_POST['no']));
		$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_comment where parent=%s", $_POST['no']));
		$wpdb->query($wpdb->prepare("update NFB_board set list_count=list_count-1 where b_name=%s", $_POST['bname']));

	}else{
		$pass = $wpdb->get_var($wpdb->prepare("select pass from NFB_".$_POST['bname']."_board where no=%s", $_POST['no']));
		
		if(!empty($current_user->ID)){
			$mem_pass = $wpdb->get_var($wpdb->prepare("select user_pass from ".$wpdb->users." where ID=%s", $current_user->ID));
		}else{
			$mem_pass = $wpdb->get_var($wpdb->prepare("select password(%s)", $_POST['pwd']));
		}
		
		if($pass == $mem_pass){
			if($board->file1 != ""){
				$filekind1 = substr($board->file1, -3);
				$filename1 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."1.".$filekind1;
				@unlink($filename1);
			}
			if($board->file2 != ""){
				$filekind2 = substr($board->file2, -3);
				$filename2 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."2.".$filekind2;
				@unlink($filename2);
			}

			$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_board where no=%s", $_POST['no']));
			$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_comment where parent=%s", $_POST['no']));
			$wpdb->query($wpdb->prepare("update NFB_board set b_list_count=b_list_count-1 where b_name=%s", $_POST['bname']));
		
		}else{
			echo "
				<script type='text/javascript'>
				location.href = '".NFB_HOME_URL."/?NFPage=board-delete&page_id=".$_POST['page_id']."&".build_param($_POST['bname'], $_POST['mode'], $_POST['no'], $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."&wrong=2';
				</script>";
			exit;
		}
	}

	echo "
		<script type='text/javascript'>
		top.location.href = '".NFB_SITE_URL."?page_id=".$_POST['page_id']."&".build_param($_POST['bname'], "list", "", $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."';
		</script>";
	exit;
}
?>