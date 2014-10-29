<?php
/* **********************************************
	Member Login ajax routine
********************************************** */
if(!function_exists('ajax_action_login')){
	function ajax_action_login() {
		global $wpdb;
		parse_str($_POST['formData'], $V);
		if(empty($V['uid'])){echo "empty id";die();}
		if(empty($V['upass'])){echo "empty pass";die();}

		$NFB_user = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_member where user_id=%s", $V['uid']));
		$wp_user = $wpdb->get_var($wpdb->prepare("select count(*) from ".$wpdb->users." where user_login=%s", $V['uid']));

		if($NFB_user > 0 || $wp_user > 0){
			$wp_login = apply_filters('authenticate', null, $V['uid'], $V['upass']);
			if($wp_login->ID > 0){
				$creds = array();
				$creds['user_login'] = $V['uid'];
				$creds['user_password'] = $V['upass'];
				if(isset($V['remember'])) $creds['remember'] = true;
				$user = wp_signon($creds, false);
				if(is_wp_error($user)){echo "login_fail";}
				echo "success";
			}else{
				echo "pass_fail";
			}
		}else{ 
			echo "id_fail";
		}
		die();
	}
}
add_action( 'wp_ajax_login_action', 'ajax_action_login' );
add_action( 'wp_ajax_nopriv_login_action', 'ajax_action_login' );


