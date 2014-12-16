<?php
if(!function_exists('NFBoardList')){
	function NFBoardList(){
		global $wpdb;

		wp_enqueue_script( 'ajax-script', plugins_url('/',__FILE__). 'inc/js/admin-board.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		if(!empty($_POST['check'])) $check = $_POST['check'];
		if(!empty($_POST['tBatch1'])) $tBatch1 = trim($_POST['tBatch1']);
		if(!empty($_POST['tBatch2'])) $tBatch2 = trim($_POST['tBatch2']);

		if((!empty($check) && count($check) > 0) && ($tBatch1 == 'remove' or $tBatch2 == 'remove')){
			$delete_type = "select";
			
			$delete_no = array();
			for($i = 0; $i < count($check); $i++){
				$b_no = $check[$i];
				$oldData = $wpdb->get_row($wpdb->prepare("select b_name from NFB_board where b_no=%s", $b_no));
				
				if(!empty($b_no) && $oldData->b_name){
					$wpdb->query($wpdb->prepare("delete from NFB_board where b_no=%s", $b_no));

					$cntBoard = $wpdb->get_var("select count(*) as cnt from information_schema.tables where table_name='NFB_".$oldData->b_name."_board'");
					if($cntBoard > 0){
						$wpdb->query("drop table NFB_".$oldData->b_name."_board");
					}

					$cntComment = $wpdb->get_var("select count(*) as cnt from information_schema.tables where table_name='NFB_".$oldData->b_name."_comment'");
					if($cntComment > 0){
						$wpdb->query("drop table NFB_".$oldData->b_name."_comment");
					}
				}
			}
		
		}else{
			$delete_type = "one";
			if(!empty($_POST['delNo'])){
				$b_no = trim($_POST['delNo']);
				$oldData = $wpdb->get_row($wpdb->prepare("select b_name from NFB_board where b_no=%s", $b_no));
				
				if(!empty($b_no) && $oldData->b_name){
					$wpdb->query($wpdb->prepare("delete from NFB_board where b_no=%s", $b_no));

					$cntBoard = $wpdb->get_var("select count(*) as cnt from information_schema.tables where table_name='NFB_".$oldData->b_name."_board'");
					if($cntBoard > 0){
						$wpdb->query("drop table NFB_".$oldData->b_name."_board");
					}

					$cntComment = $wpdb->get_var("select count(*) as cnt from information_schema.tables where table_name='NFB_".$oldData->b_name."_comment'");
					if($cntComment > 0){
						$wpdb->query("drop table NFB_".$oldData->b_name."_comment");
					}
				}
			}
		}

		$NFB_Board = new NFB_Board();
		require_once(NFB_ABS."setup/NFBoardList.php");
	}
}

if(!function_exists('NFBoardAdd')){
	function NFBoardAdd(){
		global $wpdb;

		wp_enqueue_script( 'ajax-script', plugins_url('/',__FILE__). 'inc/js/admin-board.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		$tMode = "";

		if(empty($_POST['b_hit_hide'])) $_POST['b_hit_hide'] = 0;
		if(empty($_POST['b_writer_hide'])) $_POST['b_writer_hide'] = 0;
		if(empty($_POST['b_comment_use'])) $_POST['b_comment_use'] = 0;
		if(empty($_POST['b_secret_use'])) $_POST['b_secret_use'] = 0;
		if(empty($_POST['b_notice_use'])) $_POST['b_notice_use'] = 0;
		if(empty($_POST['b_pds_use'])) $_POST['b_pds_use'] = 0;
		if(empty($_POST['b_agree_use'])) $_POST['b_agree_use'] = 0;
		if(empty($_POST['b_filter_use'])) $_POST['b_filter_use'] = 0;
		if(empty($_POST['b_facebook_use'])) $_POST['b_facebook_use'] = 0;
		if(empty($_POST['b_twitter_use'])) $_POST['b_twitter_use'] = 0;
		if(empty($_POST['b_hms_use'])) $_POST['b_hms_use'] = 0;
		if(empty($_POST['b_seo_use'])) $_POST['b_seo_use'] = 0;
		if($_POST['b_filter_use'] == 0){
			$_POST['b_filter_list'] = "";
		}
		if($_POST['b_seo_use'] == 0){
			$_POST['b_seo_title'] = $_POST['b_seo_desc'] = $_POST['b_seo_keywords'] = "";
		}

		if(!empty($_POST['b_name']) && !empty($_POST['b_skin'])){
			$_POST['b_regdate'] = time();
			
			$existCnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_board where b_name=%s", $_POST['b_name']));

			if(empty($_POST['b_seo_use']) || $_POST['b_seo_use'] == 0){
				$_POST['b_seo_title'] = "";
				$_POST['b_seo_desc'] = "";
				$_POST['b_seo_keywords'] = "";
			}

			if(empty($_POST['b_no']) and $existCnt <= 0){
				$sql = $wpdb->prepare("insert into NFB_board (b_name, b_skin, b_type, b_editor, b_width, b_align, b_psize, b_category, b_filter_use, b_filter_list, b_pds_use, b_agree_use, b_filesize, b_comment_use, b_secret_use, b_notice_use, b_spam, b_read_lv, b_comment_lv, b_write_lv, b_seo_use, b_seo_title, b_seo_desc, b_seo_keywords, b_hit_hide, b_writer_hide, b_facebook_use, b_twitter_use, b_hms_use, b_list_count, b_regdate, b_latest_page) values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 0, %s, %s)", $_POST['b_name'], $_POST['b_skin'], $_POST['b_type'], $_POST['b_editor'], $_POST['b_width'], $_POST['b_align'], $_POST['b_psize'], $_POST['b_category'], $_POST['b_filter_use'], $_POST['b_filter_list'], $_POST['b_pds_use'], $_POST['b_agree_use'], $_POST['b_filesize'], $_POST['b_comment_use'], $_POST['b_secret_use'], $_POST['b_notice_use'], $_POST['b_spam'], $_POST['b_read_lv'], $_POST['b_comment_lv'], $_POST['b_write_lv'], $_POST['b_seo_use'], $_POST['b_seo_title'], $_POST['b_seo_desc'], $_POST['b_seo_keywords'], $_POST['b_hit_hide'], $_POST['b_writer_hide'], $_POST['b_facebook_use'], $_POST['b_twitter_use'], $_POST['b_hms_use'], $_POST['b_regdate'], $_POST['b_latest_page']);
				$wpdb->query($sql);

				$tmpBoardData = $wpdb->get_row($wpdb->prepare("select b_no from NFB_board where b_name=%s and b_skin=%s", $_POST['b_name'], $_POST['b_skin']));
				$b_no = $tmpBoardData->b_no;
				$tMode = "insert";

				if(!empty($b_no) and $_POST['b_name']){
					$sql2 = "create table if not exists NFB_".$_POST['b_name']."_board (
						no int unsigned not null auto_increment, 
						memnum int(11) not null default '0', 
						memlevel varchar(20) default null, 
						writer varchar(20) not null, 
						pass varchar(50) not null, 
						email varchar(50) default null,
						phone varchar(50) default null,
						write_date datetime default null, 
						title text not null, 
						content text not null, 
						hit int default '0', 
						category varchar(20) not null default '일반', 
						use_notice char(1) default '0', 
						use_html char(1) not null default '0', 
						use_secret char(1) default '0',
						file1 varchar(100) default null, 
						file2 varchar(100) default null, 
						ref int default null, 
						re_step int not null default '0', 
						re_level int not null default '0', 
						listnum char(1) not null default '0', 
						movecheck varchar(10) default null, 
						ip varchar(15) default null, 
						primary key (no)) default charset=utf8";
					$wpdb->query($sql2);
					$sql3 = "create table if not exists NFB_".$_POST['b_name']."_comment (
						no int unsigned not null auto_increment,
						parent int not null,
						comm_parent int not null default '0',
						memnum int(11) not null default '0', 
						writer varchar(20) default null,
						pass varchar(50) default null,
						content text default null,
						ip varchar(15) default null,
						depth int not null default '0',
						move_no int not null default '0',
						write_date datetime default null, 
						primary key (no)) default charset=utf8";
					$wpdb->query($sql3);
				}
			
			}else{
				if(!empty($_POST['b_no'])){
					$sql = $wpdb->prepare("update NFB_board set b_skin=%s, b_type=%s, b_editor=%s, b_width=%s, b_align=%s, b_psize=%s, b_category=%s, b_filter_use=%s, b_filter_list=%s, b_pds_use=%s, b_agree_use =%s, b_filesize=%s, b_comment_use=%s, b_secret_use=%s, b_notice_use=%s, b_spam=%s, b_read_lv=%s, b_comment_lv=%s, b_write_lv=%s, b_seo_use=%s, b_seo_title=%s, b_seo_desc=%s, b_seo_keywords=%s, b_hit_hide=%s, b_writer_hide=%s, b_facebook_use=%s, b_twitter_use=%s, b_hms_use=%s, b_latest_page=%s where b_no=%s", $_POST['b_skin'], $_POST['b_type'], $_POST['b_editor'], $_POST['b_width'], $_POST['b_align'], $_POST['b_psize'], $_POST['b_category'], $_POST['b_filter_use'], $_POST['b_filter_list'], $_POST['b_pds_use'], $_POST['b_agree_use'], $_POST['b_filesize'], $_POST['b_comment_use'], $_POST['b_secret_use'], $_POST['b_notice_use'], $_POST['b_spam'], $_POST['b_read_lv'], $_POST['b_comment_lv'], $_POST['b_write_lv'], $_POST['b_seo_use'], $_POST['b_seo_title'], $_POST['b_seo_desc'], $_POST['b_seo_keywords'], $_POST['b_hit_hide'], $_POST['b_writer_hide'], $_POST['b_facebook_use'], $_POST['b_twitter_use'], $_POST['b_hms_use'], $_POST['b_latest_page'], $_POST['b_no']);
					$wpdb->query($sql);
					$b_no = $_POST['b_no'];
					$tMode = "modify";
				}
			}

		}else if(!empty($_GET['b_no'])){  
			$b_no = $_GET['b_no'];
		}

		if(!empty($b_no) and $b_no > 0){
			$data = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_no=%s", $b_no));
		}
		require_once(NFB_ABS."setup/NFBoardAdd.php");
	}
}

