<?php
if(!function_exists('NFB_INIT')){
	function NFB_INIT(){
		global $NFB_SID;
		if(!session_id()){
			session_start();
			$NFB_SID = session_id();
		}
		if(!is_admin()) $initLoad = new NFB_LOAD;
	}
}
if(!function_exists('NFB_Meta_Basic')){
	function NFB_Meta_Basic(){
		echo "<meta name='viewport' content='width=device-width' />\n";
	}
}
if(!function_exists('NFB_UserHead')){
	function NFB_UserHead(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_register_script('NFB-Board-JS', NFB_WEB.'inc/js/common.js');
		wp_enqueue_script('NFB-Board-JS');
	}
}
if(!function_exists('NFB_AdminHead')){
	function NFB_AdminHead(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_register_script('NFB-Board-JS', NFB_WEB.'inc/js/common.js');
		wp_enqueue_script('NFB-Board-JS');
	}
}
if(!function_exists('NFB_AdminMenu')){
	function NFB_AdminMenu(){
		add_menu_page('NF BOARD', 'NF BOARD', 'administrator', 'NFBoard', 'NFBoardSetup');
		add_submenu_page('NFBoard', '환경설정', '환경설정', 'administrator', 'NFBoard', 'NFBoardSetup');
		add_submenu_page('NFBoard', '보드목록', '보드목록', 'administrator', 'NFBoardList', 'NFBoardList');
		add_submenu_page('NFBoard', '보드추가', '보드추가', 'administrator', 'NFBoardAdd', 'NFBoardAdd');
		add_submenu_page('NFBoard', '회원목록', '회원목록', 'administrator', 'NFMemberList', 'NFMemberList');
		remove_submenu_page('users.php', 'user-new.php');
		remove_submenu_page('users.php', 'users.php');
	}
}
if(!function_exists('NFB_Activation_func')){
	function NFB_Activation_func(){
		global $wpdb;
		$boardSQL = "
			create table if not exists NFB_board (
				b_no int(10) unsigned not null auto_increment,
				b_name varchar(20) not null,
				b_skin varchar(20) default null,
				b_type char(1) not null default '1',
				b_editor char(1) not null default 'N',
				b_width varchar(5) not null default '100',
				b_align char(1) not null default 'L',
				b_psize varchar(5) not null default '20',
				b_category text,
				b_filter_use char(1) default '0',
				b_filter_list text,
				b_pds_use char(1) default '0',
				b_agree_use char(1) default '0',
				b_filesize varchar(10) default null,
				b_comment_use char(1) default '0',
				b_secret_use char(1) default '0',
				b_notice_use char(1) default '0',
				b_spam enum('GD','NO') default 'NO',
				b_read_lv varchar(20) not null default 'all',
				b_comment_lv varchar(20) not null default 'all',
				b_write_lv varchar(20) not null default 'all',
				b_seo_use char(1) not null default '0',
				b_seo_title varchar(255) default null,
				b_seo_desc varchar(255) default null,
				b_seo_keywords varchar(255) default null,
				b_hit_hide char(1) default '0',
				b_writer_hide char(1) default '0',
				b_facebook_use char(1) default '0',
				b_twitter_use char(1) default '0',
				b_hms_use char(1) default '0',
				b_list_count int(11) default null,
				b_regdate int(11) default null,
				b_latest_page varchar(20) default null,
				primary key  (b_no)) default charset=utf8";
		$wpdb->query($boardSQL);

		$memberSQL = "
			create table if not exists NFB_member (
				uno int(10) unsigned not null auto_increment,
				user_id varchar(20) not null,
				user_pass varchar(70) not null,
				name varchar(30) default null,
				birth varchar(10) default null,
				sex char(1) not null default '1',
				zipcode varchar(10) default null,
				addr1 varchar(80) default null,
				addr2 varchar(50) default null,
				email varchar(50) default null,
				phone varchar(20) default null,
				hp varchar(20) default null,
				job varchar(20) default null,
				sms_reception char(1) not null default '0',
				reg_date int(11) default null,
				primary key  (uno)) default charset=utf8";
		$wpdb->query($memberSQL);

		$setupSQL = "
			create table if not exists NFB_setup (
				skinname varchar(20) default null,
				table_width varchar(5) not null default '100',
				table_align char(1) not null default 'L',
				use_name char(1) not null default '1',
				validate_name char(1) not null default '0',
				use_addr char(1) not null default '1',
				validate_addr char(1) not null default '0',
				use_birth char(1) not null default '0',
				validate_birth char(1) not null default '0',
				use_phone char(1) not null default '0',
				validate_phone char(1) not null default '0',
				use_hp char(1) not null default '0',
				validate_hp char(1) not null default '0',
				use_sex char(1) not null default '0',
				validate_sex char(1) not null default '0',
				use_job char(1) not null default '0',
				validate_job char(1) not null default '0',
				use_zipcode_api char(1) not null default '0',
				zipcode_api_module char(1) not null default '0',
				zipcode_api_key varchar(100) default null,
				id_min_len int(11) not null default '5',
				pass_min_len int(11) not null default '5',
				join_not_id text default null,
				join_redirect varchar(255) default null,
				use_join_email char(1) not null default '0',
				from_email varchar(50) default null,
				from_name varchar(30) default null,
				join_email_title varchar(100) default null,
				join_email_content text default null,
				mail_logo varchar(100) default null,
				join_agreement text default null,
				join_private text default null,
				use_ssl char(1) not null default '0',
				ssl_domain varchar(100) default null,
				ssl_port varchar(10) default null
				) default charset=utf8";
		$wpdb->query($setupSQL);
			
		$cnt = $wpdb->get_var("select count(*) from NFB_setup");
		if(!$cnt){
			update_option("NFB_skin","Default");
			$wpdb->query("insert into NFB_setup (skinname, table_width, table_align, use_name, use_addr, id_min_len, pass_min_len) values ('Default', '100', 'L', '1', '1', '5', '5')");
		}

		if(!is_dir(NFB_UPLOAD_PATH."NFBoard/")){
			mkdir(NFB_UPLOAD_PATH."NFBoard/", 0707);
		}
		if(is_dir(NFB_UPLOAD_PATH."NFBoard/")){
			chmod(NFB_UPLOAD_PATH."NFBoard/", 0707);
		}

		//if(get_option("NFB_Default_page_on")!="y") NFB_Default_page_create();

	}
}
if(!function_exists('NFB_Deactivation_func')){
	function NFB_Deactivation_func(){
	}
}
if(!function_exists('NFB_deactivation_func')){
	function NFB_Default_page_create($pcode="")
	{
		if(!$pcode) return;

		$default_pages = array();
		if($pcode == 0) {
			$default_pages[] = array(
				'title' => '로그인',
				'content' => '[NFB_LOGIN]'
			);
		}else if($pcode == 1) {
			$default_pages[] = array(
				'title' => '회원가입',
				'content' => '[NFB_JOIN]'
			);
		}else if($pcode == 2) {
			$default_pages[] = array(
				'title' => '아이디찾기',
				'content' => ' [NFB_ID_FIND] '
			);
		}else if($pcode == 3) {
			$default_pages[] = array(
				'title' => '비밀번호찾기',
				'content' => '[NFB_PW_FIND]'
			);
		}else if($pcode == 4) {
			$default_pages[] = array(
				'title' => '회원탈퇴',
				'content' => '[NFB_LEAVE]'
			);
		}
		$existing_pages = get_pages();
		$existing_titles = array();

		foreach ($existing_pages as $page) 
		{
			$existing_titles[] = $page->post_title;
		}

		foreach ($default_pages as $new_page) 
		{
			if( !in_array( $new_page['title'], $existing_titles ) )
			{
				// create post object
				$add_default_pages = array(
					'post_title' => $new_page['title'],
					'post_content' => $new_page['content'],
					'post_status' => 'publish',
					'post_type' => 'page'
				  );

				// insert the post into the database
				$result = wp_insert_post($add_default_pages);   
			}
		}
		update_option("NFB_default_page_on","y");
	}
}
if(!function_exists('NFB_Plugin_delete')){
	function NFB_Plugin_delete(){
		global $wpdb;

		$wpdb->query("drop table NFB_board");
		$wpdb->query("drop table NFB_member");
		$wpdb->query("drop table NFB_setup");

		$boardList = $wpdb->get_results("show tables where Tables_in_".DB_NAME." like 'NFB_%_board' and Tables_in_".DB_NAME."<>'NFB_board'", ARRAY_N);
		foreach($boardList as $board){
			$wpdb->query("drop table ".$board[0]."");
		}
		$commList = $wpdb->get_results("show tables where Tables_in_".DB_NAME." like 'NFB_%_comment'", ARRAY_N);
		foreach($commList as $comm){
			$wpdb->query("drop table ".$comm[0]."");
		}

		if(is_dir(NFB_UPLOAD_PATH."NFBoard/")){
			$cmd = "rm -rf ".NFB_UPLOAD_PATH."NFBoard/";
			exec($cmd);
		}
		// Option delete
		delete_option("NFB_default_page_on");
		delete_option("NFB_login_page");
		delete_option("NFB_join_page");
		delete_option("NFB_id_find_page");
		delete_option("NFB_pw_find_page");
		delete_option("NFB_leave_page");
		delete_option("NFB_skin");
	}
}
if(!function_exists('NFB_MakeBoardCode')){
	function NFB_MakeBoardCode($args){
		global $wpdb;

		if(empty($args['bname'])) return '';
		if(!empty($_GET['bname']) && $args['bname'] != $_GET['bname']) return '잘못된 접근입니다.';

		$bCnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_board where b_name=%s", $args['bname']));
		
		if($bCnt > 0){
			ob_start();
			$NFB_Class = new NFB_Board;
			if(empty($_GET['mode']) || $_GET['mode'] == 'list'){
				wp_enqueue_script( 'ajax-script', plugins_url('/',__FILE__). 'templates/board/'.get_option("NFB_skin").'/js/tf_list.js', array('jquery'), 1.0 );
				wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
				$NFB_Class->LoadList($args['bname']);
			}else if(!empty($_GET['mode']) && $_GET['mode'] == 'write'){
				wp_enqueue_script( 'ajax-script', plugins_url('/',__FILE__). 'templates/board/'.get_option("NFB_skin").'/js/tf_write.js', array('jquery'), 1.0 );
				wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
				$NFB_Class->LoadWrite($args['bname']);
			}else if((!empty($_GET['mode']) && $_GET['mode'] == 'view') && (!empty($_GET['no']) && $_GET['no'] > 0)){
				wp_enqueue_script( 'ajax-script', plugins_url('/',__FILE__). 'templates/board/'.get_option("NFB_skin").'/js/tf_view.js', array('jquery'), 1.0 );
				wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
				$NFB_Class->LoadView($args['bname']);
			}
			$NFB_Class->copyright();
			$contentLoad = ob_get_contents();
			ob_end_clean();
			return $contentLoad;
		}else{
			return $args['bname']." 게시판은 존재하지 않는 게시판입니다.";
		}
	}
}

