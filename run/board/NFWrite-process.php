<?php
$NFB_SID = $_POST['sess_id'];
@session_start();
//@session_id($NFB_SID);
header("Content-Type: text/html; charset=UTF-8");
header("Access-Control-Allow-Origin:".str_replace("https://", "http://", NFB_SITE_URL)."");
header("Access-Control-Allow-Credentials: true");
header("X-Content-Type-Options:nosniff");
header("X-XSS-Protection:1; mode=block");

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

$json_data = array();
$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if(empty($_POST['bname'])){echo "empty bname";exit;}

$config = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $_POST['bname']));

if(empty($_POST['title'])){echo "empty title";exit;}
if(empty($_POST['writer'])){echo "empty writer";exit;}
if(mb_strlen($_POST['writer']) > 16){echo "long writer";exit;}
if(!empty($_POST['validate_pass']) && $_POST['validate_pass'] == 1){
	if(empty($_POST['pass'])){echo "empty pass";exit;}
	if(mb_strlen($_POST['pass']) > 16){echo "long pass";exit;}
}
if(empty($_POST['content'])){echo "empty content";exit;}

if(!empty($config->b_spam) && $config->b_spam != "NO"){
	if(empty($_SESSION['authKey']) || empty($_POST['string']) || $_SESSION['authKey'] != $_POST['string']){echo "auth error";exit;}
}
if((empty($curUserPermision) || $curUserPermision == 'all') && $config->b_agree_use=='1'){
	if(empty($_POST['agree1'])){echo "empty agree1";exit;}
}



if(!empty($curUserPermision) && $curUserPermision != 'all'){
	if(!empty($current_user->ID)) $memnum = $current_user->ID;
	if(!empty($curUserPermision)) $memlevel = $curUserPermision;
	if(!empty($current_user->user_pass)) $pass = $current_user->user_pass;
}else{
	$memnum = "0";
	$memlevel = "";
	if(!empty($_POST['pass'])) $pass = $_POST['pass']; 
}
if(!empty($_POST['writer'])) $writer = $_POST['writer'];
else $writer = "";
if(!empty($_POST['category'])) $category = $_POST['category'];
else $category = "";
$unique = time();
$write_date = date("Y-m-d H:i:s", $unique);
if(!empty($_POST['use_notice'])) $use_notice = $_POST['use_notice'];
else $use_notice = '0';
if(!empty($_POST['b_editor']) && $_POST['b_editor'] != "N"){ 
	$use_html = '2';
}else if(!empty($_POST['b_editor']) && $_POST['b_editor'] == "N"){
	if(!empty($_POST['use_html'])) $use_html = $_POST['use_html'];
	else $use_html = '0';
}
if(!empty($_POST['use_secret'])) $use_secret = $_POST['use_secret'];
else $use_secret = '0';
if(!empty($_POST['content'])) $content = $_POST['content'];
else $content = "";
if(!empty($_FILES['file1']['name'])) $file1 = $_FILES['file1']['name'];
else $file1 = "";
if(!empty($_FILES['file2']['name'])) $file2 = $_FILES['file2']['name'];
else $file2 = "";
if(!empty($_POST['title'])) $title = $_POST['title'];
else $title = "";
$title = str_replace("[Re]", "", $title);

$ip = $_SERVER['REMOTE_ADDR'];

if(!empty($config->b_filter_use) && $config->b_filter_use == "1"){
	$filtered = array();
	if(!empty($config->b_filter_list)){
		$fArr = explode(",", $config->b_filter_list);
		if(count($fArr) > 0){
			foreach($fArr as $filterWord){
				if(trim($filterWord) != ''){
					if(eregi($filterWord, $content)) $filtered[] = $filterWord;
					else if(eregi($filterWord, $title)) $filtered[] = $filterWord;
				}
			}
		}
	}
	if(count($filtered) > 0){echo "filter error";exit;}
}

if(!empty($_POST['b_filesize'])) $b_filesize = $_POST['b_filesize'];
else $b_filesize = 2;

