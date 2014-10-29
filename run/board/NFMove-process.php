<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if($curUserPermision != "administrator"){
	echo "
		<script type='text/javascript'>
		alert('권한이 없습니다.');
		self.close();
		</script>";
	exit;
}

if(empty($_POST['record'])) $_POST['record'] = 0;

$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $_POST['bname']));
$moveInfo = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $_POST['moveboard']));

if(!empty($brdSet->b_no) && !empty($moveInfo->b_no) && $brdSet->b_no != $moveInfo->b_no){
	$check = explode("_", $_POST['check']);

	for($i = 0; $i < count($check) - 1; $i++){

		$board = $wpdb->get_row($wpdb->prepare("select * from NFB_".$_POST['bname']."_board where no=%s", $check[$i]), ARRAY_A);

		$write_qry = "insert into NFB_".$_POST['moveboard']."_board (memnum, memlevel, writer, pass, write_date, title, content, hit, category, use_notice, use_html, use_secret, file1, file2, re_step, re_level, ip) values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
		$wpdb->query($wpdb->prepare($write_qry, $board['memnum'], $board['memlevel'], $board['writer'], $board['pass'], $board['write_date'], $board['title'], $board['content'], $board['hit'], $board['category'], $board['use_notice'], $board['use_html'], $board['use_secret'], $board['file1'], $board['file2'], $board['re_step'], $board['re_level'], $board['ip']));

		$ref_board = $wpdb->get_row("select max(no) from NFB_".$_POST['moveboard']."_board", ARRAY_N);
		
		if($board['listnum'] == "0"){
			$idx = $ref_board[0];
			$ref = $board['ref'];
			$listnum = 0;
			$movecheck = 0;

		}else{
			$idx = $ref_board[0];
			$ref = $ref_board[0];
			$listnum = 1;
			$movecheck = $brdSet->b_no."_".$board['ref']."_1";
		}

		$wpdb->query($wpdb->prepare("update NFB_".$_POST['moveboard']."_board set ref=%s, listnum=%s, movecheck=%s where no=%s", $ref, $listnum, $movecheck, $idx));
		$wpdb->query($wpdb->prepare("update NFB_board set b_list_count=b_list_count+1 where b_name=%s", $_POST['moveboard']));

		if($board['file1'] != ""){
			$fexe = substr($board['file1'], -3);
			$filename1 = NFB_UPLOAD_PATH."NFBoard/".$brdSet->b_no."_".$board['no']."_1.".$fexe;
			$newfile1 = NFB_UPLOAD_PATH."NFBoard/".$moveInfo->b_no."_".$idx."_1.".$fexe;
			copy($filename1, $newfile1);
		}
		if($board['file2'] != ""){
			$fexe = substr($board['file2'], -3);
			$filename2 = NFB_UPLOAD_PATH."NFBoard/".$brdSet->b_no."_".$board['no']."_2.".$fexe;
			$newfile2 = NFB_UPLOAD_PATH."NFBoard/".$moveInfo->b_no."_".$idx."_2.".$fexe;
			copy($filename2, $newfile2);
		}

		$cresult = $wpdb->get_results($wpdb->prepare("select * from NFB_".$_POST['bname']."_comment where parent=%s order by no asc", $check[$i]));
		$listcnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_".$_POST['bname']."_comment where parent=%s", $check[$i]));

		if($listcnt == 0){
		}else{
			foreach($cresult as $comment){
				$wpdb->query($wpdb->prepare("insert into NFB_".$_POST['moveboard']."_comment (parent, comm_parent, memnum, writer, pass, content, ip, depth, move_no, write_date) values (%s, '0', %s, %s, %s, %s, %s, %s, %s, %s)" , $idx, $comment->memnum, $comment->writer, $comment->pass, $comment->content, $comment->ip, $comment->depth, $comment->comm_parent, $comment->write_date));
				
				$comment_id = $wpdb->insert_id;

				if(empty($comment->depth) || $comment->depth == 0){
					$wpdb->query($wpdb->prepare("update NFB_".$_POST['moveboard']."_comment set comm_parent=%s where no=%s", $comment_id, $comment_id));
				}else if($comment->depth == 1){
					$comm_parent = $wpdb->get_var($wpdb->prepare("select no from NFB_".$_POST['moveboard']."_comment where move_no=%s and depth='0'", $comment->comm_parent));
					$wpdb->query($wpdb->prepare("update NFB".$_POST['moveboard']."_comment set comm_parent=%s where no=%s", $comm_parent, $comment_id));
				}
			}
		}

		if($_POST['mode'] == "move"){

			if($filename1) unlink($filename1);
			if($filename2) unlink($filename2);
			
			if($_POST['record'] == '1'){
				$wpdb->query($wpdb->prepare("update NFB_".$_POST['bname']."_board set title='본 게시물은 ".$current_user->user_login."님에 의해 ".$_POST['moveboard']." 게시판으로 이동되었습니다.', content='본 게시물은 ".$current_user->user_login."님에 의해 ".$_POST['moveboard']." 게시판으로 이동되었습니다.', file1='', file2='' where no=%s", $check[$i]));
			
			}else{
				$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_board where no=%s", $check[$i]));
				$wpdb->query($wpdb->prepare("update NFB_board set list_count=list_count-1 where b_name=%s", $_POST['bname']));
			}
			$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_comment where parent=%s", $check[$i]));
		}

	}

	$result = $wpdb->get_results("select no, ref from NFB_".$_POST['moveboard']."_board where movecheck='0'");
	$listcnt = $wpdb->get_var("select count(*) from NFB_".$_POST['moveboard']."_board where movecheck='0'");

	if($listcnt == 0){
	}else{
		foreach($result as $list){
			$check_sql = $wpdb->prepare("select no, movecheck from NFB_".$_POST['moveboard']."_board where movecheck=%s and listnum=1", $brdSet->b_no."_".$list->ref."_1");
			$check = $wpdb->get_row($check_sql);
			
			if(!empty($check->no)){
				$wpdb->query($wpdb->prepare("update NFB_".$_POST['moveboard']."_board set ref=%s, movecheck=%s where no=%s", $check->no, $check->movecheck, $list->no));
			}
		}
	}

	echo "
		<script type='text/javascript'>
		location.href = '".NFB_SITE_URL."?page_id=".$_POST['page_id']."&".build_param($_POST['bname'], "list", "", $_POST['page'], $_POST['keyfield'], $_POST['keyword'], $_POST['search_chk'], $_POST['cate'])."';
		</script>";
	exit;
}else{
	echo "
		<script type='text/javascript'>
		alert('정상적인 접근이 아닙니다.');
		history.back();
		</script>";
	exit;
}
?>