if(!function_exists('NFBoardSetup')){
	function NFBoardSetup(){
		global $wpdb;

		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');

		$config = $wpdb->get_row("select * from NFB_setup", ARRAY_A);
		$EDIT_CONFIG_URL = NFB_SETUP."&mode=edit";

		if(!empty($_GET['mode']) && $_GET['mode'] == "edit"){

			if(empty($_POST['use_name'])) $_POST['use_name'] = 0;
			if(empty($_POST['validate_name'])) $_POST['validate_name'] = 0;
			if(empty($_POST['use_addr'])) $_POST['use_addr'] = 0;
			if(empty($_POST['validate_addr'])) $_POST['validate_addr'] = 0;
			if(empty($_POST['use_zipcode_api'])) $_POST['use_zipcode_api'] = 0;
			if(empty($_POST['zipcode_api_module'])) $_POST['zipcode_api_module'] = 0;
			if($_POST['zipcode_api_module'] == 0) $_POST['zipcode_api_key'] = "";
			if(empty($_POST['use_birth'])) $_POST['use_birth'] = 0;
			if(empty($_POST['validate_birth'])) $_POST['validate_birth'] = 0;
			if(empty($_POST['use_phone'])) $_POST['use_phone'] = 0;
			if(empty($_POST['validate_phone'])) $_POST['validate_phone'] = 0;
			if(empty($_POST['use_hp'])) $_POST['use_hp'] = 0;
			if(empty($_POST['validate_hp'])) $_POST['validate_hp'] = 0;
			if(empty($_POST['use_sex'])) $_POST['use_sex'] = 0;
			if(empty($_POST['validate_sex'])) $_POST['validate_sex'] = 0;
			if(empty($_POST['use_job'])) $_POST['use_job'] = 0;
			if(empty($_POST['validate_job'])) $_POST['validate_job'] = 0;
			if(empty($_POST['use_join_email'])) $_POST['use_join_email'] = 0;
			if(empty($_POST['use_ssl'])) $_POST['use_ssl'] = 0;
			
			if(!ctype_digit($_POST['table_width'])){echo "<script type='text/javascript'>alert('가로 사이즈는 숫자만 입력가능합니다.');history.back();</script>";exit;}
			if(!ctype_digit($_POST['id_min_len'])){echo "<script type='text/javascript'>alert('아이디 최소길이는 숫자만 입력가능합니다.');history.back();</script>";exit;}
			if(!ctype_digit($_POST['pass_min_len'])){echo "<script type='text/javascript'>alert('비밀번호 최소길이는 숫자만 입력가능합니다.');history.back();</script>";exit;}
			
			if(is_array($config)){
				if(!empty($_POST['mail_logo'])) $logoval = $_POST['mail_logo'];
				else $logoval = "";
			
				if(!empty($_POST['mail_logo_del']) && $_POST['mail_logo_del'] == 1) $logoval = "";

				$wpdb->query($wpdb->prepare("update NFB_setup set skinname=%s, table_width=%s, table_align=%s, use_name=%s, validate_name=%s, use_addr=%s, validate_addr=%s, use_zipcode_api=%s, use_birth=%s, validate_birth=%s, use_phone=%s, validate_phone=%s, use_hp=%s, validate_hp=%s, use_sex=%s, validate_sex=%s, use_job=%s, validate_job=%s, zipcode_api_module=%s, zipcode_api_key=%s,  id_min_len=%s, pass_min_len=%s, join_not_id=%s, use_join_email=%s, from_email=%s, from_name=%s, join_email_title=%s, join_email_content=%s, join_agreement=%s, join_private=%s,use_ssl=%s, ssl_domain=%s, ssl_port=%s, join_redirect=%s, mail_logo=%s", $_POST['skinname'], $_POST['table_width'], $_POST['table_align'], $_POST['use_name'], $_POST['validate_name'], $_POST['use_addr'], $_POST['validate_addr'], $_POST['use_zipcode_api'], $_POST['use_birth'], $_POST['validate_birth'], $_POST['use_phone'], $_POST['validate_phone'], $_POST['use_hp'], $_POST['validate_hp'], $_POST['use_sex'], $_POST['validate_sex'], $_POST['use_job'], $_POST['validate_job'], $_POST['zipcode_api_module'], $_POST['zipcode_api_key'], $_POST['id_min_len'], $_POST['pass_min_len'], $_POST['join_not_id'], $_POST['use_join_email'], $_POST['from_email'], $_POST['from_name'], $_POST['join_email_title'], $_POST['join_email_content'], $_POST['join_agreement'], $_POST['join_private'], $_POST['use_ssl'], $_POST['ssl_domain'], $_POST['ssl_port'], $_POST['join_redirect'] ,$logoval));
			}else{
				if(!empty($_POST['mail_logo'])){ 
					$logoval = $_POST['mail_logo'];
				}else{ 
					$logoval = "";
				}
				if($_POST['mail_logo_del'] == 1){
					$logoval = "";
				}

				$wpdb->query($wpdb->prepare("insert into NFB_setup (skinname, table_width, table_align, use_name, validate_name, use_addr, validate_addr, use_zipcode_api, use_birth, validate_birth, use_phone, validate_phone, use_hp, validate_hp, use_sex, validate_sex, use_job, validate_job, zipcode_api_module, zipcode_api_key, id_min_len, pass_min_len, join_not_id, use_join_email, from_email, from_name, join_email_title, join_email_content, join_agreement, join_private,use_ssl,ssl_domain,ssl_port,mail_logo) values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s,%s,%s,%s)",$_POST['skinname'], $_POST['table_width'], $_POST['table_align'], $_POST['use_name'], $_POST['validate_name'], $_POST['use_addr'], $_POST['validate_addr'], $_POST['use_zipcode_api'], $_POST['use_birth'], $_POST['validate_birth'], $_POST['use_phone'], $_POST['validate_phone'], $_POST['use_hp'], $_POST['validate_hp'], $_POST['use_sex'], $_POST['validate_sex'], $_POST['use_job'], $_POST['validate_job'], $_POST['zipcode_api_module'], $_POST['zipcode_api_key'], $_POST['id_min_len'], $_POST['pass_min_len'], $_POST['join_not_id'], $_POST['use_join_email'], $_POST['from_email'], $_POST['from_name'], $_POST['join_email_title'], $_POST['join_email_content'], $_POST['join_agreement'], $_POST['join_private'], $_POST['use_ssl'], $_POST['ssl_domain'], $_POST['ssl_port']."'".$logoval));
			}

			update_option("NFB_login_page", $_POST['login_page']);
			update_option("NFB_join_page", $_POST['join_page']);
			update_option("NFB_id_find_page", $_POST['id_find_page']);
			update_option("NFB_pw_find_page", $_POST['pw_find_page']);
			update_option("NFB_leave_page", $_POST['leave_page']);
			update_option("NFB_skin",$_POST['skinname']);
			
			echo "
				<form method='post' name='action_frm' action='".NFB_SETUP."'>
				<input type='hidden' name='save_check' value='1' />
				</form>
				<script type='text/javascript'>document.action_frm.submit();</script>";
			exit;
		}

		if(!empty($_POST['save_check']) && $_POST['save_check'] == "1") $edit_mode = "edit";
		require_once(NFB_ABS.'setup/NFBoardSetup.php');
	}
}