if(!function_exists('NFB_BoardShortcode')){
	function NFB_BoardShortcode($posts){
		$shortcode = 'NFB_Board';
		if(empty($posts)) return $posts;
		foreach($posts as $post){
			if(stripos($post->post_content, '['.$shortcode) !== false){
				add_shortcode($shortcode, 'NFB_MakeBoardCode');
				break;
			}
		}
		return $posts;
	}
}
if(!function_exists('NFB_MakeLatestCode')){
	function NFB_MakeLatestCode($args){
		global $wpdb;

		if(empty($args['bname'])) return '';
		if(!empty($_GET['bname']) && $args['bname'] != $_GET['bname']) return '잘못된 접근입니다.';

		$bCnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_board where b_name=%s", $args['bname']));
		if($bCnt > 0){
			ob_start();
			$NFB_Class = new NFB_Board;
			$NFB_Class->LoadLatest($args['bname']);
			$contentLoad = ob_get_contents();
			ob_end_clean();
			return $contentLoad;
		}else{
			return $args['bname']." 게시판은 존재하지 않는 게시판입니다.";
		}
	}
}
if(!function_exists('NFB_LatestShortcode')){
	function NFB_LatestShortcode($posts){
		$shortcode = 'NFB_Latest';
		if(empty($posts)) return $posts;
		foreach($posts as $post){
			if(stripos($post->post_content, '['.$shortcode) !== false){
				add_shortcode($shortcode, 'NFB_MakeLatestCode');
				break;
			}
		}
		return $posts;
	}
}
if(!function_exists('NFB_JoinShortcode')){
	function NFB_JoinShortcode($atts, $content=null){
		global $wpdb;
		wp_enqueue_script( 'ajax-script-join', plugins_url('/',__FILE__). 'templates/member/'.get_option("NFB_skin").'/js/tf_join.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script-join', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		ob_start();
		$current_user = wp_get_current_user();	
		$NFB_Class = new NFB_Member;
		$NFB_Class->LoadJoin();
		$contentLoad = ob_get_contents();
		ob_end_clean();
		return $contentLoad;
	}
}
if(!function_exists('NFB_LoginShortcode')){
	function NFB_LoginShortcode($atts, $content=null){
		global $wpdb;

		wp_enqueue_script( 'ajax-script-login', plugins_url('/',__FILE__). 'templates/member/'.get_option("NFB_skin").'/js/tf_login.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script-login', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		ob_start();
		$current_user = wp_get_current_user();	
		$NFB_Class = new NFB_Member;
		$NFB_Class->LoadLogin();
		$contentLoad = ob_get_contents();
		ob_end_clean();
		return $contentLoad;
	}
}
if(!function_exists('NFB_IDFindShortcode')){
	function NFB_IDFindShortcode($atts, $content=null){
		global $wpdb;
		wp_enqueue_script( 'ajax-script-id-find', plugins_url('/',__FILE__). 'templates/member/'.get_option("NFB_skin").'/js/tf_id_find.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script-id-find', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		ob_start();
		$current_user = wp_get_current_user();	
		$NFB_Class = new NFB_Member;
		$NFB_Class->LoadIDFind();
		$contentLoad = ob_get_contents();
		ob_end_clean();
		return $contentLoad;
	}
}
if(!function_exists('NFB_PWFindShortcode')){
	function NFB_PWFindShortcode($atts, $content=null){
		global $wpdb;
		wp_enqueue_script( 'ajax-script-pw-find', plugins_url('/',__FILE__). 'templates/member/'.get_option("NFB_skin").'/js/tf_pw_find.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script-pw-find', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		ob_start();
		$current_user = wp_get_current_user();	
		$NFB_Class = new NFB_Member;
		$NFB_Class->LoadPWFind();
		$contentLoad = ob_get_contents();
		ob_end_clean();
		return $contentLoad;
	}
}
if(!function_exists('NFB_LeaveShortcode')){
	function NFB_LeaveShortcode($atts, $content=null){
		global $wpdb;
		wp_enqueue_script( 'ajax-script-leave', plugins_url('/',__FILE__). 'templates/member/'.get_option("NFB_skin").'/js/tf_leave.js', array('jquery'), 1.0 );
		wp_localize_script( 'ajax-script-leave', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		ob_start();
		$current_user = wp_get_current_user();	
		$NFB_Class = new NFB_Member;
		$NFB_Class->LoadLeave();
		$contentLoad = ob_get_contents();
		ob_end_clean();
		return $contentLoad;
	}
}

if(!function_exists('nfboard_add_query_string')) {
	function nfboard_add_query_string($qs) {
		$qs[] = 'NFPage';
		return $qs;
	}
	add_filter('query_vars', 'nfboard_add_query_string');
}

if(!function_exists('get_custom_template')){
	function get_custom_template($template) {
		if($_GET['NFPage'] == 'zipcode') {
			return dirname( __FILE__ ) . '/templates/member/'.get_option("NFB_skin").'/zipcode.php';
		}else if($_GET['NFPage'] == 'board-move') {
			return dirname( __FILE__ ) . '/templates/board/'.get_option("NFB_skin").'/tf_move.php';
		}else if($_GET['NFPage'] == 'board-popup') {
			return dirname( __FILE__ ) . '/templates/board/'.get_option("NFB_skin").'/tf_popup.php';
		}else if($_GET['NFPage'] == 'board-delete') {
			return dirname( __FILE__ ) . '/templates/board/'.get_option("NFB_skin").'/tf_delete.php';
		}else if($_GET['NFPage'] == 'board-pass-check') {
			return dirname( __FILE__ ) . '/templates/board/'.get_option("NFB_skin").'/tf_pw_check.php';
		}else if($_GET['NFPage'] == 'board-search-process') {
			return dirname( __FILE__ ) . '/run/board/NFSearch-process.php';
		}else if($_GET['NFPage'] == 'board-write-process') {
			return dirname( __FILE__ ) . '/run/board/NFWrite-process.php';
		}else if($_GET['NFPage'] == 'board-delete-process') {
			return dirname( __FILE__ ) . '/run/board/NFDelete-process.php';
		}else if($_GET['NFPage'] == 'board-delete-view-process') {
			return dirname( __FILE__ ) . '/run/board/NFDeleteView-process.php';
		}else if($_GET['NFPage'] == 'board-move-process') {
			return dirname( __FILE__ ) . '/run/board/NFMove-process.php';
		}else if($_GET['NFPage'] == 'board-pass-check-process') {
			return dirname( __FILE__ ) . '/run/board/NFPassCheck-process.php';
		}else if($_GET['NFPage'] == 'board-download') {
			return dirname( __FILE__ ) . '/run/board/NFDownload.php';
		}
		return $template;
	}
}
add_filter( "template_include", "get_custom_template" ) ;


if(!function_exists("json_encode")){ 
    function json_encode($a=false){ 
        if(is_null($a)) return 'null'; 
        if($a === false) return 'false'; 
        if($a === true) return 'true'; 
        if(is_scalar($a)){ 
            if(is_float($a)) return floatval(str_replace(",", ".", strval($a))); 
            if(is_string($a)){ 
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')); 
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"'; 
            } else return $a; 
        } 
        $isList = true; 
        for($i=0, reset($a); $i<count($a); $i++, next($a)){ 
            if(key($a) !== $i){ 
                $isList = false; 
                break; 
            } 
        } 
        $result = array(); 
        if($isList){ 
            foreach($a as $v) $result[] = json_encode($v); 
            return '[' . join(',', $result) . ']'; 
        } else{ 
            foreach($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v); 
            return '{' . join(',', $result) . '}'; 
        } 
    } 
}
if(!function_exists("check_email")){ 
	function check_email($str){
		$email_match = "/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i";
		if(preg_match($email_match, $str)){
			return false;
		}else{
			return true;
		}
	}
}
?>