$not_files = array("php", "phpm", "htm", "html", "shtm", "ztx", "dot", "asp", "cgi", "pl", "inc");
if(!empty($_FILES['file1']['tmp_name'])){
	$oFile1 = explode(".", $_FILES['file1']['name']);
	if(in_array($oFile1[1], $not_files)){echo "file type error";exit;}
	else{
		if(!empty($b_filesize)){
			if($_FILES['file1']['size'] > ($b_filesize * 1048576)){echo "file byte error";exit;}
		}
	}
}
if(!empty($_FILES['file2']['tmp_name'])){
	$oFile2 = explode(".", $_FILES['file2']['name']);
	if(in_array($oFile2[1], $not_files)){echo "file type error";exit;}
	else{
		if(!empty($b_filesize)){
			if($_FILES['file2']['size'] > ($b_filesize * 1048576)){echo "file byte error";exit;}
		}
	}
}

//write
if((!empty($_POST['mode']) && $_POST['mode'] == 'write') && empty($_POST['no']) && empty($_POST['ref'])){
	if(!empty($curUserPermision) && $curUserPermision != 'all'){
		$pass1 = "%s";
		$pass2 = $pass;
	}else{
		$pass1 = "password(%s)";
		$pass2 = $pass;
	}

	$inQuery = $wpdb->prepare("insert into NFB_".$_POST['bname']."_board (memnum, memlevel, writer, pass, write_date, title, content, category, use_notice, use_html, use_secret, file1, file2, ip, hit, listnum) values (%s, %s, %s, ".$pass1.", %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 0, 1)", $memnum, $memlevel, $writer, $pass2, $write_date, $title, $content, $category, $use_notice, $use_html, $use_secret, $file1, $file2, $ip);

	$wCnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_".$_POST['bname']."_board where writer=%s and write_date=%s and title=%s", $writer, $write_date, $title));
	if($wCnt <= 0) $wpdb->query($inQuery);  // 중복 저장 방지

	$ref = $wpdb->insert_id;

	$wpdb->query($wpdb->prepare("update NFB_".$_POST['bname']."_board set ref=%s, movecheck=%s where no=%s", $ref, $config->b_no."_".$ref."_0", $ref));
	$wpdb->query($wpdb->prepare("update NFB_board set list_count=list_count+1 where b_name=%s", $_POST['bname']));
	
	if($_FILES['file1']['tmp_name']){
		$upload_file1 = $config->b_no."_".$ref."_1.".$oFile1[1];
		$filepath1 = NFB_UPLOAD_PATH."NFBoard/".$upload_file1;
		@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
	}
	if($_FILES['file2']['tmp_name']){
		$upload_file2 = $config->b_no."_".$ref."_2.".$oFile2[1];
		$filepath2 = NFB_UPLOAD_PATH."NFBoard/".$upload_file2;
		@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
	}

	echo "success|||write";exit;

//modify
}else if((!empty($_POST['mode']) && $_POST['mode'] == 'write') && (!empty($_POST['no']) && $_POST['no'] > 0) && empty($_POST['ref'])){  
	$rows = $wpdb->get_row("select * from NFB_".$_POST['bname']."_board where no='".$_POST['no']."'");
	
	if(!empty($rows->write_date)){
		$tmp1 = explode(" ", $rows->write_date);
		$tmp2 = explode("-", $tmp1[0]);
		$tmp3 = explode(":", $tmp1[1]);
		$writetime = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
	}

	if($rows->re_level != "0") $pict = $writetime."_re";
	else $pict = $_POST['no']."_";
	
	$filekind1 = substr($rows->file1, -3);
	$filekind2 = substr($rows->file2, -3);
	
	if($curUserPermision == 'author'){
		$write_pass = $pass;
	}else if($curUserPermision == 'all'){
		$write_pass = $wpdb->get_var($wpdb->prepare("select password(%s)", $pass));
	}

	if($curUserPermision != "administrator" && $rows->pass != $write_pass){echo "password error";exit;}

	$qry = "update NFB_".$_POST['bname']."_board set ";
	$qry .= "title='".$title."', content='".$content."', category='".$category."', use_notice='".$use_notice."', use_html='".$use_html."', use_secret='".$use_secret."'";

	if(!empty($_POST['file_del1']) && $_POST['file_del1'] == "1"){
		$filedel_path1 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."1.".$filekind1;
		@unlink($filedel_path1);
		if(!empty($_FILES['file1']['tmp_name'])){
			$upload_file1 = $config->b_no."_".$pict."1.".$oFile1[1];
			$filepath1 = NFB_UPLOAD_PATH."NFBoard/".$upload_file1;
			@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
			$qry .= ", file1='".$file1."'";

		}else{
			$qry .= ", file1=''";
		}
		
	}else{
		if(!empty($_FILES['file1']['tmp_name'])){
			if(!empty($filekind1)){
				$filedel_path1 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."1.".$filekind1;
				@unlink($filedel_path1);
			}
			$upload_file1 = $config->b_no."_".$pict."1.".$oFile1[1];
			$filepath1 = NFB_UPLOAD_PATH."NFBoard/".$upload_file1;
			@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
			$qry .= ", file1='".$file1."'";
		}
	}

	if(!empty($_POST['file_del2']) && $_POST['file_del2'] == "1"){
		$filedel_path2 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."2.".$filekind2;
		@unlink($filedel_path2);
		if(!empty($_FILES['file2']['tmp_name'])){
			$upload_file2 = $config->b_no."_".$pict."2.".$oFile2[1];
			$filepath2 = NFB_UPLOAD_PATH."NFBoard/".$upload_file2;
			@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
			$qry .= ", file2='".$file2."'";

		}else{
			$qry .= ", file2=''";
		}
		
	}else{
		if(!empty($_FILES['file2']['tmp_name'])){
			if(!empty($filekind2)){
				$filedel_path2 = NFB_UPLOAD_PATH."NFBoard/".$config->b_no."_".$pict."2.".$filekind2;
				@unlink($filedel_path2);
			}
			$upload_file2 = $config->b_no."_".$pict."2.".$oFile2[1];
			$filepath2 = NFB_UPLOAD_PATH."NFBoard/".$upload_file2;
			@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
			$qry .= ", file2='".$file2."'";
		}
	}
	
	if(!empty($writer)) $qry .= ", writer='".$writer."'";
	if($curUserPermision == 'administrator'){
		if(!empty($_POST['pass'])) $qry .= ", pass=password('".trim($_POST['pass'])."')";
	}

	$qry .= " where no=%s";
	$wpdb->query($wpdb->prepare($qry, $_POST['no']));

	echo "success|||modify";exit;


//reply
}else if((!empty($_POST['mode']) && $_POST['mode'] == 'write') && (!empty($_POST['no']) && $_POST['no'] > 0) && (!empty($_POST['ref']) && $_POST['ref'] > 0)){
	if(empty($_POST['re_step'])) $_POST['re_step'] = 0;
	if(empty($_POST['re_level'])) $_POST['re_level'] = 0; 
	$wpdb->query($wpdb->prepare("update NFB_".$_POST['bname']."_board set re_step=re_step+1 where ref=%s and re_step>%s", $_POST['ref'], $_POST['re_step']));

	if(!empty($use_secret) && $use_secret == "1"){
		$pass = $wpdb->get_var($wpdb->prepare("select pass from NFB_".$_POST['bname']."_board where no=%s", $_POST['ref']));
		$pass = "'".$pass."'";
	}else{ 
		if(!empty($curUserPermision) && $curUserPermision != 'all'){
			$pass1 = "%s";
			$pass2 = $pass;
		}else{
			$pass1 = "password(%s)";
			$pass2 = $pass;
		}
	}

	$re_step = $_POST['re_step'] + 1;
	$re_level = $_POST['re_level'] + 1;

	$inQuery = $wpdb->prepare("insert into NFB_".$_POST['bname']."_board (memnum, memlevel, writer, pass, write_date, title, content, category, use_notice, use_html, use_secret, file1, file2, ip, hit, ref, re_step, re_level, movecheck) values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 0, %s, %s, %s, %s)", $memnum, $memlevel, $writer, $pass, $write_date, $title, $content, $category, $use_notice, $use_html, $use_secret, $file1, $file2, $ip, $_POST['ref'], $re_step, $re_level, $config->b_no."_".$_POST['ref']."_0");
	$wpdb->query($inQuery);
	$ref = $wpdb->insert_id;
	$wpdb->query($wpdb->prepare("update NFB_board set list_count=list_count+1 where b_name=%s", $_POST['bname']));

	if(!empty($_FILES['file1']['tmp_name'])){
		$upload_file1 = $config->b_no."_".$unique."_re1.".$oFile1[1];
		$filepath1 = NFB_UPLOAD_PATH."NFBoard/".$upload_file1;
		@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
	}
	if(!empty($_FILES['file2']['tmp_name'])){
		$upload_file2 = $config->b_no."_".$unique."_re2.".$oFile2[1];
		$filepath2 = NFB_UPLOAD_PATH."NFBoard/".$upload_file2;
		@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
	}

	echo "success|||reply";exit;

}else{
	echo "nonData";exit;
}
?>