if(!function_exists('NFMemberList')){
	function NFMemberList(){
		global $wpdb;
	
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
			
		if(empty($_GET['uno'])){
			
			if((!empty($_POST['check']) && count($_POST['check'])) > 0 && ((!empty($_POST['tBatch1']) && $_POST['tBatch1'] == 'remove') or (!empty($_POST['tBatch2']) && $_POST['tBatch2'] == 'remove'))){
				for($i = 0; $i < count($_POST['check']); $i++){
					$cno = $_POST['check'][$i];

					if($cno){
						$rows1 = $wpdb->get_row($wpdb->prepare("select * from NFB_member where uno=%s", $cno), ARRAY_A);
						$rows2 = $wpdb->get_row($wpdb->prepare("select * from ".$wpdb->users." where user_login=%s", $rows1['user_id']), ARRAY_A);
						$wpdb->query($wpdb->prepare("delete from NFB_member where uno=%s", $cno));
						wp_delete_user($rows2['ID']);
					}
				}
			
			}else{
				if(!empty($_POST['delNo'])){
					$cno = trim($_POST['delNo']);
					if($cno){
						$rows1 = $wpdb->get_row($wpdb->prepare("select * from NFB_member where uno=%s", $cno), ARRAY_A);
						$rows2 = $wpdb->get_row($wpdb->prepare("select * from ".$wpdb->users." where user_login=%s", $rows1['user_id']), ARRAY_A);
						$wpdb->query($wpdb->prepare("delete from NFB_member where uno=%s", $cno));
						wp_delete_user($rows2['ID']);
					}
				}
			}
			
			$NFB_Member = new NFB_Member();
			if(!empty($_GET['orderby'])) $orderby = $_GET['orderby'];
			else $orderby = "";
			if(!empty($_GET['order'])) $order = $_GET['order'];
			else $order = "";
			if(!empty($_POST['s'])) $s = $_POST['s'];
			else $s = "";
			
			require_once(NFB_ABS.'setup/NFMemberList.php');
		
		}else{

			$NFB_Member = new NFB_Member();
			
			if(!empty($_POST['mode']) && $_POST['mode'] == "edit"){
				if(!empty($_POST['birth_year']) && !ctype_digit($_POST['birth_year'])){echo "<script type='text/javascript'>alert('생년월일(년)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['birth_month']) && !ctype_digit($_POST['birth_month'])){echo "<script type='text/javascript'>alert('생년월일(월)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['birth_day']) && !ctype_digit($_POST['birth_day'])){echo "<script type='text/javascript'>alert('생년월일(일)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['phone_1']) && !ctype_digit($_POST['phone_1'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['phone_2']) && !ctype_digit($_POST['phone_2'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['phone_3']) && !ctype_digit($_POST['phone_3'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['hp_1']) && !ctype_digit($_POST['hp_1'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['hp_2']) && !ctype_digit($_POST['hp_2'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!empty($_POST['hp_3']) && !ctype_digit($_POST['hp_3'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}

				$edit_mode = "edit";
				$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where uno=%s", $_GET['uno']), ARRAY_A);
			
				if(is_array($rows)){
					/* NFB_member 수정 */
					$fields1 = array();
					$fields2 = array();
					$qry1 = "update NFB_member set ";

					if(!empty($_POST['pass'])){
						if($_POST['pass'] != $_POST['repass']){
							echo "<script type='text/javascript'>alert('비밀번호가 일치하지 않습니다.');history.back();</script>";
							exit;
						}
						$fields1[] = "user_pass=password(%s)";
						$fields2[] = $_POST['pass'];

					}
					if(isset($_POST['name'])) $fields1[] = "name=%s";$fields2[] = $_POST['name'];
					if(strlen($_POST['birth_month']) == 1) $_POST['birth_month'] = "0".$_POST['birth_month'];
					if(strlen($_POST['birth_day']) == 1) $_POST['birth_day'] = "0".$_POST['birth_day'];
					if(isset($_POST['birth_year']) && isset($_POST['birth_month']) && isset($_POST['birth_day'])) $_POST['birth'] = $_POST['birth_year']."-".$_POST['birth_month']."-".$_POST['birth_day'];
					if(isset($_POST['birth'])) $fields1[] = "birth=%s";$fields2[] = $_POST['birth'];
					if(isset($_POST['sex'])) $fields1[] = "sex=%s";$fields2[] = $_POST['sex'];
					if(isset($_POST['zipcode'])) $fields1[] = "zipcode=%s";$fields2[] = $_POST['zipcode'];
					if(isset($_POST['addr1'])) $fields1[] = "addr1=%s";$fields2[] = $_POST['addr1'];
					if(isset($_POST['addr2'])) $fields1[] = "addr2=%s";$fields2[] = $_POST['addr2'];
					if(isset($_POST['phone_1']) && isset($_POST['phone_2']) && isset($_POST['phone_3'])) $_POST['phone'] = $_POST['phone_1']."-".$_POST['phone_2']."-".$_POST['phone_3'];
					if(isset($_POST['phone'])) $fields1[] = "phone=%s";$fields2[] = $_POST['phone'];
					if(isset($_POST['hp_1']) && isset($_POST['hp_2']) && isset($_POST['hp_3'])) $_POST['hp'] = $_POST['hp_1']."-".$_POST['hp_2']."-".$_POST['hp_3'];
					if(isset($_POST['hp'])) $fields1[] = "hp=%s";$fields2[] = $_POST['hp'];
					if(isset($_POST['sms_reception'])) $fields1[] = "sms_reception=%s";$fields2[] = $_POST['sms_reception'];
					if(isset($_POST['email'])) $fields1[] = "email=%s";$fields2[] = $_POST['email'];
					if(isset($_POST['job'])) $fields1[] = "job=%s";$fields2[] = $_POST['job'];

					$edit_fields1 = implode(", ", $fields1);

					$qry1 .= $edit_fields1." where uno=%s";
					$fields2[] = $rows['uno'];
					$wpdb->query($wpdb->prepare($qry1, $fields2));

					$wp_uno = $wpdb->get_var($wpdb->prepare("select ID from ".$wpdb->users." where user_login=%s", $rows['user_id']));
					$wp_user['ID'] = $wp_uno;
					$wp_user['user_login'] = $rows['user_id'];
					$wp_user['user_email'] = $_POST['email'];
					if(!empty($_POST['pass'])) $wp_user['user_pass'] = $_POST['pass'];
					wp_update_user($wp_user);
				}

			}else $edit_mode = "";
			
			$config = $wpdb->get_row("select * from NFB_setup", ARRAY_A);
			$result = $NFB_Member->getView($_GET['uno']);
			
			$EDIT_USER_URL = NFB_MEMBER_LIST."&uno=".$_GET['uno'];
			
			require_once(NFB_ABS.'setup/NFMemberView.php');
		}
	}
}

if(!function_exists('build_param')){
	function build_param($b_name, $mode, $no="", $page="", $keyfield="", $keyword="", $search_chk="", $category="", $ref="", $cno=""){
		$get_vars = array();
		$get_vars['bname'] = $b_name;
		$get_vars['mode'] = $mode;
		if($no) $get_vars['no'] = $no;
		if($page) $get_vars['page'] = $page;
		if($keyfield) $get_vars['keyfield'] = $keyfield;
		if($keyword) $get_vars['keyword'] = $keyword;
		if($search_chk) $get_vars['search_chk'] = $search_chk;
		if($category) $get_vars['cate'] = $category;
		if($ref) $get_vars['ref'] = $ref;
		if($cno) $get_vars['cno'] = $cno;
		return http_build_query($get_vars);
	}
}

if(!function_exists('current_user_level')){
	function current_user_level(){
		if(current_user_can('level_5')){
			return "administrator";
		
		}else if(current_user_can('level_0')){
			return "author";
		}
		else return "all";
	}
}

if(!function_exists("get_custom_link_url")){
	function get_custom_link_url($cData){
		unset($customOut);
		if($cData){
			preg_match_all("/<a[^>]*href=[\"']?([^>\"']+)[\"']?[^>]*>/i", $cData, $customOut, PREG_PATTERN_ORDER);
		}
		return $customOut[1][0];
	}
}

if(!function_exists("get_custom_list")){
	function get_custom_list($tType){
		switch($tType){
			case "category" :
				$args = array(
					'show_option_all' => '',
					'orderby' => 'name',
					'order' => 'ASC',
					'style' => 'none',
					'hide_empty' => 0,
					'use_desc_for_title' => 1,
					'hierarchical' => 1,
					'title_li' => __( 'Categories' ),
					'show_option_none' => __('No categories'),
					'number' => null,
					'echo' => 0,  // 화면출력:1
					'taxonomy' => 'category',
					'walker' => null
				);

				$get_wp_cate = wp_list_categories($args);
				$list_cate = explode("\n", $get_wp_cate);

				$cate_data = Array();
				$cate_cnt = '0';
				for($s = 0; $s < sizeof($list_cate); $s++){
					$getCustomLink = "";
					if(strip_tags($list_cate[$s])){
						$getCustomLink = get_custom_link_url($list_cate[$s]);
						if($getCustomLink){
							$tmp_cateId = explode("cat=", $getCustomLink);
							$cate_data[$cate_cnt]['id'] = $tmp_cateId['1'];
							$cate_data[$cate_cnt]['link'] = $getCustomLink;
							$cate_data[$cate_cnt]['name'] = strip_tags($list_cate[$s]);

							if(!$cate_data[$cate_cnt]['id'] && $tmpCategory){
								$tmpCategory = get_category_by_url($cate_data[$cate_cnt]['link']);
								$cate_data[$cate_cnt]['id'] = $tmpCategory;
							}

							$cate_cnt++;
						}
					}
				}
				return $cate_data;
				break;
			case "page" :
				$args = array(
					'authors' => '',
					'child_of' => 0,
					'date_format' => get_option('date_format'),
					'depth' => 0,
					'echo' => 0,
					'post_type' => 'page',
					'post_status' => 'publish',
					'sort_column' => 'menu_order, post_title',
					'title_li' => '', 
					'walker' => ''
				);

				$get_wp_page = wp_list_pages($args);
				$list_page = explode("\n", $get_wp_page);

				$page_data = Array();
				$page_cnt = '0';

				for($s = 0; $s < sizeof($list_page); $s++){
					$getCustomLink = "";
					if(strip_tags($list_page[$s])){
						$getCustomLink = get_custom_link_url($list_page[$s]);
						if($getCustomLink){
							$tmp_pageId = explode("page_id=", $getCustomLink);
							$page_data[$page_cnt]['id'] = $tmp_pageId['1'];
							$page_data[$page_cnt]['link'] = $getCustomLink;
							$page_data[$page_cnt]['name'] = strip_tags($list_page[$s]);
							if(!$page_data[$page_cnt]['id']) $page_data[$page_cnt]['id'] = url_to_postid($page_data[$page_cnt]['link']);
							$page_cnt++;
						}
					}
				}
				return $page_data;
				break;
			default : 
				break;
		}
	}
}
if(!function_exists("NFB_PageNavi")){
	function NFB_PageNavi($paged, $total_pages, $add_args=false){
		$paging_css = "
			<style>
			.pagination{
				clear:both;
				padding:20px 0;
				position:relative;
				font-size:11px;
				line-height:13px;
			}
			.pagination span, .pagination a{
				display:block;
				float:left;
				margin: 2px 2px 2px 0;
				padding:6px 9px 5px 9px;
				text-decoration:none;
				width:auto;
				color:#fff !important;
				background: #6d6d6d !important;
			}
			.pagination a:hover{
				color:#fff !important;
				background: #3279bb !important;
			}
			.pagination .current{
				padding:6px 9px 5px 9px;
				background: #3279bb !important;
				color:#fff !important;
			}
			</style>";
		
		$paging = paginate_links(array(
			'base' => '%_%',
			'format' => '?paged=%#%',
			'current' => max( 1, $paged ),
			'total' => $total_pages,
			'mid_size' => 20,
			'add_args' => $add_args
		));

		return $paging_css."<div class=\"pagination\">".$paging."</div>";
	}
}

// 문자열 자르기
if(!function_exists('cut_text')){
	function cut_text($text, $text_count, $more_text="…") {
		$length = strlen($text);
		if($length <= $text_count) return $text;
		else return mb_substr($text, 0, $text_count, "UTF-8").$more_text;
	}
}
?>