<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$check = $_REQUEST['check'];
$bname = $_REQUEST['bname'];
$page = $_REQUEST['page'];
$keyfield = $_REQUEST['keyfield'];
$keyword = $_REQUEST['keyword'];
$search_chk = $_REQUEST['search_chk'];
$cate = $_REQUEST['cate'];

$delete_title = array();
for($i = 0; $i < count($check); $i++){
	$config = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $bname));
	$board = $wpdb->get_row($wpdb->prepare("select * from NFB_".$bname."_board where no=%s", $check[$i]));
	
	$delete_title[] = $board->title;

	if(!empty($board->write_date)){
		$tmp1 = explode(" ", $board->write_date);
		$tmp2 = explode("-", $tmp1[0]);
		$tmp3 = explode(":", $tmp1[1]);
		$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
	}
	
	if($board->re_level != "0") $pict = $unique."_re";
	else $pict = $check[$i]."_";
	
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

	$del_sql1 = $wpdb->prepare("delete from NFB_".$bname."_board where no=%s", $check[$i]);
	$del_sql2 = $wpdb->prepare("delete from NFB_".$bname."_comment where parent=%s", $check[$i]);
	$del_sql3 = $wpdb->prepare("update NFB_board set b_list_count=b_list_count-1 where b_name=%s", $bname);

	$wpdb->query($del_sql1);
	$wpdb->query($del_sql2);
	$wpdb->query($del_sql3);
}

echo "
	<script type='text/javascript'>
	location.href = '".NFB_SITE_URL."?page_id=".$_REQUEST['page_id']."&".build_param($_REQUEST['bname'], "list", "", $_REQUEST['page'], $_REQUEST['keyfield'], $_REQUEST['keyword'], $_REQUEST['search_chk'], $_REQUEST['cate'])."';
	</script>";
?>