/* **********************************************
	Member Join ajax routine
********************************************** */
if(!function_exists('ajax_action_join')){
	function ajax_action_join() {
		global $wpdb;
		parse_str($_POST['formData'], $V);

		$reg_date = time();
		if(mb_strlen($V['birth_month']) == 1) $V['birth_month'] = "0".$V['birth_month'];
		if(mb_strlen($V['birth_day']) == 1) $V['birth_day'] = "0".$V['birth_day'];
		if(isset($V['birth_year']) && isset($V['birth_month']) && isset($V['birth_day'])) $V['birth'] = $V['birth_year']."-".$V['birth_month']."-".$V['birth_day'];
		else $V['birth'] = "";
		if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
		else $V['phone'] = "";
		if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
		else $V['hp'] = "";
		if(!isset($V['sms_reception'])) $V['sms_reception'] = 0;

		$config = $wpdb->get_row("select * from NFB_setup", ARRAY_A);

		if(!empty($config['join_not_id'])){
			$not_id_arr = explode(",", $config['join_not_id']);
			for($i = 0; $i < count($not_id_arr); $i++){
				$not_id_arr[$i] = str_replace(" ", "", $not_id_arr[$i]);
			}
		}

		if(!empty($V['email'])){
			$users1 = $wpdb->get_row($wpdb->prepare("select * from NFB_member where email=%s", $V['email']));
			$users2 = $wpdb->get_row($wpdb->prepare("select * from ".$wpdb->users." where user_email=%s", $V['email']));
			
			if($V['mode'] == "edit"){
				$current_user = wp_get_current_user();	
				if($current_user->user_login != $users1->user_id){
					if(!empty($users1->email) || !empty($users2->user_email)){echo "exist email";die();}
				}
			}else{
				if(!empty($users1->email) || !empty($users2->user_email)){echo "exist email";die();}
			}
		}

		if($V['mode'] == "write"){
			$wCnt = $wCnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_member where user_id=%s", $V['user_id']));
			if($wCnt <= 0){
				if(empty($V['user_id'])){echo "empty id";die();}
				if($config['id_min_len'] > 0){if(mb_strlen($V['user_id']) < $config['id_min_len']){echo "short id";die();}}
				if(mb_strlen($V['user_id']) > 16){echo "long id";die();}
				if($V['id_checked'] != "y"){if($V['id_checked'] == "n"){echo "exist id";die();}else{echo "check id";die();}}
				if(!ctype_alnum($V['user_id'])){echo "error id";die();}
				if(isset($not_id_arr)){
					if(in_array($V['user_id'], $not_id_arr)){echo "join not id";die();}
				}
				if(empty($V['user_name']) && ($config['use_name'] == 1 && $config['validate_name'] == 1)){echo "empty name";die();}
				if(empty($V['email'])){echo "empty email";die();}
				if(check_email($V['email']) == true){echo "not form email";die();}
				if(empty($V['pass'])){echo "empty pass";die();}
				if($config['pass_min_len'] > 0){if(mb_strlen($V['pass']) < $config['pass_min_len']){echo "short pass";die();}}
				if(mb_strlen($V['pass']) > 16){echo "long pass";die();}
				if(empty($V['repass'])){echo "empty repass";die();}
				if($V['pass'] != $V['repass']){echo "password mismatch";die();}
				if(empty($V['birth_year']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_year";die();}
				if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_year'] < 1900 || $V['birth_year'] > date("Y"))){echo "incorrect birth_year";die();}
				if(empty($V['birth_month']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_month";die();}
				if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_month'] > 12 || $V['birth_month'] < 1)){echo "incorrect birth_month";die();}
				if(empty($V['birth_day']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_day";die();}
				if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_day'] > 31 || $V['birth_day'] < 1)){echo "incorrect birth_day";die();}
				if(empty($V['sex']) && ($config['use_sex'] == 1 && $config['validate_sex'] == 1)){echo "empty sex";die();}
				if(empty($V['zipcode']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty zipcode";die();}
				if(empty($V['addr1']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty addr1";die();}
				if(empty($V['addr2']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty addr2";die();}
				if(empty($V['phone_1']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_1";die();}
				if(empty($V['phone_2']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_2";die();}
				if(empty($V['phone_3']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_3";die();}
				if(empty($V['hp_1']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_1";die();}
				if(empty($V['hp_2']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_2";die();}
				if(empty($V['hp_3']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_3";die();}
				if(empty($V['job']) && ($config['use_job'] == 1 && $config['validate_job'] == 1)){echo "empty job";die();}
				if($V['agree_check1'] != 1){echo "empty agree_check1";die();}
				if($V['agree_check2'] != 1){echo "empty agree_check2";die();}
					
				$wpdb->query( $wpdb->prepare("insert into NFB_member (user_id, user_pass, name, birth, sex, zipcode, addr1, addr2, email, phone, hp, sms_reception, job, reg_date) values (%s, password(%s), %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, job, %s)", $V['user_id'], $V['pass'], $V['user_name'], $V['birth'], $V['sex'], $V['zipcode'], $V['addr1'], $V['addr2'], $V['email'], $V['phone'], $V['hp'], $V['sms_reception'], $V['job'], time() ));

				$wp_user['user_login'] = $V['user_id'];
				$wp_user['user_pass'] = $V['pass'];
				$wp_user['user_email'] = $V['email'];
				$wp_user_no = wp_create_user($wp_user['user_login'], $wp_user['user_pass'], $wp_user['user_email']);
				
				$blogname = get_option('blogname');
				
				if($config['use_join_email'] == 1){
					if((!empty($config['from_email']) && !empty($config['from_name']) && !empty($V['email'])) && (check_email($config['from_email']) == false && check_email($V['email']) == false)){
						$headers[] = 'From: '.$config['from_name'].' <'.$config['from_email'].'>';
						$email_title = $config['join_email_title'];
						$email_content = "
							<div style='width:650px;border:1px solid #dcdcdc;font-size:14px;font-family:\"굴림\",Tahoma,AppleGothic,sans-serif;color:#333;line-height:1.6em;'>
								<p style='padding:35px 75px;background:#f7f7f7;min-height:40px;margin:0;border-bottom:1px solid #dcdcdc;'>{LOGO}</p>
								<div style='margin:55px 75px;'>
									".nl2br($config['join_email_content'])."
								</div>
								<p style='padding:35px 75px;background:#f7f7f7;min-height:20px;margin:0;color:#777;font-size:12px;'>{SITE_LINK}</p>
							</div>";
						
						if(!empty($config['mail_logo'])){
							$logo_arr = explode(".", $config['mail_logo']);
							$ok_ext = array("jpeg", "jpg", "gif", "png");
							$logo_ext = strtolower($logo_arr[count($logo_arr) - 1]);
							if(in_array($logo_ext, $ok_ext)){
								$email_content = str_replace("{LOGO}", "<img src='".$config['mail_logo']."' border='0' alt='로고' />", $email_content);
							}else{
								$email_content = str_replace("{LOGO}", "&nbsp;", $email_content);
							}
						}else{
							$email_content = str_replace("{LOGO}", "&nbsp;", $email_content);
						}
						
						$email_content = str_replace("{SITE_LINK}", "<a herf='".NFB_SITE_URL."' target='_blank'>".NFB_SITE_URL."</a>", $email_content);
						$email_content = str_replace("{USER_ID}", "<span style='font-weight:bold;'>".$V['user_id']."</span>", $email_content);
						$email_content = str_replace("{USER_NAME}", $V['user_name'], $email_content);
						$email_content = str_replace("{SITE_NAME}", $blogname, $email_content);
						
						if(is_file(NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_email.php")){
							require_once(NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_email.php");
							function set_html_content_type(){
								return 'text/html';
							}
							add_filter('wp_mail_content_type', 'set_html_content_type');
							wp_mail($V['email'], $email_title, $email_body, $headers);
						}
					}			
				}
				
				echo "success|||".$V['user_id']."|||".$V['hp']."|||".$V['sms_reception'];die();
			}else{echo "exist id";die();}

		}else if($V['mode'] == "edit"){
			$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where uno=%d", $V['uno']), ARRAY_A);

			if(empty($rows['user_id'])){echo "nonData";die();}
			if(empty($V['user_name']) && ($config['use_name'] == 1 && $config['validate_name'] == 1)){echo "empty name";die();}
			if(empty($V['email'])){echo "empty email";die();}
			if(check_email($V['email']) == true){echo "not form email";die();}
			if(!empty($V['pass'])){
				if($config['pass_min_len'] > 0){if(mb_strlen($V['pass']) < $config['pass_min_len']){echo "short pass";die();}}
				if(mb_strlen($V['pass']) > 16){echo "long pass";die();}
				if(empty($V['repass'])){echo "empty repass";die();}
				if($V['pass'] != $V['repass']){echo "password mismatch";die();}
			}
			if(empty($V['birth_year']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_year";die();}
			if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_year'] < 1900 || $V['birth_year'] > date("Y"))){echo "incorrect birth_year";die();}
			if(empty($V['birth_month']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_month";die();}
			if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_month'] > 12 || $V['birth_month'] < 1)){echo "incorrect birth_month";die();}
			if(empty($V['birth_day']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo "empty birth_day";die();}
			if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_day'] > 31 || $V['birth_day'] < 1)){echo "incorrect birth_day";die();}
			if(empty($V['sex']) && ($config['use_sex'] == 1 && $config['validate_sex'] == 1)){echo "empty sex";die();}
			if(empty($V['zipcode']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty zipcode";die();}
			if(empty($V['addr1']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty addr1";die();}
			if(empty($V['addr2']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo "empty addr2";die();}
			if(empty($V['phone_1']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_1";die();}
			if(empty($V['phone_2']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_2";die();}
			if(empty($V['phone_3']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo "empty phone_3";die();}
			if(empty($V['hp_1']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_1";die();}
			if(empty($V['hp_2']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_2";die();}
			if(empty($V['hp_3']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo "empty hp_3";die();}
			if(!isset($V['sms_reception']) && $config['use_hp'] == 1){echo "empty sms_reception";die();}
			if(empty($V['job']) && ($config['use_job'] == 1 && $config['validate_job'] == 1)){}
			
			$wpdb->query($wpdb->prepare("update NFB_member set name=%s, birth=%s, sex=%s, zipcode=%s, addr1=%s, addr2=%s, email=%s, phone=%s, hp=%s, sms_reception=%s, job=%s where uno=%d", $V['user_name'], $V['birth'], $V['sex'], $V['zipcode'], $V['addr1'], $V['addr2'], $V['email'], $V['phone'], $V['hp'], $V['sms_reception'], $V['job'], $rows['uno']));

			if(!empty($V['pass'])){
				$wpdb->query($wpdb->prepare("update NFB_member set user_pass=password(%s) where uno=%d", $V['pass'], $V['uno']));
			}

			$wp_user_no = $wpdb->get_var($wpdb->prepare("select ID from ".$wpdb->users." where user_login=%s", $rows['user_id']));
			$wp_user['ID'] = $wp_user_no;
			$wp_user['user_login'] = $rows['user_id'];
			$wp_user['user_email'] = $V['email'];
			if(!empty($V['pass'])) $wp_user['user_pass'] = $V['pass'];
			wp_update_user($wp_user);
			
			echo "success";die();
		}else{
			echo "nonData";die();
		}

	}
}
add_action( 'wp_ajax_join_action', 'ajax_action_join' );
add_action( 'wp_ajax_nopriv_join_action', 'ajax_action_join' );


/* **********************************************
	Member ID Check ajax routine
********************************************** */
if(!function_exists('ajax_action_join_id_check')){
	function ajax_action_join_id_check() {
		global $wpdb;
		$user_id = $_POST['user_id'];

		$config = $wpdb->get_row("select * from NFB_setup limit 1");

		if(!empty($config->join_not_id)){
			$not_id_arr = explode(",", $config->join_not_id);
			for($i = 0; $i < count($not_id_arr); $i++){
				$not_id_arr[$i] = str_replace(" ", "", $not_id_arr[$i]);
			}
		}

		if($user_id){
			if(empty($user_id)){echo "empty id";die();}
			if($config->id_min_len > 0){if(mb_strlen($user_id) < $config->id_min_len){echo "short id";die();}}
			if(mb_strlen($user_id) > 16){echo "long id";die();}
			if(!ctype_alnum($user_id)){echo "error id";die();}
			if(isset($not_id_arr)){
				if(in_array($user_id, $not_id_arr)){echo "join not id";die();}
			}
			$rows1 = $wpdb->get_row($wpdb->prepare("select count(*) from NFB_member where user_id=%s", $user_id), ARRAY_N);
			$rows2 = $wpdb->get_row($wpdb->prepare("select count(*) from ".$wpdb->users." where user_login=%s", $user_id), ARRAY_N);

			if($rows1[0] == 0 && $rows2[0] == 0){
				echo "success|||y|||사용 가능한 아이디입니다";
			}else{
				echo "success|||n|||사용 불가능한 아이디입니다";
			}
			die();

		}else{
			echo "fail";
			die();
		}

	}
}
add_action( 'wp_ajax_id_check_action', 'ajax_action_join_id_check' );
add_action( 'wp_ajax_nopriv_id_check_action', 'ajax_action_join_id_check' );


/* **********************************************
	Member ID Find ajax routine
********************************************** */
if(!function_exists('ajax_action_id_find')){
	function ajax_action_id_find() {
		global $wpdb;
		parse_str($_POST['formData'], $V);

		if(empty($V['email'])){echo "empty email";die();}
		if(check_email($V['email']) == true){echo "not form email";die();}

		$user_id = $wpdb->get_var($wpdb->prepare("select user_id from NFB_member where email=%s", $V['email']));

		if(!empty($user_id)){
			$resultType = "ok";
			ob_start();
			include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_id_find_result.php";
			$fileContent = ob_get_contents();
			ob_end_clean();
			echo "success|||y|||".$fileContent;
			die();
		}else{
			$resultType = "not";
			ob_start();
			include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_id_find_result.php";
			$fileContent = ob_get_contents();
			ob_end_clean();
			echo "success|||n|||".$fileContent;
			die();
		}
	}
}
add_action( 'wp_ajax_id_find_action', 'ajax_action_id_find' );
add_action( 'wp_ajax_nopriv_id_find_action', 'ajax_action_id_find' );


/* **********************************************
	Member Password Find ajax routine
********************************************** */
if(!function_exists('ajax_action_pw_find')){
	function ajax_action_pw_find() {
		global $wpdb;
		parse_str($_POST['formData'], $V);

		if(empty($V['user_id'])){echo "empty id";die();}
		if(empty($V['email'])){echo "empty email";die();}
		if(check_email($V['email']) == true){echo "not form email";die();}

		$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where user_id=%s and email=%s", $V['user_id'], $V['email']));
		$config = $wpdb->get_row("select * from NFB_setup", ARRAY_A);

		if(!empty($rows->uno)){
			$ipwd = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
			$pwd = ""; 
			for($i = 0; $i < 8; $i++){ 
				$pwd .= $ipwd[rand(0, 35)]; 
			}
			
			$wpdb->query($wpdb->prepare("update NFB_member set user_pass=password(%s) where uno=%s", $pwd, $rows->uno));

			$wp_user_no = $wpdb->get_var($wpdb->prepare("select ID from ".$wpdb->users." where user_login=%s", $rows->user_id));
			$wp_user['ID'] = $wp_user_no;
			$wp_user['user_login'] = $rows->user_id;
			$wp_user['user_pass'] = $pwd;
			wp_update_user($wp_user);
			
			$blogname = get_option('blogname');
			if(!empty($config['from_email'])) $admin_email = $config['from_email'];
			else $admin_email = get_option('admin_email');
			
			$headers[] = 'From: '.$blogname.' <'.$admin_email.'>';
			$email_title = "[".$blogname."] 비밀번호 재설정 요청";
			$email_content = "
				<div style='width:650px;border:1px solid #dcdcdc;font-size:14px;font-family:\"굴림\",Tahoma,AppleGothic,sans-serif;color:#333;line-height:1.6em;'>
					<h1 style='display:none;'>비밀번호 재설정 메일</h1>
					<p style='padding:35px 75px;background:#f7f7f7;min-height:40px;margin:0;border-bottom:1px solid #dcdcdc;'>{LOGO_IMG}</p>
					<div style='margin:55px 75px;'>
						<p style='font-size:18px;width:100%;border-bottom:1px solid #dcdcdc;padding-bottom:20px;'><span style='font-weight:bold;'>{USER_ID} 님,</span> <br />비밀번호 재설정을 요청하셨습니다.</p>
						<p style='margin:25px 0;'>비밀번호를 잊으셨나요?<br />아래 임시비밀번호로 로그인 후 비밀번호를 재설정하세요.</p>
						<p>임시비밀번호<br />{IMSI_PW}</p>
					</div>
					<p style='padding:35px 75px;background:#f7f7f7;min-height:20px;margin:0;color:#777;font-size:12px;'>{PAGE_LINK}</p>
				</div>";

			if(!empty($config['mail_logo'])){
				$logo_arr = explode(".", $config['mail_logo']);
				$ok_ext = array("jpeg", "jpg", "gif", "png");
				$logo_ext = strtolower($logo_arr[count($logo_arr) - 1]);
				if(in_array($logo_ext, $ok_ext)){
					$email_content = str_replace("{LOGO_IMG}", "<img src='".$config['mail_logo']."' border='0' alt='로고' />", $email_content);
				}else{
					$email_content = str_replace("{LOGO_IMG}", "", $email_content);
				}
			}else{
				$email_content = str_replace("{LOGO_IMG}", "", $email_content);
			}
			
			$email_content = str_replace("{USER_ID}", $rows->user_id, $email_content);
			$email_content = str_replace("{IMSI_PW}", $pwd, $email_content);
			$email_content = str_replace("{PAGE_LINK}", "<a herf='".NFB_SITE_URL."' target='_blank'>".NFB_SITE_URL."</a>", $email_content);
			
			if(is_file(NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_email.php")){
				require_once(NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_email.php");
				function set_html_content_type(){
					return 'text/html';
				}
				add_filter('wp_mail_content_type', 'set_html_content_type');
				wp_mail($V['email'], $email_title, $email_body, $headers);
			}

			$resultType = "ok";
			ob_start();
			include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_pw_find_result.php";
			$fileContent = ob_get_contents();
			ob_end_clean();
			echo "success|||y|||".$fileContent;
			die();

		}else{

			$resultType = "not";
			ob_start();
			include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_pw_find_result.php";
			$fileContent = ob_get_contents();
			ob_end_clean();
			echo "success|||y|||".$fileContent;
			die();
		}

	}
}
add_action( 'wp_ajax_pw_find_action', 'ajax_action_pw_find' );
add_action( 'wp_ajax_nopriv_pw_find_action', 'ajax_action_pw_find' );


/* **********************************************
	Member Leave ajax routine
********************************************** */
if(!function_exists('ajax_action_leave')){
	function ajax_action_leave() {
		global $wpdb;
		parse_str($_POST['formData'], $V);

		if(empty($V['pass'])){echo "empty pass";die();}
		if(empty($V['repass'])){echo "empty repass";die();}
		if($V['pass'] != $V['repass']){echo "password mismatch";die();}

		$current_user = wp_get_current_user();	
		$user_id = $current_user->user_login;

		if(empty($current_user->ID)){
			echo "not login";die();
		}else{
			$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where user_id=%s", $current_user->user_login));
			if(!empty($rows->uno)){
				
				$pass_check = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_member where user_id=%s and user_pass=password(%s)", $current_user->user_login, $V['pass']));
				
				if($pass_check > 0){
					$wpdb->query($wpdb->prepare("delete from NFB_member where user_id=%s", $current_user->user_login));
					wp_delete_user($current_user->ID);

					$resultType = "ok";
					ob_start();
					include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_leave_result.php";
					$fileContent = ob_get_contents();
					ob_end_clean();
					echo "success|||y|||".$fileContent;
					die();

				}else{

					$resultType = "mismatch";
					ob_start();
					include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_leave_result.php";
					$fileContent = ob_get_contents();
					ob_end_clean();
					echo "success|||n|||".$fileContent;
					die();

				}

			}else{

				$resultType = "not";
				ob_start();
				include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_leave_result.php";
				$fileContent = ob_get_contents();
				ob_end_clean();
				echo "success|||n|||".$fileContent;
				die();

			}
		}

	}
}
add_action( 'wp_ajax_leave_action', 'ajax_action_leave' );
add_action( 'wp_ajax_nopriv_leave_action', 'ajax_action_leave' );


/* **********************************************
	Admin Board ajax routine
********************************************** */
if(!function_exists('ajax_action_admin_board')){
	function ajax_action_admin_board() {
		global $wpdb;

		if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";die();}

		$tMode = $_POST['tMode'];
		$tBoardName = $_POST['tBoardName'];
		if(!empty($_POST['tBoardNo'])) $tBoardNo = $_POST['tBoardNo'];

		if($tMode == 'chkBoardName'){
			if($tBoardName == 'admin'){echo "usedAdmin";die();}

			$blank = explode(" ", $tBoardName);
			if(empty($tBoardName) || count($blank) > 1){echo "usedBlank";die();}
			if(strlen($tBoardName) > 20){echo "over20";die();}
			if(empty($tBoardNo)){
				$boardchk = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_board where b_name=%s", $tBoardName));
				if($boardchk > 0){echo "existName";die();}
				else{echo "success";die();}
			}else{
				$boardchk = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_board where b_no=%d", $tBoardNo));
				if($boardchk <= 0){echo "nonExist";die();}
				else{echo "success";die();}
			}
		}else{echo "nonData";die();}

	}
}
add_action( 'wp_ajax_admin_board_action', 'ajax_action_admin_board' );
add_action( 'wp_ajax_nopriv_admin_board_action', 'ajax_action_admin_board' );


/* **********************************************
	Board comment write ajax routine
********************************************** */
if(!function_exists('ajax_action_board_comment_write')){
	function ajax_action_board_comment_write() {
		global $wpdb;
		parse_str($_POST['formData'], $V);

		$NFB_SID = $V['sess_id'];

		$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출


		$cert_tmp_buff = explode("\n", base64_decode(trim($V['cert'])));
		$cert_time = base64_decode($cert_tmp_buff[0]);
		$stime = time();
		$limitime =  time() - 3600;

		if(empty($V['page'])) $V['page'] = 1;
		if(empty($V['keyfield'])) $V['keyfield'] = "";
		if(empty($V['keyword'])) $V['keyword'] = "";
		if(empty($V['search_chk'])) $V['search_chk'] = "";
		if(empty($V['cate'])) $V['cate'] = "";
		if(empty($V['cno'])) $V['cno'] = "";
		if(empty($V['reply_cname'])) $V['reply_cname'] = "";
		if(empty($V['reply_cpass'])) $V['reply_cpass'] = "";
		if(empty($V['reply_string'])) $V['reply_string'] = "";
		if(empty($V['reply_cmemo'])) $V['reply_cmemo'] = "";

		// 대댓글
		if(!empty($V['cno'])){
			if(empty($V['bname'])){echo "fail|||2";die();}
			if(empty($V['no'])){echo "fail|||2";die();}

			$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $V['bname']));
			$comment = $wpdb->get_row($wpdb->prepare("select * from NFB_".$V['bname']."_comment where no=%d", $V['cno']));

			if(empty($comment->no)){echo "fail|||2";die();}

			if(($stime >= $cert_time && $limitime < $cert_time) && $V['mode'] == "view" && !empty($V['no'])){
				if(($brdSet->b_comment_lv == "author" && $curUserPermision == "all") || ($brdSet->b_comment_lv == "administrator" && $curUserPermision != "administrator")){
					echo "not permission|||2";die();
				}

				if($curUserPermision == "all"){
					if(empty($V['reply_cname'])){echo "empty cname|||2";die();}
					if(mb_strlen($V['reply_cname']) > 16){echo "long cname|||2";die();}
					if(empty($V['reply_cpass'])){echo "empty cpass|||2";die();}
					if(mb_strlen($V['reply_cpass']) > 16){echo "long cpass|||2";die();}
					
					if($brdSet->b_spam != 'NO'){
						if(empty($V['reply_string'])){echo "empty string|||2";die();}
						if(!$_SESSION['authKeySub'] || !$V['reply_string'] || $_SESSION['authKeySub'] != $V['reply_string']){
							echo "fail string|||2";die();
						}
					}
					if(empty($V['reply_cmemo'])){echo "empty cmemo|||2";die();}

					$memnum = 0;
					$writer = $V['reply_cname'];
					$pass1 = "password(%s)";
					$pass2 = $V['reply_cpass'];
				
				}else{
					if($brdSet->b_spam != 'NO'){
						if(empty($V['reply_string'])){echo "empty string|||2";die();}
						if(!$_SESSION['authKeySub'] || !$V['reply_string'] || $_SESSION['authKeySub'] != $V['reply_string']){
							echo "fail string|||2";die();
						}
					}
					if(empty($V['reply_cmemo'])){echo "empty cmemo|||2";die();}

					$memnum = $current_user->ID;
					$writer = $current_user->user_login;
					$pass1 = "%s";
					$pass2 = $current_user->user_pass;
				}
				
				$depth = $comment->depth + 1;

				$wpdb->query($wpdb->prepare("insert into NFB_".$V['bname']."_comment (parent, comm_parent, memnum, writer, pass, content, ip, depth, write_date) values (%s, %s, %s, %s, ".$pass1.", %s, %s, %s, now())", $V['no'], $comment->no, $memnum, $writer, $pass2, $V['reply_cmemo'], $_SERVER['REMOTE_ADDR'], $depth));
				
				echo "success|||2";die();

			}else{
				echo "fail|||2";die();
			}

		// 댓글
		}else{
			if(empty($V['bname'])){echo "fail|||1";die();}
			if(empty($V['no'])){echo "fail|||1";die();}

			$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $V['bname']));
			
			if(($stime >= $cert_time && $limitime < $cert_time) && $V['mode'] == "view" && !empty($V['no'])){
				if(($brdSet->b_comment_lv == "author" && $curUserPermision == "all") || ($brdSet->b_comment_lv == "administrator" && $curUserPermision != "administrator")){
					echo "not permission|||1";die();
				}

				if($curUserPermision == "all"){
					if(empty($V['cname'])){echo "empty cname|||1";die();}
					if(mb_strlen($V['cname']) > 16){echo "long cname|||1";die();}
					if(empty($V['cpass'])){echo "empty cpass|||1";die();}
					if(mb_strlen($V['cpass']) > 16){echo "long cpass|||1";die();}
					
					if($brdSet->b_spam != 'NO'){
						if(empty($V['string'])){echo "empty string|||1";die();}
						if(!$_SESSION['authKey'] || !$V['string'] || $_SESSION['authKey'] != $V['string']){
							echo "fail string|||1";die();
						}
					}
					if(empty($V['cmemo'])){echo "empty cmemo|||1";die();}

					$memnum = 0;
					$writer = $V['cname'];
					$pass1 = "password(%s)";
					$pass2 = $V['cpass'];
				
				}else{
					if($brdSet->b_spam != 'NO'){
						if(empty($V['string'])){echo "empty string|||1";die();}
						if(!$_SESSION['authKey'] || !$V['string'] || $_SESSION['authKey'] != $V['string']){
							echo "fail string|||1";die();
						}
					}
					if(empty($V['cmemo'])){echo "empty cmemo|||1";die();}

					$memnum = $current_user->ID;
					$writer = $current_user->user_login;
					$pass1 = "%s";
					$pass2 = $current_user->user_pass;
				}

				$wpdb->query($wpdb->prepare("insert into NFB_".$V['bname']."_comment (parent, comm_parent, memnum, writer, pass, content, ip, depth, write_date) values (%s, '0', %s, %s, ".$pass1.", %s, %s, '0', now())", $V['no'], $memnum, $writer, $pass2, $V['cmemo'], $_SERVER['REMOTE_ADDR']));

				$comm_parent = $wpdb->insert_id;
				$wpdb->query($wpdb->prepare("update NFB_".$V['bname']."_comment set comm_parent=%s where no=%s", $comm_parent, $comm_parent));
				
				
				echo "success|||1";die();

			}else{
				echo "fail|||1";die();
			}
		}


	}
}
add_action( 'wp_ajax_board_comment_write', 'ajax_action_board_comment_write' );
add_action( 'wp_ajax_nopriv_board_comment_write', 'ajax_action_board_comment_write' );


/* **********************************************
	Board comment delete all ajax routine
********************************************** */
if(!function_exists('ajax_action_board_comment_delete')){
	function ajax_action_board_comment_delete() {
		global $wpdb;

		$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

		if(empty($_POST['cno'])){echo "empty cno";die();}

		if($curUserPermision == "administrator"){
			$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_comment where no=%d", $_POST['cno']));
			echo "success";die();

		}else{
			$pass = $wpdb->get_var($wpdb->prepare("select pass from NFB_".$_POST['bname']."_comment where no=%d", $_POST['cno']));
			
			if(!empty($current_user->ID)){
				$mem_pass = $wpdb->get_var($wpdb->prepare("select user_pass from ".$wpdb->users." where ID=%s", $current_user->ID));
			}else{
				if(empty($_POST['pwd'])){echo "empty password";die();}
				$mem_pass = $wpdb->get_var($wpdb->prepare("select password(%s)", $_POST['pwd']));
			}
			
			if(!empty($pass) && !empty($mem_pass) && $pass == $mem_pass){
				$wpdb->query($wpdb->prepare("delete from NFB_".$_POST['bname']."_comment where no=%s", $_POST['cno']));
				echo "success";die();
			}else{
				echo "password error";die();
			}
		}

	}
}
add_action( 'wp_ajax_board_comment_delete', 'ajax_action_board_comment_delete' );
add_action( 'wp_ajax_nopriv_board_comment_delete', 'ajax_action_board_comment_delete' );




/* **********************************************
	Board list ajax routine
********************************************** */
/*
if(!function_exists('ajax_action_board_list')){
	function ajax_action_board_list() {
		global $wpdb;
		parse_str($_POST['formData'], $V);
	}
}
add_action( 'wp_ajax_leave_action', 'ajax_action_board_list' );
add_action( 'wp_ajax_nopriv_leave_action', 'ajax_action_board_list' );
*/
?>