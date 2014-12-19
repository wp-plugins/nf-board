<?php
class NFB_Board {
	var $row;
	var $totCount;
	var $bName;
	var $total;
	
	public function LoadBoardList($start_pos="", $per_page=""){
		global $wpdb;
		
		if(isset($start_pos) && !empty($per_page)){
			$sql = $wpdb->prepare("select * from NFB_board where b_no<>'' and b_no is not null order by b_no desc limit %d, %d", $start_pos, $per_page);
			$this->row = $wpdb->get_results($sql);
		}else{
			$this->row = $wpdb->get_results("select * from NFB_board where b_no<>'' and b_no is not null order by b_no desc");
		}
		return $this->row;
	}

	public function LoadBoardCount(){
		global $wpdb;
		$this->totCount = $wpdb->get_var("select count(*) from NFB_board where b_no<>'' and b_no is not null order by b_no desc");
		return $this->totCount;
	}

	public function LoadLatest($bname=""){
		global $wpdb, $NFB_SID;
		$detect = new NFB_MobileDetect;
		$curUserPermision = current_user_level();
		$current_user = wp_get_current_user();

		$page_id = get_queried_object_id();
		$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $bname));
		$curUrl = get_permalink($brdSet->b_latest_page);

		$purl = parse_url($curUrl);
		if(!$purl['query']) $link_add = "?";
		else $link_add = "&";

		if(is_dir(NFB_ABS."templates/board/".$brdSet->b_skin)){
			$latest_res = $wpdb->get_results("select * from NFB_".$bname."_board order by no desc limit 5");
			$total = count($latest_res);
			require_once(NFB_ABS."templates/board/".$brdSet->b_skin."/tf_latest.php");
		}else{
			echo "<div class='NFBoard_error_msg'>존재하지 않는 스킨입니다.</div>";
			return "";
		}

		return "";

	}

	public function LoadList($bname=""){
		global $wpdb, $NFB_SID;
		
		$detect = new NFB_MobileDetect;
		$curUserPermision = current_user_level();
		$current_user = wp_get_current_user();

		$page_id = get_queried_object_id();
		$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $bname));
		$curUrl = get_permalink();

		$purl = parse_url($curUrl);
		if(!$purl['query']) $link_add = "?";
		else $link_add = "&";

		$page_block = 10; 

		if(empty($brdSet->b_width)) $brdSet->b_width = "100";
		if($brdSet->b_width <= 100) $b_width = $brdSet->b_width."%";
		else $b_width = $brdSet->b_width."px";

		if(empty($brdSet->b_align)) $brdSet->b_align = "L";
		switch($brdSet->b_align){
			case "C": $b_align = "margin:0 auto;";break;
			case "R": $b_align = "float:right;";break;
			case "L": default: $b_align = "float:left;";break;
		}
		
		if(empty($_GET['mode'])) $_GET['mode'] = "";
		if(empty($_GET['search_chk'])) $_GET['search_chk'] = "";
		if(empty($_GET['keyfield'])) $_GET['keyfield'] = "";
		if(empty($_GET['keyword'])) $_GET['keyword'] = "";
		if(empty($_GET['cate'])) $_GET['cate'] = "";
		if(empty($_GET['no'])) $_GET['no'] = "";
		if(empty($_GET['ref'])) $_GET['ref'] = "";
		if(empty($_GET['page'])) $_GET['page'] = 1;

		if(!empty($brdSet->b_read_lv)){
			if(($brdSet->b_read_lv == "author" && $curUserPermision == "all") || ($brdSet->b_read_lv == "administrator" && $curUserPermision != "administrator")){
				echo "<div class='NFBoard_error_msg'>게시판 읽기권한이 없습니다.</div>";
				return "";
			}
		}
			
		$where = "where movecheck<>'0'";
		if(!empty($_GET['search_chk']) && $_GET['search_chk'] == 1){
			$where .= " and ".$_GET['keyfield']." like '%".$_GET['keyword']."%'";
		}
		if(!empty($_GET['cate'])){
			$where .= " and category='".$_GET['cate']."'";
		}

		$total = $wpdb->get_var("select count(*) from NFB_".$bname."_board ".$where);
		$totalpage = ceil($total / $brdSet->b_psize);
		$startpos = ($_GET['page'] - 1) * $brdSet->b_psize;
		if($startpos > $total) $startpos = 0;
		if($_GET['page'] > $totalpage) $_GET['page'] = 1;

		$orderby = "";
		if($brdSet->b_type == "1"){
			$orderby .= " order by use_notice desc, ref desc, re_step asc limit ".$startpos.", ".$brdSet->b_psize;
		
		}else{
			$orderby .= " order by ref desc, re_step asc limit ".$startpos.", ".$brdSet->b_psize;
		}
		$result = $wpdb->get_results("select * from NFB_".$bname."_board ".$where.$orderby);

		if(!empty($brdSet->b_category)){
			$category_arr = explode(",", $brdSet->b_category);

			$select_category = "<select name=\"category\" onchange=\"location.href = this.value\">";
			$select_category .= "<option value=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'])."\">카테고리</option>";
			for($i = 0; $i < count($category_arr); $i++){
				$select_category .= "<option value=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $category_arr[$i])."\"";
				if($_GET['cate'] == $category_arr[$i]){
					$select_category .= "selected";
				}	
				$select_category .= ">".$category_arr[$i]."</option>";
			}
			$select_category .= "</select>";
		
		}else $select_category = "";
		
		$list_move = "onclick=\"listAction('".NFB_HOME_URL."/?NFPage=board-move&page_id=".$page_id."&".build_param($bname, $_GET['mode'], "", $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."', 'move');\"";
		$list_copy = "onclick=\"listAction('".NFB_HOME_URL."/?NFPage=board-move&page_id=".$page_id."&".build_param($bname, $_GET['mode'], "", $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."', 'copy');\"";
		$list_delete = "onclick=\"listDelete('".NFB_HOME_URL."/?NFPage=board-delete-process&page_id=".$page_id."&".build_param($bname, $_GET['mode'], "", $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."');\"";

		if($_GET['page'] == 1) $pagelink_first = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"pre_end\">";
		else $pagelink_first = "<a href=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", "1", $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\" class=\"pre_end\">";
		
		$page_temp = floor(($_GET['page'] - 1) / $page_block) * $page_block + 1;
		if($page_temp == 1){
			$pagelink_pre = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"pre\">";
		}else{
			$n_page = $page_temp - $page_block;
			$pagelink_pre = "<a href=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $n_page, $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\" class=\"pre\">";
		}
		$pagelink_view = "";
		for($intloop = 1; $intloop <= $page_block && $page_temp <= $totalpage; $intloop++){
			if($intloop == 1){ 
				$first_cls = " class='fir'";
				$first_style = " style='border:none;'";
				$page_nbsp = "";
			}else{ 
				$first_cls = "";
				$first_style = "";
				$page_nbsp = "&nbsp;";
			}
			if($page_temp == $_GET['page']){
				$pagelink_view = $pagelink_view.$page_nbsp."<strong".$first_style.">".$page_temp."</strong>" ;
			}else{
				$pagelink_view = $pagelink_view.$page_nbsp."<a href=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $page_temp, $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\"".$first_cls.">".$page_temp."</a>";
			}
			$page_temp = $page_temp + 1;
		}

		if($page_temp > $totalpage){
			$pagelink_next = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"next\">";
		}else{
			$pagelink_next = "<a href=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $page_temp, $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\" class=\"next\">";
		}

		if($_GET['page'] == $totalpage|| $totalpage == 0){
			$pagelink_last = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"next_end\">";
		}else{
			$pagelink_last = "<a href=\"".$curUrl.$link_add.build_param($bname, $_GET['mode'], "", $totalpage, $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\" class=\"next_end\">";
		}
		$list_write = "onclick=\"location.href='".$curUrl.$link_add.build_param($bname, 'write', '', $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";
		if(is_dir(NFB_ABS."templates/board/".$brdSet->b_skin)){
			if($total > 0){
				$num = $total - (($_GET['page'] - 1) * $brdSet->b_psize);
			}
			require_once(NFB_ABS."templates/board/".$brdSet->b_skin."/tf_list.php");
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}

		return "";
	}

	public function LoadWrite($bname=""){
		global $wpdb, $NFB_SID;

		$detect = new NFB_MobileDetect;
		$curUserPermision = current_user_level();
		$current_user = wp_get_current_user();

		$page_id = get_queried_object_id();
		$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $bname));
		$curUrl = get_permalink();
		
		if($detect->isMobile()) {
			$brdSet->b_editor = "N";
		}

		$purl = parse_url($curUrl);
		if(!$purl['query']) $link_add = "?";
		else $link_add = "&";

		if(empty($brdSet->b_width)) $brdSet->b_width = "100";
		if($brdSet->b_width <= 100) $b_width = $brdSet->b_width."%";
		else $b_width = $brdSet->b_width."px";

		if(empty($brdSet->b_align)) $brdSet->b_align = "L";
		switch($brdSet->b_align){
			case "C": $b_align = "margin:0 auto;";break;
			case "R": $b_align = "float:right;";break;
			case "L": default: $b_align = "float:left;";break;
		}
		
		if(empty($_GET['mode'])) $_GET['mode'] = "";
		if(empty($_GET['search_chk'])) $_GET['search_chk'] = "";
		if(empty($_GET['keyfield'])) $_GET['keyfield'] = "";
		if(empty($_GET['keyword'])) $_GET['keyword'] = "";
		if(empty($_GET['cate'])) $_GET['cate'] = "";
		if(empty($_GET['no'])) $_GET['no'] = "";
		if(empty($_GET['ref'])) $_GET['ref'] = "";
		if(empty($_GET['page'])) $_GET['page'] = 1;

		if(!empty($brdSet->b_write_lv)){
			if(($brdSet->b_write_lv == "author" && $curUserPermision == "all") || ($brdSet->b_write_lv == "administrator" && $curUserPermision != "administrator")){
				echo "<div class='NFBoard_error_msg'>게시판 쓰기권한이 없습니다.</div>";
				return "";
			}
		}

		if($_GET['no'] > 0 && empty($_GET['ref'])){
			$brdData = $wpdb->get_row($wpdb->prepare("select * from NFB_".$bname."_board where no=%d", $_GET['no']));
			$tMode = "modify";
		}else if($_GET['no'] > 0 && $_GET['ref'] > 0){
			if(($brdSet->l_reply == "author" && $curUserPermision == "all") || ($brdSet->l_reply == "administrator" && $curUserPermision != "administrator")){
				echo "<div class='NFBoard_error_msg'>게시판 답변쓰기 권한이 없습니다.</div>";
				return "";
			}
			$brdData = $wpdb->get_row($wpdb->prepare("select * from NFB_".$bname."_board where no=%d", $_GET['no']));
			$tMode = "reply";
		}else $tMode = "insert";
		
		if(!empty($brdSet->b_category)){
			$category_arr = explode(",", $brdSet->b_category);

			$select_category = "<select name=\"category\" id=\"category\" onchange=\"if(this.value != '') fieldCheck();\">";
			$select_category .= "<option value=\"\">카테고리</option>";
			for($i = 0; $i < count($category_arr); $i++){
				$select_category .= "<option value=\"".$category_arr[$i]."\"";
				
				if(!empty($brdData->category) && ($brdData->category == $category_arr[$i])){
					$select_category .= "selected";
				}	
									
				$select_category .= ">".$category_arr[$i]."</option>";
			}
			$select_category .= "</select> &nbsp;";
		
		}else $select_category = "";

		$prvCnfData = $wpdb->get_row("select * from NFB_setup");
		
		if($tMode == "modify"){
			$writer = $brdData->writer;

			$title = $brdData->title;
			$content = $brdData->content;
			if($brdData->file1) $view_file1 = "<p class=\"i_dsc\">".$brdData->file1." <font style=\"color:#e46c0a;\">[삭제]</font> <input type=\"checkbox\" name=\"file_del1\" value=\"1\" /></p>";
			else $view_file1 = "";
			if($brdData->file2) $view_file2 = "<p class=\"i_dsc\">".$brdData->file2." <font style=\"color:#e46c0a;\">[삭제]</font> <input type=\"checkbox\" name=\"file_del2\" value=\"1\" /></p>";
			else $view_file2 = "";

		}else if($tMode == "reply"){
			$writer = $current_user->user_login;
			$title = "[Re]".$brdData->title;
			$content = $brdData->content.chr(13)."----------------------------------------------------------".chr(13);
		
		}else{
			$writer = $current_user->user_login;
		}

		$config = $wpdb->get_row("select * from NFB_setup");
		if($config->use_ssl == 1 && !empty($config->ssl_domain)){
			$plugin_path_arr = explode("/", NFB_ABS);
			$path_end = substr($config->ssl_domain, -1);
			if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
			else $ssl_domain = $config->ssl_domain;
			$action_url = "https://".$ssl_domain;
			if(empty($config->ssl_port)) $action_url .= ":443/";
			else $action_url .= ":".$config->ssl_port."/";
			$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
		}else{
			$action_url = NFB_WEB;
		}
		
		if(is_dir(NFB_ABS."templates/board/".$brdSet->b_skin)){
			require_once(NFB_ABS."templates/board/".$brdSet->b_skin."/tf_write.php");
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}
		
		return "";
	}

	public function LoadView($bname=""){
		global $wpdb, $NFB_SID;

		$curUserPermision = current_user_level();
		$current_user = wp_get_current_user();

		$page_id = get_queried_object_id();
		$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $bname));
		$curUrl = get_permalink();
		
		$purl = parse_url($curUrl);
		if(!$purl['query']) $link_add = "?";
		else $link_add = "&";

		if(empty($brdSet->b_width)) $brdSet->b_width = "100";
		if($brdSet->b_width <= 100) $b_width = $brdSet->b_width."%";
		else $b_width = $brdSet->b_width."px";

		if(empty($brdSet->b_align)) $brdSet->b_align = "L";
		switch($brdSet->b_align){
			case "C": $b_align = "margin:0 auto;";break;
			case "R": $b_align = "float:right;";break;
			case "L": default: $b_align = "float:left;";break;
		}
		
		if(empty($_GET['mode'])) $_GET['mode'] = "";
		if(empty($_GET['search_chk'])) $_GET['search_chk'] = "";
		if(empty($_GET['keyfield'])) $_GET['keyfield'] = "";
		if(empty($_GET['keyword'])) $_GET['keyword'] = "";
		if(empty($_GET['cate'])) $_GET['cate'] = "";
		if(empty($_GET['no'])) $_GET['no'] = "";
		if(empty($_GET['ref'])) $_GET['ref'] = "";
		if(empty($_GET['page'])) $_GET['page'] = 1;

		if(!empty($brdSet->b_read_lv)){
			if(($brdSet->b_read_lv == "author" && $curUserPermision == "all") || ($brdSet->b_read_lv == "administrator" && $curUserPermision != "administrator")){
				echo "<div class='NFBoard_error_msg'>게시판 읽기권한이 없습니다.</div>";
				return "";
			}
		}
		
		$comment_write_check = true;
		if(!empty($brdSet->b_comment_lv)){
			if(($brdSet->b_comment_lv == "author" && $curUserPermision == "all") || ($brdSet->b_comment_lv == "administrator" && $curUserPermision != "administrator")){
				$comment_write_check = false;
			}
		}

		$brdData = $wpdb->get_row($wpdb->prepare("select * from NFB_".$bname."_board where no=%d", $_GET['no']));
		$wpdb->query($wpdb->prepare("update NFB_".$bname."_board set hit=hit+1 where no=%d", $_GET['no']));
		
		if(empty($brdData->no)){
			echo "해당 게시물이 존재하지 않습니다.";
			return "";
		}

		if((!empty($brdSet->b_secret_use) && $brdSet->b_secret_use == 1) && (!empty($brdData->use_secret) && $brdData->use_secret == 1) && empty($_POST['passcheck'])){
			if(empty($curUserPermision) || $curUserPermision != "administrator"){
				if(!empty($brdData->listnum) && $brdData->listnum == 1){  // 원본글
					if(empty($brdData->memnum) || $brdData->memnum == 0){
						echo "
							<script type='text/javascript'>
							location.href = '".NFB_HOME_URL."/?NFPage=board-move-process&page_id=".$page_id."&".build_param($bname, $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';
							</script>";
						exit;
					
					}else{
						if($brdData->memnum != $current_user->ID){
							echo "<div class='NFBoard_error_msg'>해당글에 대한 보기 권한이 없습니다.</div>";
							return "";
						}
					}
				}else{
					$ref_memnum = $wpdb->get_var($wpdb->prepare("select memnum from NFB_".$bname."_board where no=%d", $brdData->ref));

					if(empty($ref_memnum)){
						echo "<div class='NFBoard_error_msg'>원본글이 삭제 되었거나 존재하지 않습니다.</div>";
						return "";
					
					}else{
						if($ref_memnum == 0){
							echo "
								<script type='text/javascript'>
								location.href = '".NFB_HOME_URL."/?NFPage=board-move-process&page_id=".$page_id."&".build_param($bname, $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';
								</script>";
							exit;
						
						}else{
							if($brdData->memnum != $current_user->ID){
								echo "<div class='NFBoard_error_msg'>해당글에 대한 보기 권한이 없습니다.</div>";
								return "";
							}
						}
					}
				}
			}
		}
		
		if(!empty($brdData->write_date)){
			$tmp1 = explode(" ", $brdData->write_date);
			$tmp2 = explode("-", $tmp1[0]);
			$tmp3 = explode(":", $tmp1[1]);
			$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
		}

		if(!empty($brdData->re_level) && $brdData->re_level != 0) $pict = $unique."_re";
		else $pict = $brdData->ref."_";

		if(!empty($brdData->file1)){
			$oFile1 = explode(".", $brdData->file1);

			$filepath1 = NFB_UPLOAD_PATH."NFBoard/".$brdSet->b_no."_".$pict."1.".$oFile1[1];
			$filename1 = NFB_CONTENT_URL."/uploads/NFBoard/".$brdSet->b_no."_".$pict."1.".$oFile1[1];
			
			if(file_exists($filepath1)){
				$img_size1 = getimagesize($filepath1);
				$img_width1 = $img_size1[0];
				$img_height1 = $img_size1[1];

				if($img_width1 >= 550){
					$img_width1 = 550;
					$width_per1 = round(550 / $img_size1[0], 2);
				
				}else{
					$img_width1 = $img_size1[0];
					$width_per1 = 1;
				}
				$img_height1 = $img_height1 * $width_per1;
				$filesize1 = floor(filesize($filepath1) / 1024 + 1);
				if(strtolower($oFile1[1]) == "jpg" || strtolower($oFile1[1]) == 'jpeg' || strtolower($oFile1[1]) == 'gif' || strtolower($oFile1[1]) == 'bmp' || strtolower($oFile1[1]) == 'png'){
					$view_file_result1 = "<a href=\"javascript:open_window('img_view', '".NFB_HOME_URL."/?NFPage=board-popup&file=".$brdSet->b_no."_".$pict."1.".$oFile1[1]."', 0, 0, 100, 100, 0, 0, 0, 0, 0);\" class=\"image_view\"><img src='".$filename1."' border='0' width='".$img_width1."' height='".$img_height1."' alt='첨부파일1' /></a><br />";
				}
				$file_download1 = "<a href=\"".NFB_HOME_URL."/?NFPage=board-download&file=".$brdData->file1."&filepath=".$brdSet->b_no."_".$pict."1.".$oFile1[1]."\">".$brdData->file1." (file size ".$filesize1."KB)</a>";
			}else{
				$file_download1 = $brdData->file1;
			}
		}

		if(!empty($brdData->file2)){
			$oFile2 = explode(".", $brdData->file2);

			$filepath2 = NFB_UPLOAD_PATH."NFBoard/".$brdSet->b_no."_".$pict."2.".$oFile2[1];
			$filename2 = NFB_CONTENT_URL."/uploads/NFBoard/".$brdSet->b_no."_".$pict."2.".$oFile2[1];

			if(file_exists($filepath2)){
				$img_size2 = getimagesize($filepath2);
				$img_width2 = $img_size2[0];
				$img_height2 = $img_size2[1];

				if($img_width2 >= 550){
					$img_width2 = 550;
					$width_per2 = round(550 / $img_size2[0], 2);
				
				}else{
					$img_width2 = $img_size2[0];
					$width_per2 = 1;
				}
				$img_height2 = $img_height2 * $width_per2;
				$filesize2 = floor(filesize($filepath2) / 1024 + 1);
				if(strtolower($oFile2[1]) == "jpg" || strtolower($oFile2[1]) == 'jpeg' || strtolower($oFile2[1]) == 'gif' || strtolower($oFile2[1]) == 'bmp' || strtolower($oFile2[1]) == 'png'){
					$view_file_result2 = "<a href=\"javascript:open_window('img_view', '".NFB_HOME_URL."/?NFPage=board-popup&file=".$brdSet->b_no."_".$pict."2.".$oFile2[1]."', 0, 0, 100, 100, 0, 0, 0, 0, 0);\" class=\"image_view\"><img src='".$filename2."' border='0' width='".$img_width2."' height='".$img_height2."' alt='첨부파일2' /></a><br />";
				}
				$file_download2 = "<a href=\"".NFB_HOME_URL."/?NFPage=board-download&file=".$brdData->file2."&filepath=".$brdSet->b_no."_".$pict."2.".$oFile2[1]."\">".$brdData->file2." (file size ".$filesize2."KB)</a>";
			}else{
				$file_download2 = $brdData->file2;
			}
		}
		
		$content = $brdData->content;

		if($brdData->use_html == 0){
			$content = nl2br(htmlentities($content,  ENT_QUOTES, "utf-8"));
		}else if($brdData->use_html == 1){
		}else if($brdData->use_html == 2){
		}

		if($curUserPermision == "administrator" || $brdData->memnum == $current_user->ID || $brdData->memnum == 0){
			$view_modify = "onclick=\"location.href='".$curUrl.$link_add.build_param($bname, 'write', $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";
		}else{
			$view_modify = "style=\"display:none;\"";
		}
		
		if(!empty($curUserPermision) && $curUserPermision == "administrator"){
			$view_delete =  "onclick=\"top.frames['delete_hidden'].location.href='".NFB_HOME_URL."/?NFPage=board-delete&page_id=".$page_id."&".build_param($bname, $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";
			$view_delete_hidden = "<iframe name=\"delete_hidden\" id=\"delete_hidden\" style=\"display:none;\"></iframe>";
		}else{
			if((!empty($brdData->memnum) && $brdData->memnum != 0) && $brdData->memnum == $current_user->ID){
				$view_delete = "onclick=\"top.frames['delete_hidden'].location.href='".NFB_HOME_URL."/?NFPage=board-delete&page_id=".$page_id."&".build_param($bname, $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."'\"";
				$view_delete_hidden = "<iframe name=\"delete_hidden\" id=\"delete_hidden\" style=\"display:none;\"></iframe>";
			}else if((!empty($brdData->memnum) && $brdData->memnum != 0) && $brdData->memnum != $current_user->ID){
				$view_delete = "style=\"display:none;\"";
			}
			if($brdData->memnum == 0){
				$view_delete =  "onclick=\"location.href='".NFB_HOME_URL."/?NFPage=board-delete&page_id=".$page_id."&".build_param($bname, $_GET['mode'], $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";
			}
		}
		
		if(empty($brdData->use_notice) || $brdData->use_notice == 0){
			$view_reply = "onclick=\"location.href='".$curUrl.$link_add.build_param($bname, 'write', $_GET['no'], $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'], $brdData->ref)."';\"";
		}else{
			$view_reply = "style=\"display:none;\"";
		}
		
		$view_write = "onclick=\"location.href='".$curUrl.$link_add.build_param($bname, 'write', '', $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";
		
		if(!empty($curUserPermision) && $curUserPermision != 'all') $vmnum = $current_user->ID;
		else $vmnum = "0";
		
		$view_list =  "onclick=\"location.href='".$curUrl.$link_add.build_param($bname, 'list', '', $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."';\"";

		if(empty($brdData->use_notice) || $brdData->use_notice == 0){
			$sql_common = "";
			if(!empty($_GET['search_chk']) && $_GET['search_chk'] == 1){
				$sql_common .= " and ".$_GET['keyfield']." like '%".$_GET['keyword']."%'";
			}
			if(!empty($brdSet->b_category)){
				$category_arr = explode(",", $brdSet->b_category);
				if(!empty($_GET['cate']) && count($category_arr) > 0) {
					$sql_common .= " and category='".$_GET['cate']."'";
				}
			}

			$maxnum = $wpdb->get_var("select max(no) from NFB_".$bname."_board where use_notice=0 and listnum=1 and movecheck!='0'".$sql_common);
			$minnum = $wpdb->get_var("select min(no) from NFB_".$bname."_board where use_notice=0 and listnum=1 and movecheck!='0'".$sql_common);
			$nextnum = $wpdb->get_var("select min(no) from NFB_".$bname."_board where use_notice=0 and listnum=1 and movecheck!='0' and no>".$brdData->ref.$sql_common);
			$prevnum = $wpdb->get_var("select max(no) from NFB_".$bname."_board where use_notice=0 and listnum=1 and movecheck!='0' and no<".$brdData->ref.$sql_commo);
			$next = $wpdb->get_row("select title, write_date from NFB_".$bname."_board where no='".$nextnum."'");
			$prev = $wpdb->get_row("select title, write_date from NFB_".$bname."_board where no='".$prevnum."'");

			if($brdData->ref >= $maxnum){
				$nextlink = "다음글이 없습니다.";
				$next_wdate = "";
			}else{
				$nextlink = "<a href=\"".$curUrl.$link_add.build_param($bname, 'view', $nextnum, $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\">".$next->title."</a>";
				$next_wdate = $next->write_date;
			}

			if($brdData->ref <= $minnum){
				$prevlink = "이전글이 없습니다.";
				$prev_wdate = "";
			}else{
				$prevlink = "<a href=\"".$curUrl.$link_add.build_param($bname, 'view', $prevnum, $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\">".$prev->title."</a>";
				$prev_wdate = $prev->write_date;
			}
		
		}else if(!empty($brdData->use_notice) && $brdData->use_notice == 1){
			$maxnum = $wpdb->get_var("select max(no) from NFB_".$bname."_board where use_notice=1");
			$minnum = $wpdb->get_var("select min(no) from NFB_".$bname."_board where use_notice=1");
			$nextnum = $wpdb->get_var($wpdb->prepare("select min(no) from NFB_".$bname."_board where use_notice=1 and no>%d",$_GET['no']));
			$prevnum = $wpdb->get_var($wpdb->prepare("select max(no) from NFB_".$bname."_board where use_notice=1 and no<%d", $_GET['no']));
			$next = $wpdb->get_row($wpdb->prepare("select title, write_date from NFB_".$bname."_board where no=%d", $nextnum));
			$prev = $wpdb->get_row($wpdb->prepare("select title, write_date from NFB_".$bname."_board where no=%d", $prevnum));
			
			if($_GET['no'] == $maxnum){
				$nextlink = "다음글이 없습니다.";
				$next_wdate = "";
			}else{
				$nextlink = "<a href=\"".$curUrl.$link_add.build_param($bname, 'view', $nextnum, $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\">".$next->title."</a>";
				$next_wdate = $next->write_date;
			}

			if($_GET['no'] == $minnum){
				$prevlink = "이전글이 없습니다.";
				$prev_wdate = "";
			}else{
				$prevlink = "<a href=\"".$curUrl.$link_add.build_param($bname, 'view', $prevnum, $_GET['page'], $_GET['keyfield'], $_GET['keyword'], $_GET['search_chk'], $_GET['cate'])."\">".$prev->title."</a>";
				$prev_wdate = $prev->write_date;
			}
		}

		if(!empty($brdSet->b_comment_use) && $brdSet->b_comment_use == 1){
			$option = " where parent='".$_GET['no']."'";

			$comment_result = $wpdb->get_results($wpdb->prepare("select * from NFB_".$bname."_comment where parent=%s order by comm_parent asc, depth asc, no asc", $_GET['no']));
			$comment_cnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_".$bname."_comment where parent=%s", $_GET['no']));
		}

		if(empty($comment_cnt) || $comment_cnt == 0){
			$comment_list = "";
		
		}else{
			$comment_list = "<div class='comment_list_box'>";
			$comment_list .= "<ul id='comment_list' class='comment_list'>";
			
			foreach($comment_result as $i => $list){

				$reply_cnt = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_".$bname."_comment where comm_parent=%s and depth>0", $list->no));
				
				if(!empty($reply_cnt) && $reply_cnt > 0){
					$comment_del_link = "onclick=\"commentFail(jQuery(this), 'child');\"";
				}else{
					if($curUserPermision == "administrator"){
						$comment_del_link = "onclick=\"commentDeleteCheck('".$list->no."', jQuery(this));\"";
					}else{
						if(empty($list->memnum) || $list->memnum == 0){
							$comment_del_link = "onclick=\"deleteConfirm(jQuery(this), '".$list->no."');\"";
						}else{ 
							if($list->memnum == $current_user->ID){
								$comment_del_link = "onclick=\"commentDeleteCheck('".$list->no."', jQuery(this));\"";
							}else{
								$comment_del_link = "onclick=\"commentFail(jQuery(this), 'delete');\"";
							}
						}
					}
				}

				if(empty($list->depth) || $list->depth == 0){
					$comment_reply_link = "onclick=\"commentReply('".$list->no."', jQuery(this));\"";
					$add_cls = " panel panel-default comm";
				}else if($list->depth >= 1){
					$comment_reply_link = "onclick=\"commentFail(jQuery(this), 'reply');\"";
					$add_cls = " reply comm_dot";
				}

				$c_content = str_replace(chr(13), "<br />", $list->content);
				$comment_list .= "<li class='".$add_cls."'>";
				$comment_list .=	"<p class='name'>".$list->writer." <span class='date'>".$list->write_date."</span></p>";
				$comment_list .= "<p class='con'>".$c_content."</p>";
				$comment_list .= "<p><button type='button' class='btn btn-default btn-xs' ".$comment_del_link.">삭제</button>";
				if($comment_write_check == true && $list->depth == 0){
					$comment_list .= "&nbsp;<button type='button' class='btn btn-default btn-xs' ".$comment_reply_link.">댓글</button></p>";
				}
				$comment_list .= "</li>";
			}

			$comment_list .= "</ul>";
			$comment_list .= "</div>";
		}

		$cert_time = time();
		$cert_encode = base64_encode(base64_encode($cert_time)."\n");
		$cert = "<input type='hidden' name='cert' value='$cert_encode' />";

		$config = $wpdb->get_row("select * from NFB_setup");
		if($config->use_ssl == 1 && !empty($config->ssl_domain)){
			$plugin_path_arr = explode("/", NFB_ABS);
			$path_end = substr($config->ssl_domain, -1);
			if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
			else $ssl_domain = $config->ssl_domain;
			$action_url = "https://".$ssl_domain;
			if(empty($config->ssl_port)) $action_url .= ":443/";
			else $action_url .= ":".$config->ssl_port."/";
			$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
		}else{
			$action_url = NFB_WEB;
		}
		
		if(is_dir(NFB_ABS."templates/board/".$brdSet->b_skin)){
			require_once(NFB_ABS."templates/board/".$brdSet->b_skin."/tf_view.php");
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}
		
		return "";
	}
	public function copyright(){
		echo base64_decode("PHNwYW4gY2xhc3M9J2NvcHlyaWdodCc+UG93ZXJlZCBieSA8YSBocmVmPSdodHRwOi8vd3d3Lm5mYm9hcmQuY28ua3InIHRhcmdldD0nX2JsYW5rJyB0aXRsZT0nTkYgQk9BUkQnPk5GIEJPQVJEPC9hPjwvc3Bhbj4=");
	}

	public function NFMessageBox($msg) {
		if(!$msg) return false;
		ob_start();
		include_once NFB_ABS."templates/board/".get_option("NFB_skin")."/tf_errorbox.php";
		$fileContent = ob_get_contents();
		ob_end_clean();
		return $fileContent;
	}


}

class NFB_Member {
	var $row;
	var $total;
	var $orderby;
	var $order;

	public function getList($val1="", $val2="", $val3="", $start_pos="", $per_page=""){
		global $wpdb;

		if(!empty($val1)) $this->orderby = $val1;
		else $this->orderby = "uno";

		if(!empty($val2)) $this->order = $val2;
		else $this->order = "desc";

		if(!empty($val3)){
			$where = " and (user_id='".$val3."' or name='".$val3."' or email='".$val3."')";
		}else $where = "";
		
		if(isset($start_pos) && !empty($per_page) && empty($val3)){
			if($where!="") {
				$sql = $wpdb->prepare("select * from NFB_member where uno<>'' and uno is not null and (user_id=%s or name=%s or email=%s) order by ".$this->orderby." ".$this->order." limit %d, %d", $val3, $val3, $val3, $start_pos, $per_page);
			}else{
				$sql = $wpdb->prepare("select * from NFB_member where uno<>'' and uno is not null order by ".$this->orderby." ".$this->order." limit %d, %d", $start_pos, $per_page);
			}
			$this->row = $wpdb->get_results($sql);
		}else{
			if($where!="") {
				$this->row = $wpdb->get_results($wpdb->prepare("select * from NFB_member where uno<>'' and uno is not null (user_id=%s or name=%s or email=%s) order by ".$this->orderby." ".$this->order, $val3, $val3, $val3));
			}else{
				$this->row = $wpdb->get_results("select * from NFB_member where uno<>'' and uno is not null order by ".$this->orderby." ".$this->order);
			}
		}

		return $this->row;
	}

	public function getCount($keyword=""){
		global $wpdb;

		if(!empty($keyword)){
			$where = " and (user_id='".$keyword."' or name='".$keyword."' or email='".$keyword."')";
		}else $where = "";
		if($where != "") {
			$this->total = $wpdb->get_var($wpdb->prepare("select count(*) from NFB_member where uno<>'' and uno is not null and (user_id=%s or name=%s or email=%s)", $keyword, $keyword, $keyword));
		}else{
			$this->total = $wpdb->get_var("select count(*) from NFB_member where uno<>'' and uno is not null");
		}
		return $this->total;
	}

	public function getView($no){
		global $wpdb;

		if(empty($no)) return;

		$this->row = $wpdb->get_row($wpdb->prepare("select * from NFB_member where uno=%s", $no));
		return $this->row;
	}

	public function LoadJoin(){
		global $wpdb, $current_user;

		$skin = get_option("NFB_skin");
		if(empty($skin)) $skin = "Default";
		$config = $wpdb->get_row("select * from NFB_setup limit 1");

		if(!empty($current_user->user_login)){
			$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where user_id=%s", $current_user->user_login));
			if(empty($rows->user_id)){
				echo $this->NFMessageBox("일반회원이 아닙니다.");
				return;
			}
		}
	
		if(is_file(NFB_ABS."templates/member/".$skin."/tf_join.php")){
			$curUrl = NFB_SITE_URL;
			if($config->use_ssl == 1 && !empty($config->ssl_domain)){
				$plugin_path_arr = explode("/", NFB_ABS);
				$path_end = substr($config->ssl_domain, -1);
				if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
				else $ssl_domain = $config->ssl_domain;
				$action_url = "https://".$ssl_domain;
				if(empty($config->ssl_port)) $action_url .= ":443/";
				else $action_url .= ":".$config->ssl_port."/";
				$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
			}else{
				$action_url = NFB_WEB;
			}
			require_once(NFB_ABS."templates/member/".$skin."/tf_join.php");
		}else{
			echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
		}
	}

	public function LoadLeave(){
		global $current_user, $wpdb;

		if(!is_user_logged_in()){
			echo $this->NFMessageBox("로그인 후 이용해주세요.");
			return;
		}
		$skin = get_option("NFB_skin");
		if(empty($skin)) $skin = "Default";
		$config = $wpdb->get_row("select * from NFB_setup");

		if(!empty($current_user->user_login)){
			$rows = $wpdb->get_row($wpdb->prepare("select * from NFB_member where user_id=%s", $current_user->user_login));
			if(empty($rows->user_id)){
				echo $this->NFMessageBox("일반회원이 아닙니다.");
				return;
			}
		}

		if(is_file(NFB_ABS."templates/member/".$skin."/tf_leave.php")){
			$curUrl = NFB_SITE_URL;
			if($config->use_ssl == 1 && !empty($config->ssl_domain)){
				$plugin_path_arr = explode("/", NFB_ABS);
				$path_end = substr($config->ssl_domain, -1);
				if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
				else $ssl_domain = $config->ssl_domain;
				$action_url = "https://".$ssl_domain;
				if(empty($config->ssl_port)) $action_url .= ":443/";
				else $action_url .= ":".$config->ssl_port."/";
				$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
			}else{
				$action_url = NFB_WEB;
			}
			require_once(NFB_ABS."templates/member/".$skin."/tf_leave.php");
		}else{
			echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
		}
	}

	public function LoadLogin(){
		global $current_user, $wpdb;

		$skin = get_option("NFB_skin");
		if(empty($skin)) $skin = "Default";

		$config = $wpdb->get_row("select * from NFB_setup");

		if(is_user_logged_in()){
			if(is_file(NFB_ABS."templates/member/".$skin."/tf_logout.php")){
				$curUrl = NFB_SITE_URL;
				/* SSL 처리 */
				if($config->use_ssl == 1 && !empty($config->ssl_domain)){
					$plugin_path_arr = explode("/", NFB_ABS);
					$path_end = substr($config->ssl_domain, -1);
					if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
					else $ssl_domain = $config->ssl_domain;
					$action_url = "https://".$ssl_domain;
					if(empty($config->ssl_port)) $action_url .= ":443/";
					else $action_url .= ":".$config->ssl_port."/";
					$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
				}else{
					$action_url = NFB_WEB;
				}
				require_once(NFB_ABS."templates/member/".$skin."/tf_logout.php");
			}else{
				echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
			}
		}else{
			if(is_file(NFB_ABS."templates/member/".$skin."/tf_login.php")){
				$curUrl = NFB_SITE_URL;
				if($config->use_ssl == 1 && !empty($config->ssl_domain)){
					$plugin_path_arr = explode("/", NFB_ABS);
					$path_end = substr($config->ssl_domain, -1);
					if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
					else $ssl_domain = $config->ssl_domain;
					$action_url = "https://".$ssl_domain;
					if(empty($config->ssl_port)) $action_url .= ":443/";
					else $action_url .= ":".$config->ssl_port."/";
					$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
				}else{
					$action_url = NFB_WEB;
				}
				require_once(NFB_ABS."templates/member/".$skin."/tf_login.php");
			}else{
				echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
			}
		}
	}

	public function userLogout(){
		if(is_user_logged_in()){
			$redirect_url = NFB_SITE_URL;
	
			$logout = "<script type='text/javascript'>location.href = '".str_replace("&amp;", "&", wp_logout_url($redirect_url))."';</script>";
			echo $logout;
		}
		return "";
	}

	public function LoadIDFind(){
		global $current_user, $wpdb;

		$skin = get_option("NFB_skin");
		if(empty($skin)) $skin = "Default";

		if(is_user_logged_in()){
			echo $this->NFMessageBox("로그인 상태입니다.");
			return;
		}
		$config = $wpdb->get_row("select * from NFB_setup");

		if(is_file(NFB_ABS."templates/member/".$skin."/tf_id_find.php")){
			$curUrl = NFB_SITE_URL;
			if($config->use_ssl == 1 && !empty($config->ssl_domain)){
				$plugin_path_arr = explode("/", NFB_ABS);
				$path_end = substr($config->ssl_domain, -1);
				if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
				else $ssl_domain = $config->ssl_domain;
				$action_url = "https://".$ssl_domain;
				if(empty($config->ssl_port)) $action_url .= ":443/";
				else $action_url .= ":".$config->ssl_port."/";
				$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
			}else{
				$action_url = NFB_WEB;
			}
			require_once(NFB_ABS."templates/member/".$skin."/tf_id_find.php");
		}else{
			echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
		}
	}

	public function LoadPWFind(){
		global $current_user, $wpdb;

		if(is_user_logged_in()){
			echo $this->NFMessageBox("로그인 상태입니다.");
			return;
		}
		$skin = get_option("NFB_skin");
		if(empty($skin)) $skin = "Default";

		$config = $wpdb->get_row("select * from NFB_setup");

		if(is_file(NFB_ABS."templates/member/".$skin."/tf_pw_find.php")){
			$curUrl = NFB_SITE_URL;
			if($config->use_ssl == 1 && !empty($config->ssl_domain)){
				$plugin_path_arr = explode("/", NFB_ABS);
				$path_end = substr($config->ssl_domain, -1);
				if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
				else $ssl_domain = $config->ssl_domain;
				$action_url = "https://".$ssl_domain;
				if(empty($config->ssl_port)) $action_url .= ":443/";
				else $action_url .= ":".$config->ssl_port."/";
				$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
			}else{
				$action_url = NFB_WEB;
			}
			require_once(NFB_ABS."templates/member/".$skin."/tf_pw_find.php");
		}else{
			echo $this->NFMessageBox("스킨 파일이 존재하지 않습니다.");
		}
	}
	public function NFMessageBox($msg) {
		if(!$msg) return false;
		ob_start();
		include_once NFB_ABS."templates/member/".get_option("NFB_skin")."/tf_errorbox.php";
		$fileContent = ob_get_contents();
		ob_end_clean();
		return $fileContent;
	}

}

class NFB_LOAD {

	var $bname;

	function __construct(){
		add_action('wp_head', 'NFB_Meta_Basic');
		add_action('wp', array($this, 'NFB_Detect_shortcode_board'));
		add_action('wp', array($this, 'NFB_Detect_shortcode_seo'));
	}

	function NFB_Detect_shortcode_board(){
		global $wpdb, $wp_query;	
		$post = $wp_query->posts;
		
		$content = strip_tags($post[0]->post_content);
		preg_match_all("/\[[^]]+\]/", $content, $check_content);
		
		for($i = 0; $i < count($check_content[0]); $i++){
			$tmpData = str_replace("[", "", $check_content[0][$i]);
			$tmpData = str_replace("]", "", $tmpData);

			$bData = shortcode_parse_atts($tmpData); 

			if(in_array("NFB_Board", $bData)){
				$this->bname = $bData['bname'];	
				add_action('wp_enqueue_scripts', array($this, 'NFB_BoardStyle'));
				break;
			}
		}
		add_action('wp_enqueue_scripts', array($this, 'NFB_MemberStyle'));

	}

	function NFB_BoardStyle(){
		global $wpdb;
		$skin = get_option("NFB_skin");
		if(!empty($skin)){
			wp_register_style('NFB-Board-css', NFB_WEB.'templates/board/'.$skin.'/css/board.css');
			wp_enqueue_style('NFB-Board-css');
			wp_register_style('NFB-Board-Bootstrap', NFB_WEB.'templates/board/'.$skin.'/css/bootstrap.css');
			wp_enqueue_style('NFB-Board-Bootstrap');
			wp_register_style('NFB-Board-Bootstrap-theme', NFB_WEB.'templates/board/'.$skin.'/css/bootstrap-theme.css');
			wp_enqueue_style('NFB-Board-Bootstrap-theme');
			wp_register_script('NFB-Board-Bootstrap-js', NFB_WEB.'templates/board/'.$skin.'/js/bootstrap.min.js');
			wp_enqueue_script('NFB-Board-Bootstrap-js');
		}
	}

	function NFB_MemberStyle(){
		global $wpdb;
		$skin = get_option("NFB_skin");
		if(!empty($skin)){
			wp_register_style('NFB-Member-Bootstrap', NFB_WEB.'templates/member/'.$skin.'/css/bootstrap.css');
			wp_enqueue_style('NFB-Member-Bootstrap');
			wp_register_style('NFB-Member-Bootstrap-theme', NFB_WEB.'templates/member/'.$skin.'/css/bootstrap-theme.css');
			wp_enqueue_style('NFB-Member-Bootstrap-theme');
			wp_register_script('NFB-Member-Bootstrap-js', NFB_WEB.'templates/member/'.$skin.'/js/bootstrap.min.js');
			wp_enqueue_script('NFB-Member-Bootstrap-js');
		}
	}

	function NFB_Detect_shortcode_seo(){
		global $wpdb, $wp_query;	
		$post = $wp_query->posts;

		$tmpData = str_replace("[", "", $post[0]->post_content);
		$tmpData = str_replace("]", "", $tmpData);
		$bData = shortcode_parse_atts($tmpData); 

		if($bData[0] == 'NFB_Board'){
			$this->bname = $bData['bname'];	
			add_action('wp_head', array($this, 'NFB_Meta_Seo'));
		}    
	}

	function NFB_Meta_Seo(){
		global $wpdb;
		$brdSet = $wpdb->get_row($wpdb->prepare("select * from NFB_board where b_name=%s", $this->bname));
			
		if($brdSet->b_seo_use == 1){
			$output .= "<meta name=\"title\" value=\"".$brdSet->b_seo_title."\" />\n";
			$output .= "<meta itemprop=\"name\" value=\"".$brdSet->b_seo_title."\" />\n";
			$output .= "<meta property=\"og:title\" value=\"".$brdSet->b_seo_title."\" />\n";
			$output .= "<meta name=\"keywords\" content=\"".$brdSet->b_seo_keywords."\" />\n";
			$output .= "<meta name=\"description\" content=\"".$brdSet->b_seo_desc."\" />\n";
			echo $output;
		}
	}

}

//	문자 체크(Ascii) 클래스
class NFB_StringCheck{
	public $str;
	public $len = 0;
	
	public function init($s){
		if(!empty($s)){
			$this->str = trim($s);
			$this->len = strlen($s);
		}
	}
	
	# null 값인지 체크한다 [ 널값이면 : true / 아니면 : false ]
	public function isNull(){
		$result = false;
		$asciiNumber = Ord($this->str);
		if(empty($asciiNumber)) return true;
		return $result;
	}
	

	# 문자와 문자사이 공백이 있는지 체크 [ 공백 있으면 : true / 없으면 : false ]
	public function isSpace(){
		$result = false;
		$str_split	= split("[[:space:]]+",$this->str);
		$count = count($str_split);	
		for($i=0; $i<$count; $i++){
			if($i>0){
				$result = true;
				break;
			}
		}
		return $result;
	}
	
	# 연속적으로 똑같은 문자는 입력할 수 없다  [ 반복문자 max 이상이면 : true / 아니면 : false ]
	# ex : 010-111-1111,010-222-1111 형태제한
	# max = 3; // 반복문자 3개 "초과" 입력제한
	public function isSameRepeatString($max=3){
		$result = false;
		$sameCount = 0;
		$preAsciiNumber = 0;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if( ($preAsciiNumber == $asciiNumber) && ($preAsciiNumber>0) )
				$sameCount += 1;
			else
				$preAsciiNumber = $asciiNumber;
				
			if($sameCount==$max){
				$result = true;
				break;
			}
		}		
		return $result;
	}
	
	# 숫자인지 체크 [ 숫자면 : true / 아니면 : false ]
	# Ascii table = 48 ~ 57
	public function isNumber(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<47 || $asciiNumber>57){
				$result = false;
				break;
			}
		}
		return $result;
	}

	# 영문인지 체크 [ 영문이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90], 소문자[97~122]
	public function isAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
			else{ $result = false; }
		}
		return $result;
	}

	# 영문이 대문자 인지체크 [ 대문자이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90]
	public function isUpAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<65 || $asciiNumber>90){
				$result = false;
				break;
			}
		}
		return $result;
	}

	# 영문이 소문자 인지체크 [ 소문자면 : true / 아니면 : false ]
	# Ascii table = 소문자[97~122]
	public function isLowAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<97 || $asciiNumber>122){
				$result = false;
				break;
			}
		}
		return $result;
	}
	
	# 한글인지 체크한다 [ 한글이면 : true / 아니면 : false ]
	# Ascii table = 128 > 
	public function isKorean(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<128){
				$result = false;
				break;
			}
		}
		return $result;
	}
	
	# 특수문자 입력여부 체크 [ 특수문자 찾으면 : true / 못찾으면 : false ]
	# allow = "-,_"; 허용시킬 
	# space 공백은 자동 제외
	public function isEtcString($allow){
		# 허용된 특수문자 키
		$allowArgs = array();
		$tmpArgs = (!empty($allow)) ? explode(',',$allow) : '';
		if(is_array($tmpArgs)){
			foreach($tmpArgs as $k => $v){
				$knumber = Ord($v);
				$allowArgs['s'.$knumber] = $v;
			}
		}
		
		$result = false;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(array_key_exists('s'.$asciiNumber, $allowArgs) === false){
				if( ($asciiNumber<48) && ($asciiNumber != 32) ){ $result = true; break; }
				else if($asciiNumber>57 && $asciiNumber<65){ $result = true; break; }
				else if($asciiNumber>90 && $asciiNumber<97){ $result = true; break; }
				else if($asciiNumber>122 && $asciiNumber<128){ $result = true; break; }
			}
		}
		return $result;
	}
	
	# 첫번째 문자가 영문인지 체크한다[ 찾으면 : true / 못찾으면 : false ]
	public function isFirstAlphabet(){
		$result = true;
		$asciiNumber = Ord($this->str[0]);
		if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
		else{ $result = false; }
		return $result;
	}
	
	# 문자길이 체크 한글/영문/숫자/특수문자/공백 전부포함
	# min : 최소길이 / max : 최대길이
	public function isStringLength($min,$max){
		$strCount = 0;
		for($i=0;$i<$this->len;$i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<=127 && $asciiNumber>=0){ $strCount++; } 
			else if($asciiNumber<=223 && $asciiNumber>=194){ $strCount++; $i+1; }
			else if($asciiNumber<=239 && $asciiNumber>=224){ $strCount++; $i+2; }
			else if($asciiNumber<=244 && $asciiNumber>=240){ $strCount++; $i+3; }
		}
		
		if($strCount<$min) return false;
		else if($strCount>$max) return false;
		else return true;
	}
	
	# 두 문자가 서로 같은지 비교
	public function equals($s){
		$result = true;
		if(is_string($eStr)){ # 문자인지 체크
			if(strcmp($this->str, $s)) $result= false;
		}else{
			if($this->str != $s ) $result = false;
		}
		return $result;
	}
}

//	암호화 클래스
class NFB_CRYPT {
    var $skey = "_NFB2014_";

    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function decode($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}

//	Mobile Check
class NFB_MobileDetect
{
    /**
     * Mobile detection type.
     *
     * @deprecated since version 2.6.9
     */
    const DETECTION_TYPE_MOBILE     = 'mobile';

    /**
     * Extended detection type.
     *
     * @deprecated since version 2.6.9
     */
    const DETECTION_TYPE_EXTENDED   = 'extended';

    /**
     * A frequently used regular expression to extract version #s.
     *
     * @deprecated since version 2.6.9
     */
    const VER                       = '([\w._\+]+)';

    /**
     * Top-level device.
     */
    const MOBILE_GRADE_A            = 'A';

    /**
     * Mid-level device.
     */
    const MOBILE_GRADE_B            = 'B';

    /**
     * Low-level device.
     */
    const MOBILE_GRADE_C            = 'C';

    /**
     * Stores the version number of the current release.
     */
    const VERSION                   = '2.8.0';

    /**
     * A type for the version() method indicating a string return value.
     */
    const VERSION_TYPE_STRING       = 'text';

    /**
     * A type for the version() method indicating a float return value.
     */
    const VERSION_TYPE_FLOAT        = 'float';

    /**
     * The User-Agent HTTP header is stored in here.
     * @var string
     */
    protected $userAgent = null;

    /**
     * HTTP headers in the PHP-flavor. So HTTP_USER_AGENT and SERVER_SOFTWARE.
     * @var array
     */
    protected $httpHeaders = array();

    /**
     * The detection type, using self::DETECTION_TYPE_MOBILE or self::DETECTION_TYPE_EXTENDED.
     *
     * @deprecated since version 2.6.9
     *
     * @var string
     */
    protected $detectionType = self::DETECTION_TYPE_MOBILE;

    /**
     * HTTP headers that trigger the 'isMobile' detection
     * to be true.
     *
     * @var array
     */
    protected static $mobileHeaders = array(

            'HTTP_ACCEPT'                  => array('matches' => array(
                                                                        // Opera Mini; @reference: http://dev.opera.com/articles/view/opera-binary-markup-language/
                                                                        'application/x-obml2d',
                                                                        // BlackBerry devices.
                                                                        'application/vnd.rim.html',
                                                                        'text/vnd.wap.wml',
                                                                        'application/vnd.wap.xhtml+xml'
                                            )),
            'HTTP_X_WAP_PROFILE'           => null,
            'HTTP_X_WAP_CLIENTID'          => null,
            'HTTP_WAP_CONNECTION'          => null,
            'HTTP_PROFILE'                 => null,
            // Reported by Opera on Nokia devices (eg. C3).
            'HTTP_X_OPERAMINI_PHONE_UA'    => null,
            'HTTP_X_NOKIA_IPADDRESS'       => null,
            'HTTP_X_NOKIA_GATEWAY_ID'      => null,
            'HTTP_X_ORANGE_ID'             => null,
            'HTTP_X_VODAFONE_3GPDPCONTEXT' => null,
            'HTTP_X_HUAWEI_USERID'         => null,
            // Reported by Windows Smartphones.
            'HTTP_UA_OS'                   => null,
            // Reported by Verizon, Vodafone proxy system.
            'HTTP_X_MOBILE_GATEWAY'        => null,
            // Seend this on HTC Sensation. @ref: SensationXE_Beats_Z715e.
            'HTTP_X_ATT_DEVICEID'          => null,
            // Seen this on a HTC.
            'HTTP_UA_CPU'                  => array('matches' => array('ARM')),
    );

    /**
     * List of mobile devices (phones).
     *
     * @var array
     */
    protected static $phoneDevices = array(
        'iPhone'        => '\biPhone.*Mobile|\biPod', // |\biTunes
        'BlackBerry'    => 'BlackBerry|\bBB10\b|rim[0-9]+',
        'HTC'           => 'HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|Android.*\bEVO\b|T-Mobile G1|Z520m',
        'Nexus'         => 'Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile',
        // @todo: Is 'Dell Streak' a tablet or a phone? ;)
        'Dell'          => 'Dell.*Streak|Dell.*Aero|Dell.*Venue|DELL.*Venue Pro|Dell Flash|Dell Smoke|Dell Mini 3iX|XCD28|XCD35|\b001DL\b|\b101DL\b|\bGS01\b',
        'Motorola'      => 'Motorola|DROIDX|DROID BIONIC|\bDroid\b.*Build|Android.*Xoom|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925',
        'Samsung'       => 'Samsung|SGH-I337|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|GT-B3410|GT-B3730|GT-B3740|GT-B5510|GT-B5512|GT-B5722|GT-B6520|GT-B7300|GT-B7320|GT-B7330|GT-B7350|GT-B7510|GT-B7722|GT-B7800|GT-C3010|GT-C3011|GT-C3060|GT-C3200|GT-C3212|GT-C3212I|GT-C3262|GT-C3222|GT-C3300|GT-C3300K|GT-C3303|GT-C3303K|GT-C3310|GT-C3322|GT-C3330|GT-C3350|GT-C3500|GT-C3510|GT-C3530|GT-C3630|GT-C3780|GT-C5010|GT-C5212|GT-C6620|GT-C6625|GT-C6712|GT-E1050|GT-E1070|GT-E1075|GT-E1080|GT-E1081|GT-E1085|GT-E1087|GT-E1100|GT-E1107|GT-E1110|GT-E1120|GT-E1125|GT-E1130|GT-E1160|GT-E1170|GT-E1175|GT-E1180|GT-E1182|GT-E1200|GT-E1210|GT-E1225|GT-E1230|GT-E1390|GT-E2100|GT-E2120|GT-E2121|GT-E2152|GT-E2220|GT-E2222|GT-E2230|GT-E2232|GT-E2250|GT-E2370|GT-E2550|GT-E2652|GT-E3210|GT-E3213|GT-I5500|GT-I5503|GT-I5700|GT-I5800|GT-I5801|GT-I6410|GT-I6420|GT-I7110|GT-I7410|GT-I7500|GT-I8000|GT-I8150|GT-I8160|GT-I8190|GT-I8320|GT-I8330|GT-I8350|GT-I8530|GT-I8700|GT-I8703|GT-I8910|GT-I9000|GT-I9001|GT-I9003|GT-I9010|GT-I9020|GT-I9023|GT-I9070|GT-I9082|GT-I9100|GT-I9103|GT-I9220|GT-I9250|GT-I9300|GT-I9305|GT-I9500|GT-I9505|GT-M3510|GT-M5650|GT-M7500|GT-M7600|GT-M7603|GT-M8800|GT-M8910|GT-N7000|GT-S3110|GT-S3310|GT-S3350|GT-S3353|GT-S3370|GT-S3650|GT-S3653|GT-S3770|GT-S3850|GT-S5210|GT-S5220|GT-S5229|GT-S5230|GT-S5233|GT-S5250|GT-S5253|GT-S5260|GT-S5263|GT-S5270|GT-S5300|GT-S5330|GT-S5350|GT-S5360|GT-S5363|GT-S5369|GT-S5380|GT-S5380D|GT-S5560|GT-S5570|GT-S5600|GT-S5603|GT-S5610|GT-S5620|GT-S5660|GT-S5670|GT-S5690|GT-S5750|GT-S5780|GT-S5830|GT-S5839|GT-S6102|GT-S6500|GT-S7070|GT-S7200|GT-S7220|GT-S7230|GT-S7233|GT-S7250|GT-S7500|GT-S7530|GT-S7550|GT-S7562|GT-S7710|GT-S8000|GT-S8003|GT-S8500|GT-S8530|GT-S8600|SCH-A310|SCH-A530|SCH-A570|SCH-A610|SCH-A630|SCH-A650|SCH-A790|SCH-A795|SCH-A850|SCH-A870|SCH-A890|SCH-A930|SCH-A950|SCH-A970|SCH-A990|SCH-I100|SCH-I110|SCH-I400|SCH-I405|SCH-I500|SCH-I510|SCH-I515|SCH-I600|SCH-I730|SCH-I760|SCH-I770|SCH-I830|SCH-I910|SCH-I920|SCH-I959|SCH-LC11|SCH-N150|SCH-N300|SCH-R100|SCH-R300|SCH-R351|SCH-R400|SCH-R410|SCH-T300|SCH-U310|SCH-U320|SCH-U350|SCH-U360|SCH-U365|SCH-U370|SCH-U380|SCH-U410|SCH-U430|SCH-U450|SCH-U460|SCH-U470|SCH-U490|SCH-U540|SCH-U550|SCH-U620|SCH-U640|SCH-U650|SCH-U660|SCH-U700|SCH-U740|SCH-U750|SCH-U810|SCH-U820|SCH-U900|SCH-U940|SCH-U960|SCS-26UC|SGH-A107|SGH-A117|SGH-A127|SGH-A137|SGH-A157|SGH-A167|SGH-A177|SGH-A187|SGH-A197|SGH-A227|SGH-A237|SGH-A257|SGH-A437|SGH-A517|SGH-A597|SGH-A637|SGH-A657|SGH-A667|SGH-A687|SGH-A697|SGH-A707|SGH-A717|SGH-A727|SGH-A737|SGH-A747|SGH-A767|SGH-A777|SGH-A797|SGH-A817|SGH-A827|SGH-A837|SGH-A847|SGH-A867|SGH-A877|SGH-A887|SGH-A897|SGH-A927|SGH-B100|SGH-B130|SGH-B200|SGH-B220|SGH-C100|SGH-C110|SGH-C120|SGH-C130|SGH-C140|SGH-C160|SGH-C170|SGH-C180|SGH-C200|SGH-C207|SGH-C210|SGH-C225|SGH-C230|SGH-C417|SGH-C450|SGH-D307|SGH-D347|SGH-D357|SGH-D407|SGH-D415|SGH-D780|SGH-D807|SGH-D980|SGH-E105|SGH-E200|SGH-E315|SGH-E316|SGH-E317|SGH-E335|SGH-E590|SGH-E635|SGH-E715|SGH-E890|SGH-F300|SGH-F480|SGH-I200|SGH-I300|SGH-I320|SGH-I550|SGH-I577|SGH-I600|SGH-I607|SGH-I617|SGH-I627|SGH-I637|SGH-I677|SGH-I700|SGH-I717|SGH-I727|SGH-i747M|SGH-I777|SGH-I780|SGH-I827|SGH-I847|SGH-I857|SGH-I896|SGH-I897|SGH-I900|SGH-I907|SGH-I917|SGH-I927|SGH-I937|SGH-I997|SGH-J150|SGH-J200|SGH-L170|SGH-L700|SGH-M110|SGH-M150|SGH-M200|SGH-N105|SGH-N500|SGH-N600|SGH-N620|SGH-N625|SGH-N700|SGH-N710|SGH-P107|SGH-P207|SGH-P300|SGH-P310|SGH-P520|SGH-P735|SGH-P777|SGH-Q105|SGH-R210|SGH-R220|SGH-R225|SGH-S105|SGH-S307|SGH-T109|SGH-T119|SGH-T139|SGH-T209|SGH-T219|SGH-T229|SGH-T239|SGH-T249|SGH-T259|SGH-T309|SGH-T319|SGH-T329|SGH-T339|SGH-T349|SGH-T359|SGH-T369|SGH-T379|SGH-T409|SGH-T429|SGH-T439|SGH-T459|SGH-T469|SGH-T479|SGH-T499|SGH-T509|SGH-T519|SGH-T539|SGH-T559|SGH-T589|SGH-T609|SGH-T619|SGH-T629|SGH-T639|SGH-T659|SGH-T669|SGH-T679|SGH-T709|SGH-T719|SGH-T729|SGH-T739|SGH-T746|SGH-T749|SGH-T759|SGH-T769|SGH-T809|SGH-T819|SGH-T839|SGH-T919|SGH-T929|SGH-T939|SGH-T959|SGH-T989|SGH-U100|SGH-U200|SGH-U800|SGH-V205|SGH-V206|SGH-X100|SGH-X105|SGH-X120|SGH-X140|SGH-X426|SGH-X427|SGH-X475|SGH-X495|SGH-X497|SGH-X507|SGH-X600|SGH-X610|SGH-X620|SGH-X630|SGH-X700|SGH-X820|SGH-X890|SGH-Z130|SGH-Z150|SGH-Z170|SGH-ZX10|SGH-ZX20|SHW-M110|SPH-A120|SPH-A400|SPH-A420|SPH-A460|SPH-A500|SPH-A560|SPH-A600|SPH-A620|SPH-A660|SPH-A700|SPH-A740|SPH-A760|SPH-A790|SPH-A800|SPH-A820|SPH-A840|SPH-A880|SPH-A900|SPH-A940|SPH-A960|SPH-D600|SPH-D700|SPH-D710|SPH-D720|SPH-I300|SPH-I325|SPH-I330|SPH-I350|SPH-I500|SPH-I600|SPH-I700|SPH-L700|SPH-M100|SPH-M220|SPH-M240|SPH-M300|SPH-M305|SPH-M320|SPH-M330|SPH-M350|SPH-M360|SPH-M370|SPH-M380|SPH-M510|SPH-M540|SPH-M550|SPH-M560|SPH-M570|SPH-M580|SPH-M610|SPH-M620|SPH-M630|SPH-M800|SPH-M810|SPH-M850|SPH-M900|SPH-M910|SPH-M920|SPH-M930|SPH-N100|SPH-N200|SPH-N240|SPH-N300|SPH-N400|SPH-Z400|SWC-E100|SCH-i909|GT-N7100|GT-N7105|SCH-I535|SM-N900A|SGH-I317|SGH-T999L|GT-S5360B|GT-I8262|GT-S6802|GT-S6312|GT-S6310|GT-S5312|GT-S5310|GT-I9105|GT-I8510|GT-S6790N|SM-G7105|SM-N9005|GT-S5301|GT-I9295|GT-I9195|SM-C101|GT-S7392|GT-S7560|GT-B7610|GT-I5510|GT-S7582|GT-S7530E',
        'LG'            => '\bLG\b;|LG[- ]?(C800|C900|E400|E610|E900|E-900|F160|F180K|F180L|F180S|730|855|L160|LS840|LS970|LU6200|MS690|MS695|MS770|MS840|MS870|MS910|P500|P700|P705|VM696|AS680|AS695|AX840|C729|E970|GS505|272|C395|E739BK|E960|L55C|L75C|LS696|LS860|P769BK|P350|P500|P509|P870|UN272|US730|VS840|VS950|LN272|LN510|LS670|LS855|LW690|MN270|MN510|P509|P769|P930|UN200|UN270|UN510|UN610|US670|US740|US760|UX265|UX840|VN271|VN530|VS660|VS700|VS740|VS750|VS910|VS920|VS930|VX9200|VX11000|AX840A|LW770|P506|P925|P999|E612|D955|D802)',
        'Sony'          => 'SonyST|SonyLT|SonyEricsson|SonyEricssonLT15iv|LT18i|E10i|LT28h|LT26w|SonyEricssonMT27i',
        'Asus'          => 'Asus.*Galaxy|PadFone.*Mobile',
        // @ref: http://www.micromaxinfo.com/mobiles/smartphones
        // Added because the codes might conflict with Acer Tablets.
        'Micromax'      => 'Micromax.*\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\b',
        'Palm'          => 'PalmSource|Palm', // avantgo|blazer|elaine|hiptop|plucker|xiino ; @todo - complete the regex.
        'Vertu'         => 'Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature', // Just for fun ;)
        // @ref: http://www.pantech.co.kr/en/prod/prodList.do?gbrand=VEGA (PANTECH)
        // Most of the VEGA devices are legacy. PANTECH seem to be newer devices based on Android.
        'Pantech'       => 'PANTECH|IM-A850S|IM-A840S|IM-A830L|IM-A830K|IM-A830S|IM-A820L|IM-A810K|IM-A810S|IM-A800S|IM-T100K|IM-A725L|IM-A780L|IM-A775C|IM-A770K|IM-A760S|IM-A750K|IM-A740S|IM-A730S|IM-A720L|IM-A710K|IM-A690L|IM-A690S|IM-A650S|IM-A630K|IM-A600S|VEGA PTL21|PT003|P8010|ADR910L|P6030|P6020|P9070|P4100|P9060|P5000|CDM8992|TXT8045|ADR8995|IS11PT|P2030|P6010|P8000|PT002|IS06|CDM8999|P9050|PT001|TXT8040|P2020|P9020|P2000|P7040|P7000|C790',
        // @ref: http://www.fly-phone.com/devices/smartphones/ ; Included only smartphones.
        'Fly'           => 'IQ230|IQ444|IQ450|IQ440|IQ442|IQ441|IQ245|IQ256|IQ236|IQ255|IQ235|IQ245|IQ275|IQ240|IQ285|IQ280|IQ270|IQ260|IQ250',
       'iMobile'        => 'i-mobile (IQ|i-STYLE|idea|ZAA|Hitz)',
        // Added simvalley mobile just for fun. They have some interesting devices.
        // @ref: http://www.simvalley.fr/telephonie---gps-_22_telephonie-mobile_telephones_.html
        'SimValley'     => '\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\b',
        // @Tapatalk is a mobile app; @ref: http://support.tapatalk.com/threads/smf-2-0-2-os-and-browser-detection-plugin-and-tapatalk.15565/#post-79039
        'GenericPhone'  => 'Tapatalk|PDA;|SAGEM|\bmmp\b|pocket|\bpsp\b|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|\bwap\b|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser'
    );

    /**
     * List of tablet devices.
     *
     * @var array
     */
    protected static $tabletDevices = array(
        'iPad'              => 'iPad|iPad.*Mobile', // @todo: check for mobile friendly emails topic.
        'NexusTablet'       => '^.*Android.*Nexus(((?:(?!Mobile))|(?:(\s(7|10).+))).)*$',
        'SamsungTablet'     => 'SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000|GT-P1003|GT-P1010|GT-P3105|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P3100|GT-P3108|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7320|GT-P7511|GT-N8000|GT-P8510|SGH-I497|SPH-P500|SGH-T779|SCH-I705|SCH-I915|GT-N8013|GT-P3113|GT-P5113|GT-P8110|GT-N8010|GT-N8005|GT-N8020|GT-P1013|GT-P6201|GT-P7501|GT-N5100|GT-N5110|SHV-E140K|SHV-E140L|SHV-E140S|SHV-E150S|SHV-E230K|SHV-E230L|SHV-E230S|SHW-M180K|SHW-M180L|SHW-M180S|SHW-M180W|SHW-M300W|SHW-M305W|SHW-M380K|SHW-M380S|SHW-M380W|SHW-M430W|SHW-M480K|SHW-M480S|SHW-M480W|SHW-M485W|SHW-M486W|SHW-M500W|GT-I9228|SCH-P739|SCH-I925|GT-I9200|GT-I9205|GT-P5200|GT-P5210|SM-T311|SM-T310|SM-T210|SM-T210R|SM-T211|SM-P600|SM-P601|SM-P605|SM-P900|SM-T217|SM-T217A|SM-T217S|SM-P6000|SM-T3100|SGH-I467|XE500|SM-T110|GT-P5220|GT-I9200X|GT-N5110X|GT-N5120|SM-P905|SM-T111|SM-T2105|SM-T315|SM-T320|SM-T520|SM-T525',
        // @reference: http://www.labnol.org/software/kindle-user-agent-string/20378/
        'Kindle'            => 'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE)\b',
        // Only the Surface tablets with Windows RT are considered mobile.
        // @ref: http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
        'SurfaceTablet'     => 'Windows NT [0-9.]+; ARM;',
        // @ref: http://shopping1.hp.com/is-bin/INTERSHOP.enfinity/WFS/WW-USSMBPublicStore-Site/en_US/-/USD/ViewStandardCatalog-Browse?CatalogCategoryID=JfIQ7EN5lqMAAAEyDcJUDwMT
        'HPTablet'          => 'HP Slate 7|HP ElitePad 900|hp-tablet|EliteBook.*Touch',
        // @note: watch out for PadFone, see #132
        'AsusTablet'        => '^.*PadFone((?!Mobile).)*$|Transformer|TF101|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101|\bK00F\b|TX201LA',
        'BlackBerryTablet'  => 'PlayBook|RIM Tablet',
        'HTCtablet'         => 'HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200',
        'MotorolaTablet'    => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
        'NookTablet'        => 'Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2',
        // @ref: http://www.acer.ro/ac/ro/RO/content/drivers
        // @ref: http://www.packardbell.co.uk/pb/en/GB/content/download (Packard Bell is part of Acer)
        // @ref: http://us.acer.com/ac/en/US/content/group/tablets
        // @note: Can conflict with Micromax and Motorola phones codes.
        'AcerTablet'        => 'Android.*; \b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810)\b|W3-810|\bA3-A10\b',
        // @ref: http://eu.computers.toshiba-europe.com/innovation/family/Tablets/1098744/banner_id/tablet_footerlink/
        // @ref: http://us.toshiba.com/tablets/tablet-finder
        // @ref: http://www.toshiba.co.jp/regza/tablet/
        'ToshibaTablet'     => 'Android.*(AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO',
        // @ref: http://www.nttdocomo.co.jp/english/service/developer/smart_phone/technical_info/spec/index.html
        'LGTablet'          => '\bL-06C|LG-V900|LG-V500|LG-V909\b',
        'FujitsuTablet'     => 'Android.*\b(F-01D|F-05E|F-10D|M532|Q572)\b',
        // Prestigio Tablets http://www.prestigio.com/support
        'PrestigioTablet'   => 'PMP3170B|PMP3270B|PMP3470B|PMP7170B|PMP3370B|PMP3570C|PMP5870C|PMP3670B|PMP5570C|PMP5770D|PMP3970B|PMP3870C|PMP5580C|PMP5880D|PMP5780D|PMP5588C|PMP7280C|PMP7280C3G|PMP7280|PMP7880D|PMP5597D|PMP5597|PMP7100D|PER3464|PER3274|PER3574|PER3884|PER5274|PER5474|PMP5097CPRO|PMP5097|PMP7380D|PMP5297C|PMP5297C_QUAD',
        // @ref: http://support.lenovo.com/en_GB/downloads/default.page?#
        'LenovoTablet'      => 'IdeaTab|S2110|S6000|K3011|A3000|A1000|A2107|A2109|A1107|ThinkPad([ ]+)?Tablet',
        // @ref: http://www.yarvik.com/en/matrix/tablets/
        'YarvikTablet'      => 'Android.*\b(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468|TAB07-100|TAB07-101|TAB07-150|TAB07-151|TAB07-152|TAB07-200|TAB07-201-3G|TAB07-210|TAB07-211|TAB07-212|TAB07-214|TAB07-220|TAB07-400|TAB07-485|TAB08-150|TAB08-200|TAB08-201-3G|TAB08-201-30|TAB09-100|TAB09-211|TAB09-410|TAB10-150|TAB10-201|TAB10-211|TAB10-400|TAB10-410|TAB13-201|TAB274EUK|TAB275EUK|TAB374EUK|TAB462EUK|TAB474EUK|TAB9-200)\b',
        'MedionTablet'      => 'Android.*\bOYO\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB',
        'ArnovaTablet'      => 'AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT',
        // http://www.intenso.de/kategorie_en.php?kategorie=33
        // @todo: http://www.nbhkdz.com/read/b8e64202f92a2df129126bff.html - investigate
        'IntensoTablet'     => 'INM8002KP|INM1010FP|INM805ND|Intenso Tab',
        // IRU.ru Tablets http://www.iru.ru/catalog/soho/planetable/
        'IRUTablet'         => 'M702pro',
        'MegafonTablet'     => 'MegaFon V9|\bZTE V9\b|Android.*\bMT7A\b',
        // @ref: http://www.e-boda.ro/tablete-pc.html
        'EbodaTablet'       => 'E-Boda (Supreme|Impresspeed|Izzycomm|Essential)',
        // @ref: http://www.allview.ro/produse/droseries/lista-tablete-pc/
        'AllViewTablet'           => 'Allview.*(Viva|Alldro|City|Speed|All TV|Frenzy|Quasar|Shine|TX1|AX1|AX2)',
        // @reference: http://wiki.archosfans.com/index.php?title=Main_Page
        'ArchosTablet'      => '\b(101G9|80G9|A101IT)\b|Qilive 97R|ARCHOS 101G10',
        // @ref: http://www.ainol.com/plugin.php?identifier=ainol&module=product
        'AinolTablet'       => 'NOVO7|NOVO8|NOVO10|Novo7Aurora|Novo7Basic|NOVO7PALADIN|novo9-Spark',
        // @todo: inspect http://esupport.sony.com/US/p/select-system.pl?DIRECTOR=DRIVER
        // @ref: Readers http://www.atsuhiro-me.net/ebook/sony-reader/sony-reader-web-browser
        // @ref: http://www.sony.jp/support/tablet/
        'SonyTablet'        => 'Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321|EBRD1101|EBRD1102|EBRD1201',
        // @ref: db + http://www.cube-tablet.com/buy-products.html
        'CubeTablet'        => 'Android.*(K8GT|U9GT|U10GT|U16GT|U17GT|U18GT|U19GT|U20GT|U23GT|U30GT)|CUBE U8GT',
        // @ref: http://www.cobyusa.com/?p=pcat&pcat_id=3001
        'CobyTablet'        => 'MID1042|MID1045|MID1125|MID1126|MID7012|MID7014|MID7015|MID7034|MID7035|MID7036|MID7042|MID7048|MID7127|MID8042|MID8048|MID8127|MID9042|MID9740|MID9742|MID7022|MID7010',
        // @ref: http://www.match.net.cn/products.asp
        'MIDTablet'         => 'M9701|M9000|M9100|M806|M1052|M806|T703|MID701|MID713|MID710|MID727|MID760|MID830|MID728|MID933|MID125|MID810|MID732|MID120|MID930|MID800|MID731|MID900|MID100|MID820|MID735|MID980|MID130|MID833|MID737|MID960|MID135|MID860|MID736|MID140|MID930|MID835|MID733',
        // @ref: http://pdadb.net/index.php?m=pdalist&list=SMiT (NoName Chinese Tablets)
        // @ref: http://www.imp3.net/14/show.php?itemid=20454
        'SMiTTablet'        => 'Android.*(\bMID\b|MID-560|MTV-T1200|MTV-PND531|MTV-P1101|MTV-PND530)',
        // @ref: http://www.rock-chips.com/index.php?do=prod&pid=2
        'RockChipTablet'    => 'Android.*(RK2818|RK2808A|RK2918|RK3066)|RK2738|RK2808A',
        // @ref: http://www.fly-phone.com/devices/tablets/ ; http://www.fly-phone.com/service/
        'FlyTablet'         => 'IQ310|Fly Vision',
        // @ref: http://www.bqreaders.com/gb/tablets-prices-sale.html
        'bqTablet'          => 'bq.*(Elcano|Curie|Edison|Maxwell|Kepler|Pascal|Tesla|Hypatia|Platon|Newton|Livingstone|Cervantes|Avant)|Maxwell.*Lite|Maxwell.*Plus',
        // @ref: http://www.huaweidevice.com/worldwide/productFamily.do?method=index&directoryId=5011&treeId=3290
        // @ref: http://www.huaweidevice.com/worldwide/downloadCenter.do?method=index&directoryId=3372&treeId=0&tb=1&type=software (including legacy tablets)
        'HuaweiTablet'      => 'MediaPad|MediaPad 7 Youth|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim',
        // Nec or Medias Tab
        'NecTablet'         => '\bN-06D|\bN-08D',
        // Pantech Tablets: http://www.pantechusa.com/phones/
        'PantechTablet'     => 'Pantech.*P4100',
        // Broncho Tablets: http://www.broncho.cn/ (hard to find)
        'BronchoTablet'     => 'Broncho.*(N701|N708|N802|a710)',
        // @ref: http://versusuk.com/support.html
        'VersusTablet'      => 'TOUCHPAD.*[78910]|\bTOUCHTAB\b',
        // @ref: http://www.zync.in/index.php/our-products/tablet-phablets
        'ZyncTablet'        => 'z1000|Z99 2G|z99|z930|z999|z990|z909|Z919|z900',
        // @ref: http://www.positivoinformatica.com.br/www/pessoal/tablet-ypy/
        'PositivoTablet'    => 'TB07STA|TB10STA|TB07FTA|TB10FTA',
        // @ref: https://www.nabitablet.com/
        'NabiTablet'        => 'Android.*\bNabi',
        'KoboTablet'        => 'Kobo Touch|\bK080\b|\bVox\b Build|\bArc\b Build',
        // French Danew Tablets http://www.danew.com/produits-tablette.php
        'DanewTablet'       => 'DSlide.*\b(700|701R|702|703R|704|802|970|971|972|973|974|1010|1012)\b',
        // Texet Tablets and Readers http://www.texet.ru/tablet/
        'TexetTablet'       => 'NaviPad|TB-772A|TM-7045|TM-7055|TM-9750|TM-7016|TM-7024|TM-7026|TM-7041|TM-7043|TM-7047|TM-8041|TM-9741|TM-9747|TM-9748|TM-9751|TM-7022|TM-7021|TM-7020|TM-7011|TM-7010|TM-7023|TM-7025|TM-7037W|TM-7038W|TM-7027W|TM-9720|TM-9725|TM-9737W|TM-1020|TM-9738W|TM-9740|TM-9743W|TB-807A|TB-771A|TB-727A|TB-725A|TB-719A|TB-823A|TB-805A|TB-723A|TB-715A|TB-707A|TB-705A|TB-709A|TB-711A|TB-890HD|TB-880HD|TB-790HD|TB-780HD|TB-770HD|TB-721HD|TB-710HD|TB-434HD|TB-860HD|TB-840HD|TB-760HD|TB-750HD|TB-740HD|TB-730HD|TB-722HD|TB-720HD|TB-700HD|TB-500HD|TB-470HD|TB-431HD|TB-430HD|TB-506|TB-504|TB-446|TB-436|TB-416|TB-146SE|TB-126SE',
        // @note: Avoid detecting 'PLAYSTATION 3' as mobile.
        'PlaystationTablet' => 'Playstation.*(Portable|Vita)',
        // @ref: http://www.trekstor.de/surftabs.html
        'TrekstorTablet'    => 'ST10416-1|VT10416-1|ST70408-1|ST702xx-1|ST702xx-2|ST80208|ST97216|ST70104-2',
        // @ref: http://www.pyleaudio.com/Products.aspx?%2fproducts%2fPersonal-Electronics%2fTablets
        'PyleAudioTablet'   => '\b(PTBL10CEU|PTBL10C|PTBL72BC|PTBL72BCEU|PTBL7CEU|PTBL7C|PTBL92BC|PTBL92BCEU|PTBL9CEU|PTBL9CUK|PTBL9C)\b',
        // @ref: http://www.advandigital.com/index.php?link=content-product&jns=JP001
        // @Note: because of the short codenames we have to include whitespaces to reduce the possible conflicts.
        'AdvanTablet'       => 'Android.* \b(E3A|T3X|T5C|T5B|T3E|T3C|T3B|T1J|T1F|T2A|T1H|T1i|E1C|T1-E|T5-A|T4|E1-B|T2Ci|T1-B|T1-D|O1-A|E1-A|T1-A|T3A|T4i)\b ',
        // @ref: http://www.danytech.com/category/tablet-pc
        'DanyTechTablet' => 'Genius Tab G3|Genius Tab S2|Genius Tab Q3|Genius Tab G4|Genius Tab Q4|Genius Tab G-II|Genius TAB GII|Genius TAB GIII|Genius Tab S1',
        // @ref: http://www.galapad.net/product.html
        'GalapadTablet'     => 'Android.*\bG1\b',
        // @ref: http://www.micromaxinfo.com/tablet/funbook
        'MicromaxTablet'    => 'Funbook|Micromax.*\b(P250|P560|P360|P362|P600|P300|P350|P500|P275)\b',
        // http://www.karbonnmobiles.com/products_tablet.php
        'KarbonnTablet'     => 'Android.*\b(A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2)\b',
        // @ref: http://www.myallfine.com/Products.asp
        'AllFineTablet'     => 'Fine7 Genius|Fine7 Shine|Fine7 Air|Fine8 Style|Fine9 More|Fine10 Joy|Fine11 Wide',
        // @ref: http://www.proscanvideo.com/products-search.asp?itemClass=TABLET&itemnmbr=
        'PROSCANTablet'     => '\b(PEM63|PLT1023G|PLT1041|PLT1044|PLT1044G|PLT1091|PLT4311|PLT4311PL|PLT4315|PLT7030|PLT7033|PLT7033D|PLT7035|PLT7035D|PLT7044K|PLT7045K|PLT7045KB|PLT7071KG|PLT7072|PLT7223G|PLT7225G|PLT7777G|PLT7810K|PLT7849G|PLT7851G|PLT7852G|PLT8015|PLT8031|PLT8034|PLT8036|PLT8080K|PLT8082|PLT8088|PLT8223G|PLT8234G|PLT8235G|PLT8816K|PLT9011|PLT9045K|PLT9233G|PLT9735|PLT9760G|PLT9770G)\b',
        // @ref: http://www.yonesnav.com/products/products.php
        'YONESTablet' => 'BQ1078|BC1003|BC1077|RK9702|BC9730|BC9001|IT9001|BC7008|BC7010|BC708|BC728|BC7012|BC7030|BC7027|BC7026',
        // @ref: http://www.cjshowroom.com/eproducts.aspx?classcode=004001001
        // China manufacturer makes tablets for different small brands (eg. http://www.zeepad.net/index.html)
        'ChangJiaTablet'    => 'TPC7102|TPC7103|TPC7105|TPC7106|TPC7107|TPC7201|TPC7203|TPC7205|TPC7210|TPC7708|TPC7709|TPC7712|TPC7110|TPC8101|TPC8103|TPC8105|TPC8106|TPC8203|TPC8205|TPC8503|TPC9106|TPC9701|TPC97101|TPC97103|TPC97105|TPC97106|TPC97111|TPC97113|TPC97203|TPC97603|TPC97809|TPC97205|TPC10101|TPC10103|TPC10106|TPC10111|TPC10203|TPC10205|TPC10503',
        // @ref: http://www.gloryunion.cn/products.asp
        // @ref: http://www.allwinnertech.com/en/apply/mobile.html
        // @ref: http://www.ptcl.com.pk/pd_content.php?pd_id=284 (EVOTAB)
        // @todo: Softwiner tablets?
        // aka. Cute or Cool tablets. Not sure yet, must research to avoid collisions.
        'GUTablet'          => 'TX-A1301|TX-M9002|Q702|kf026', // A12R|D75A|D77|D79|R83|A95|A106C|R15|A75|A76|D71|D72|R71|R73|R77|D82|R85|D92|A97|D92|R91|A10F|A77F|W71F|A78F|W78F|W81F|A97F|W91F|W97F|R16G|C72|C73E|K72|K73|R96G
        // @ref: http://www.pointofview-online.com/showroom.php?shop_mode=product_listing&category_id=118
        'PointOfViewTablet' => 'TAB-P506|TAB-navi-7-3G-M|TAB-P517|TAB-P-527|TAB-P701|TAB-P703|TAB-P721|TAB-P731N|TAB-P741|TAB-P825|TAB-P905|TAB-P925|TAB-PR945|TAB-PL1015|TAB-P1025|TAB-PI1045|TAB-P1325|TAB-PROTAB[0-9]+|TAB-PROTAB25|TAB-PROTAB26|TAB-PROTAB27|TAB-PROTAB26XL|TAB-PROTAB2-IPS9|TAB-PROTAB30-IPS9|TAB-PROTAB25XXL|TAB-PROTAB26-IPS10|TAB-PROTAB30-IPS10',
        // @ref: http://www.overmax.pl/pl/katalog-produktow,p8/tablety,c14/
        // @todo: add more tests.
        'OvermaxTablet'     => 'OV-(SteelCore|NewBase|Basecore|Baseone|Exellen|Quattor|EduTab|Solution|ACTION|BasicTab|TeddyTab|MagicTab|Stream|TB-08|TB-09)',
        // @ref: http://hclmetablet.com/India/index.php
        'HCLTablet'         => 'HCL.*Tablet|Connect-3G-2.0|Connect-2G-2.0|ME Tablet U1|ME Tablet U2|ME Tablet G1|ME Tablet X1|ME Tablet Y2|ME Tablet Sync',
        // @ref: http://www.edigital.hu/Tablet_es_e-book_olvaso/Tablet-c18385.html
        'DPSTablet'         => 'DPS Dream 9|DPS Dual 7',
        // @ref: http://www.visture.com/index.asp
        'VistureTablet'     => 'V97 HD|i75 3G|Visture V4( HD)?|Visture V5( HD)?|Visture V10',
        // @ref: http://www.mijncresta.nl/tablet
        'CrestaTablet'     => 'CTP(-)?810|CTP(-)?818|CTP(-)?828|CTP(-)?838|CTP(-)?888|CTP(-)?978|CTP(-)?980|CTP(-)?987|CTP(-)?988|CTP(-)?989',
        // MediaTek - http://www.mediatek.com/_en/01_products/02_proSys.php?cata_sn=1&cata1_sn=1&cata2_sn=309
        'MediatekTablet' => '\bMT8125|MT8389|MT8135|MT8377\b',
        // Concorde tab
        'ConcordeTablet' => 'Concorde([ ]+)?Tab|ConCorde ReadMan',
        // GoClever Tablets - http://www.goclever.com/uk/products,c1/tablet,c5/
        'GoCleverTablet' => 'GOCLEVER TAB|A7GOCLEVER|M1042|M7841|M742|R1042BK|R1041|TAB A975|TAB A7842|TAB A741|TAB A741L|TAB M723G|TAB M721|TAB A1021|TAB I921|TAB R721|TAB I720|TAB T76|TAB R70|TAB R76.2|TAB R106|TAB R83.2|TAB M813G|TAB I721|GCTA722|TAB I70|TAB I71|TAB S73|TAB R73|TAB R74|TAB R93|TAB R75|TAB R76.1|TAB A73|TAB A93|TAB A93.2|TAB T72|TAB R83|TAB R974|TAB R973|TAB A101|TAB A103|TAB A104|TAB A104.2|R105BK|M713G|A972BK|TAB A971|TAB R974.2|TAB R104|TAB R83.3|TAB A1042',
        // Modecom Tablets - http://www.modecom.eu/tablets/portal/
        'ModecomTablet' => 'FreeTAB 9000|FreeTAB 7.4|FreeTAB 7004|FreeTAB 7800|FreeTAB 2096|FreeTAB 7.5|FreeTAB 1014|FreeTAB 1001 |FreeTAB 8001|FreeTAB 9706|FreeTAB 9702|FreeTAB 7003|FreeTAB 7002|FreeTAB 1002|FreeTAB 7801|FreeTAB 1331|FreeTAB 1004|FreeTAB 8002|FreeTAB 8014|FreeTAB 9704|FreeTAB 1003',
        // Vonino Tablets - http://www.vonino.eu/tablets
        'VoninoTablet'  => '\b(Argus[ _]?S|Diamond[ _]?79HD|Emerald[ _]?78E|Luna[ _]?70C|Onyx[ _]?S|Onyx[ _]?Z|Orin[ _]?HD|Orin[ _]?S|Otis[ _]?S|SpeedStar[ _]?S|Magnet[ _]?M9|Primus[ _]?94[ _]?3G|Primus[ _]?94HD|Primus[ _]?QS|Android.*\bQ8\b|Sirius[ _]?EVO[ _]?QS|Sirius[ _]?QS|Spirit[ _]?S)\b',
        // ECS Tablets - http://www.ecs.com.tw/ECSWebSite/Product/Product_Tablet_List.aspx?CategoryID=14&MenuID=107&childid=M_107&LanID=0
        'ECSTablet'     => 'V07OT2|TM105A|S10OT1|TR10CS1',
        // Storex Tablets - http://storex.fr/espace_client/support.html
        // @note: no need to add all the tablet codes since they are guided by the first regex.
        'StorexTablet'  => 'eZee[_\']?(Tab|Go)[0-9]+|TabLC7|Looney Tunes Tab',
        // Generic Vodafone tablets.
        'VodafoneTablet' => 'SmartTab([ ]+)?[0-9]+|SmartTabII10',
        // French tablets - Essentiel B http://www.boulanger.fr/tablette_tactile_e-book/tablette_tactile_essentiel_b/cl_68908.htm?multiChoiceToDelete=brand&mc_brand=essentielb
        // Aka: http://www.essentielb.fr/
        'EssentielBTablet' => 'Smart[ \']?TAB[ ]+?[0-9]+|Family[ \']?TAB2',
        // Ross & Moor - http://ross-moor.ru/
        'RossMoorTablet' => 'RM-790|RM-997|RMD-878G|RMD-974R|RMT-705A|RMT-701|RME-601|RMT-501|RMT-711',
        // i-mobile http://product.i-mobilephone.com/Mobile_Device
        'iMobileTablet'        => 'i-mobile i-note',
        // @ref: http://www.tolino.de/de/vergleichen/
        'TolinoTablet'  => 'tolino tab [0-9.]+|tolino shine',
        // AudioSonic - a Kmart brand
        // http://www.kmart.com.au/webapp/wcs/stores/servlet/Search?langId=-1&storeId=10701&catalogId=10001&categoryId=193001&pageSize=72&currentPage=1&searchCategory=193001%2b4294965664&sortBy=p_MaxPrice%7c1
        'AudioSonicTablet' => '\bC-22Q|T7-QC|T-17B|T-17P\b',
        // AMPE Tablets - http://www.ampe.com.my/product-category/tablets/
        // @todo: add them gradually to avoid conflicts.
        'AMPETablet' => 'Android.* A78 ',
        // Skk Mobile - http://skkmobile.com.ph/product_tablets.php
        'SkkTablet' => 'Android.* (SKYPAD|PHOENIX|CYCLOPS)',
        // Tecno Mobile (only tablet) - http://www.tecno-mobile.com/index.php/product?filterby=smart&list_order=all&page=1
        'TecnoTablet' => 'TECNO P9',
        // JXD (consoles & tablets) - http://jxd.hk/products.asp?selectclassid=009008&clsid=3
        'JXDTablet' => 'Android.*\b(F3000|A3300|JXD5000|JXD3000|JXD2000|JXD300B|JXD300|S5800|S7800|S602b|S5110b|S7300|S5300|S602|S603|S5100|S5110|S601|S7100a|P3000F|P3000s|P101|P200s|P1000m|P200m|P9100|P1000s|S6600b|S908|P1000|P300|S18|S6600|S9100)\b',
        // i-Joy tablets - http://www.i-joy.es/en/cat/products/tablets/
        'iJoyTablet' => 'Tablet (Spirit 7|Essentia|Galatea|Fusion|Onix 7|Landa|Titan|Scooby|Deox|Stella|Themis|Argon|Unique 7|Sygnus|Hexen|Finity 7|Cream|Cream X2|Jade|Neon 7|Neron 7|Kandy|Scape|Saphyr 7|Rebel|Biox|Rebel|Rebel 8GB|Myst|Draco 7|Myst|Tab7-004|Myst|Tadeo Jones|Tablet Boing|Arrow|Draco Dual Cam|Aurix|Mint|Amity|Revolution|Finity 9|Neon 9|T9w|Amity 4GB Dual Cam|Stone 4GB|Stone 8GB|Andromeda|Silken|X2|Andromeda II|Halley|Flame|Saphyr 9,7|Touch 8|Planet|Triton|Unique 10|Hexen 10|Memphis 4GB|Memphis 8GB|Onix 10)',
        // @ref: http://www.tesco.com/direct/hudl/
        'Hudl'              => 'Hudl HT7S3',
        // @ref: http://www.telstra.com.au/home-phone/thub-2/
        'TelstraTablet'     => 'T-Hub2',
        'GenericTablet'     => 'Android.*\b97D\b|Tablet(?!.*PC)|ViewPad7|BNTV250A|MID-WCDMA|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|rk30sdk|\bEVOTAB\b|M758A|ET904|ALUMIUM10|Smartfren Tab',
    );

    /**
     * List of mobile Operating Systems.
     *
     * @var array
     */
    protected static $operatingSystems = array(
        'AndroidOS'         => 'Android',
        'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
        'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
        'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        // @reference: http://en.wikipedia.org/wiki/Windows_Mobile
        'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
        // @reference: http://en.wikipedia.org/wiki/Windows_Phone
        // http://wifeng.cn/?r=blog&a=view&id=106
        // http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
        'WindowsPhoneOS'   => 'Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7',
        'iOS'               => '\biPhone.*Mobile|\biPod|\biPad',
        // http://en.wikipedia.org/wiki/MeeGo
        // @todo: research MeeGo in UAs
        'MeeGoOS'           => 'MeeGo',
        // http://en.wikipedia.org/wiki/Maemo
        // @todo: research Maemo in UAs
        'MaemoOS'           => 'Maemo',
        'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
        'webOS'             => 'webOS|hpwOS',
        'badaOS'            => '\bBada\b',
        'BREWOS'            => 'BREW',
    );

    /**
     * List of mobile User Agents.
     *
     * @var array
     */
    protected static $browsers = array(
        // @reference: https://developers.google.com/chrome/mobile/docs/user-agent
        'Chrome'          => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
        'Dolfin'          => '\bDolfin\b',
        'Opera'           => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
        'Skyfire'         => 'Skyfire',
        'IE'              => 'IEMobile|MSIEMobile', // |Trident/[.0-9]+
        'Firefox'         => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile',
        'Bolt'            => 'bolt',
        'TeaShark'        => 'teashark',
        'Blazer'          => 'Blazer',
        // @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
        'Safari'          => 'Version.*Mobile.*Safari|Safari.*Mobile',
        // @ref: http://en.wikipedia.org/wiki/Midori_(web_browser)
        //'Midori'          => 'midori',
        'Tizen'           => 'Tizen',
        'UCBrowser'       => 'UC.*Browser|UCWEB',
        // @ref: https://github.com/serbanghita/Mobile-Detect/issues/7
        'DiigoBrowser'    => 'DiigoBrowser',
        // http://www.puffinbrowser.com/index.php
        'Puffin'            => 'Puffin',
        // @ref: http://mercury-browser.com/index.html
        'Mercury'          => '\bMercury\b',
        // @reference: http://en.wikipedia.org/wiki/Minimo
        // http://en.wikipedia.org/wiki/Vision_Mobile_Browser
        'GenericBrowser'  => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger'
    );

    /**
     * Utilities.
     *
     * @var array
     */
    protected static $utilities = array(
        // Experimental. When a mobile device wants to switch to 'Desktop Mode'.
        // @ref: http://scottcate.com/technology/windows-phone-8-ie10-desktop-or-mobile/
        // @ref: https://github.com/serbanghita/Mobile-Detect/issues/57#issuecomment-15024011
        'DesktopMode' => 'WPDesktop',
        'TV'          => 'SonyDTV|HbbTV', // experimental
        'WebKit'      => '(webkit)[ /]([\w.]+)',
        'Bot'         => 'Googlebot|DoCoMo|YandexBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|facebookexternalhit',
        'MobileBot'   => 'Googlebot-Mobile|DoCoMo|YahooSeeker/M1A1-R2D2',
        // @todo: Include JXD consoles.
        'Console'     => '\b(Nintendo|Nintendo WiiU|PLAYSTATION|Xbox)\b',
        'Watch'       => 'SM-V700',
    );

    /**
     * All possible HTTP headers that represent the
     * User-Agent string.
     *
     * @var array
     */
    protected static $uaHttpHeaders = array(
        // The default User-Agent string.
        'HTTP_USER_AGENT',
        // Header can occur on devices using Opera Mini.
        'HTTP_X_OPERAMINI_PHONE_UA',
        // Vodafone specific header: http://www.seoprinciple.com/mobile-web-community-still-angry-at-vodafone/24/
        'HTTP_X_DEVICE_USER_AGENT',
        'HTTP_X_ORIGINAL_USER_AGENT',
        'HTTP_X_SKYFIRE_PHONE',
        'HTTP_X_BOLT_PHONE_UA',
        'HTTP_DEVICE_STOCK_UA',
        'HTTP_X_UCBROWSER_DEVICE_UA'
    );

    /**
     * The individual segments that could exist in a User-Agent string. VER refers to the regular
     * expression defined in the constant self::VER.
     *
     * @var array
     */
    protected static $properties = array(

        // Build
        'Mobile'        => 'Mobile/[VER]',
        'Build'         => 'Build/[VER]',
        'Version'       => 'Version/[VER]',
        'VendorID'      => 'VendorID/[VER]',

        // Devices
        'iPad'          => 'iPad.*CPU[a-z ]+[VER]',
        'iPhone'        => 'iPhone.*CPU[a-z ]+[VER]',
        'iPod'          => 'iPod.*CPU[a-z ]+[VER]',
        //'BlackBerry'    => array('BlackBerry[VER]', 'BlackBerry [VER];'),
        'Kindle'        => 'Kindle/[VER]',

        // Browser
        'Chrome'        => array('Chrome/[VER]', 'CriOS/[VER]', 'CrMo/[VER]'),
        'Coast'         => array('Coast/[VER]'),
        'Dolfin'        => 'Dolfin/[VER]',
        // @reference: https://developer.mozilla.org/en-US/docs/User_Agent_Strings_Reference
        'Firefox'       => 'Firefox/[VER]',
        'Fennec'        => 'Fennec/[VER]',
        // @reference: http://msdn.microsoft.com/en-us/library/ms537503(v=vs.85).aspx
        'IE'      => array('IEMobile/[VER];', 'IEMobile [VER]', 'MSIE [VER];'),
        // http://en.wikipedia.org/wiki/NetFront
        'NetFront'      => 'NetFront/[VER]',
        'NokiaBrowser'  => 'NokiaBrowser/[VER]',
        'Opera'         => array( ' OPR/[VER]', 'Opera Mini/[VER]', 'Version/[VER]' ),
        'Opera Mini'    => 'Opera Mini/[VER]',
        'Opera Mobi'    => 'Version/[VER]',
        'UC Browser'    => 'UC Browser[VER]',
        'MQQBrowser'    => 'MQQBrowser/[VER]',
        'MicroMessenger' => 'MicroMessenger/[VER]',
        // @note: Safari 7534.48.3 is actually Version 5.1.
        // @note: On BlackBerry the Version is overwriten by the OS.
        'Safari'        => array( 'Version/[VER]', 'Safari/[VER]' ),
        'Skyfire'       => 'Skyfire/[VER]',
        'Tizen'         => 'Tizen/[VER]',
        'Webkit'        => 'webkit[ /][VER]',

        // Engine
        'Gecko'         => 'Gecko/[VER]',
        'Trident'       => 'Trident/[VER]',
        'Presto'        => 'Presto/[VER]',

        // OS
        'iOS'              => ' \bOS\b [VER] ',
        'Android'          => 'Android [VER]',
        'BlackBerry'       => array('BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'),
        'BREW'             => 'BREW [VER]',
        'Java'             => 'Java/[VER]',
        // @reference: http://windowsteamblog.com/windows_phone/b/wpdev/archive/2011/08/29/introducing-the-ie9-on-windows-phone-mango-user-agent-string.aspx
        // @reference: http://en.wikipedia.org/wiki/Windows_NT#Releases
        'Windows Phone OS' => array( 'Windows Phone OS [VER]', 'Windows Phone [VER]'),
        'Windows Phone'    => 'Windows Phone [VER]',
        'Windows CE'       => 'Windows CE/[VER]',
        // http://social.msdn.microsoft.com/Forums/en-US/windowsdeveloperpreviewgeneral/thread/6be392da-4d2f-41b4-8354-8dcee20c85cd
        'Windows NT'       => 'Windows NT [VER]',
        'Symbian'          => array('SymbianOS/[VER]', 'Symbian/[VER]'),
        'webOS'            => array('webOS/[VER]', 'hpwOS/[VER];'),
    );

    /**
     * Construct an instance of this class.
     *
     * @param array $headers Specify the headers as injection. Should be PHP _SERVER flavored.
     *                       If left empty, will use the global _SERVER['HTTP_*'] vars instead.
     * @param string $userAgent Inject the User-Agent header. If null, will use HTTP_USER_AGENT
     *                          from the $headers array instead.
     */
    public function __construct(
        array $headers = null,
        $userAgent = null
    ){
        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    /**
    * Get the current script version.
    * This is useful for the demo.php file,
    * so people can check on what version they are testing
    * for mobile devices.
    *
    * @return string The version number in semantic version format.
    */
    public static function getScriptVersion()
    {
        return self::VERSION;
    }

    /**
     * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
     *
     * @param array $httpHeaders The headers to set. If null, then using PHP's _SERVER to extract
     *                           the headers. The default null is left for backwards compatibilty.
     */
    public function setHttpHeaders($httpHeaders = null)
    {
        //use global _SERVER if $httpHeaders aren't defined
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        //clear existing headers
        $this->httpHeaders = array();

        //Only save HTTP headers. In PHP land, that means only _SERVER vars that
        //start with HTTP_.
        foreach ($httpHeaders as $key => $value) {
            if (substr($key,0,5) == 'HTTP_') {
                $this->httpHeaders[$key] = $value;
            }
        }
    }

    /**
     * Retrieves the HTTP headers.
     *
     * @return array
     */
    public function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    /**
     * Retrieves a particular header. If it doesn't exist, no exception/error is caused.
     * Simply null is returned.
     *
     * @param string $header The name of the header to retrieve. Can be HTTP compliant such as
     *                       "User-Agent" or "X-Device-User-Agent" or can be php-esque with the
     *                       all-caps, HTTP_ prefixed, underscore seperated awesomeness.
     *
     * @return string|null The value of the header.
     */
    public function getHttpHeader($header)
    {
        //are we using PHP-flavored headers?
        if (strpos($header, '_') === false) {
            $header = str_replace('-', '_', $header);
            $header = strtoupper($header);
        }

        //test the alternate, too
        $altHeader = 'HTTP_' . $header;

        //Test both the regular and the HTTP_ prefix
        if (isset($this->httpHeaders[$header])) {
            return $this->httpHeaders[$header];
        } elseif (isset($this->httpHeaders[$altHeader])) {
            return $this->httpHeaders[$altHeader];
        }
    }

    public function getMobileHeaders()
    {
        return self::$mobileHeaders;
    }

    /**
     * Get all possible HTTP headers that
     * can contain the User-Agent string.
     *
     * @return array List of HTTP headers.
     */
    public function getUaHttpHeaders()
    {
        return self::$uaHttpHeaders;
    }

    /**
     * Set the User-Agent to be used.
     *
     * @param string $userAgent The user agent string to set.
     */
    public function setUserAgent($userAgent = null)
    {
        if (!empty($userAgent)) {
            return $this->userAgent = $userAgent;
        } else {

            $this->userAgent = null;

            foreach($this->getUaHttpHeaders() as $altHeader){
                if(!empty($this->httpHeaders[$altHeader])){ // @todo: should use getHttpHeader(), but it would be slow. (Serban)
                    $this->userAgent .= $this->httpHeaders[$altHeader] . " ";
                }
            }

            return $this->userAgent = (!empty($this->userAgent) ? trim($this->userAgent) : null);

        }
    }

    /**
     * Retrieve the User-Agent.
     *
     * @return string|null The user agent if it's set.
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set the detection type. Must be one of self::DETECTION_TYPE_MOBILE or
     * self::DETECTION_TYPE_EXTENDED. Otherwise, nothing is set.
     *
     * @deprecated since version 2.6.9
     *
     * @param string $type The type. Must be a self::DETECTION_TYPE_* constant. The default
     *                     parameter is null which will default to self::DETECTION_TYPE_MOBILE.
     */
    public function setDetectionType($type = null)
    {
        if ($type === null) {
            $type = self::DETECTION_TYPE_MOBILE;
        }

        if ($type != self::DETECTION_TYPE_MOBILE && $type != self::DETECTION_TYPE_EXTENDED) {
            return;
        }

        $this->detectionType = $type;
    }

    /**
     * Retrieve the list of known phone devices.
     *
     * @return array List of phone devices.
     */
    public static function getPhoneDevices()
    {
        return self::$phoneDevices;
    }

    /**
     * Retrieve the list of known tablet devices.
     *
     * @return array List of tablet devices.
     */
    public static function getTabletDevices()
    {
        return self::$tabletDevices;
    }

    /**
     * Alias for getBrowsers() method.
     *
     * @return array List of user agents.
     */
    public static function getUserAgents()
    {
        return self::getBrowsers();
    }

    /**
     * Retrieve the list of known browsers. Specifically, the user agents.
     *
     * @return array List of browsers / user agents.
     */
    public static function getBrowsers()
    {
        return self::$browsers;
    }

    /**
     * Retrieve the list of known utilities.
     *
     * @return array List of utilities.
     */
    public static function getUtilities()
    {
        return self::$utilities;
    }

    /**
     * Method gets the mobile detection rules. This method is used for the magic methods $detect->is*().
     *
     * @deprecated since version 2.6.9
     *
     * @return array All the rules (but not extended).
     */
    public static function getMobileDetectionRules()
    {
        static $rules;

        if (!$rules) {
            $rules = array_merge(
                self::$phoneDevices,
                self::$tabletDevices,
                self::$operatingSystems,
                self::$browsers
            );
        }

        return $rules;

    }

    /**
     * Method gets the mobile detection rules + utilities.
     * The reason this is separate is because utilities rules
     * don't necessary imply mobile. This method is used inside
     * the new $detect->is('stuff') method.
     *
     * @deprecated since version 2.6.9
     *
     * @return array All the rules + extended.
     */
    public function getMobileDetectionRulesExtended()
    {
        static $rules;

        if (!$rules) {
            // Merge all rules together.
            $rules = array_merge(
                self::$phoneDevices,
                self::$tabletDevices,
                self::$operatingSystems,
                self::$browsers,
                self::$utilities
            );
        }

        return $rules;
    }

    /**
     * Retrieve the current set of rules.
     *
     * @deprecated since version 2.6.9
     *
     * @return array
     */
    public function getRules()
    {
        if ($this->detectionType == self::DETECTION_TYPE_EXTENDED) {
            return self::getMobileDetectionRulesExtended();
        } else {
            return self::getMobileDetectionRules();
        }
    }

    /**
     * Retrieve the list of mobile operating systems.
     *
     * @return array The list of mobile operating systems.
     */
    public static function getOperatingSystems()
    {
        return self::$operatingSystems;
    }

    /**
    * Check the HTTP headers for signs of mobile.
    * This is the fastest mobile check possible; it's used
    * inside isMobile() method.
    *
    * @return bool
    */
    public function checkHttpHeadersForMobile()
    {

        foreach($this->getMobileHeaders() as $mobileHeader => $matchType){
            if( isset($this->httpHeaders[$mobileHeader]) ){
                if( is_array($matchType['matches']) ){
                    foreach($matchType['matches'] as $_match){
                        if( strpos($this->httpHeaders[$mobileHeader], $_match) !== false ){
                            return true;
                        }
                    }
                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Magic overloading method.
     *
     * @method boolean is[...]()
     * @param  string                 $name
     * @param  array                  $arguments
     * @return mixed
     * @throws BadMethodCallException when the method doesn't exist and doesn't start with 'is'
     */
    public function __call($name, $arguments)
    {
        //make sure the name starts with 'is', otherwise
        if (substr($name, 0, 2) != 'is') {
            throw new BadMethodCallException("No such method exists: $name");
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        $key = substr($name, 2);

        return $this->matchUAAgainstKey($key);
    }

    /**
    * Find a detection rule that matches the current User-agent.
    *
    * @param null $userAgent deprecated
    * @return boolean
    */
    protected function matchDetectionRulesAgainstUA($userAgent = null)
    {
        // Begin general search.
        foreach ($this->getRules() as $_regex) {
            if (empty($_regex)) {
                continue;
            }
            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
    * Search for a certain key in the rules array.
    * If the key is found the try to match the corresponding
    * regex agains the User-Agent.
    *
    * @param string $key
    * @param null $userAgent deprecated
    * @return mixed
    */
    protected function matchUAAgainstKey($key, $userAgent = null)
    {
        // Make the keys lowercase so we can match: isIphone(), isiPhone(), isiphone(), etc.
        $key = strtolower($key);

        //change the keys to lower case
        $_rules = array_change_key_case($this->getRules());

        if (array_key_exists($key, $_rules)) {
            if (empty($_rules[$key])) {
                return null;
            }

            return $this->match($_rules[$key], $userAgent);
        }

        return false;
    }

    /**
    * Check if the device is mobile.
    * Returns true if any type of mobile device detected, including special ones
    * @param null $userAgent deprecated
    * @param null $httpHeaders deprecated
    * @return bool
    */
    public function isMobile($userAgent = null, $httpHeaders = null)
    {

        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }

        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        if ($this->checkHttpHeadersForMobile()) {
            return true;
        } else {
            return $this->matchDetectionRulesAgainstUA();
        }

    }

    /**
     * Check if the device is a tablet.
     * Return true if any type of tablet device is detected.
     *
     * @param  string $userAgent   deprecated
     * @param  array  $httpHeaders deprecated
     * @return bool
     */
    public function isTablet($userAgent = null, $httpHeaders = null)
    {
        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        foreach (self::$tabletDevices as $_regex) {
            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method checks for a certain property in the
     * userAgent.
     * @todo: The httpHeaders part is not yet used.
     *
     * @param $key
     * @param  string        $userAgent   deprecated
     * @param  string        $httpHeaders deprecated
     * @return bool|int|null
     */
    public function is($key, $userAgent = null, $httpHeaders = null)
    {
        // Set the UA and HTTP headers only if needed (eg. batch mode).
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }

        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        $this->setDetectionType(self::DETECTION_TYPE_EXTENDED);

        return $this->matchUAAgainstKey($key);
    }

    /**
     * Some detection rules are relative (not standard),
     * because of the diversity of devices, vendors and
     * their conventions in representing the User-Agent or
     * the HTTP headers.
     *
     * This method will be used to check custom regexes against
     * the User-Agent string.
     *
     * @param $regex
     * @param  string $userAgent
     * @return bool
     *
     * @todo: search in the HTTP headers too.
     */
    public function match($regex, $userAgent = null)
    {
        // Escape the special character which is the delimiter.
        $regex = str_replace('/', '\/', $regex);

        return (bool) preg_match('/'.$regex.'/is', (!empty($userAgent) ? $userAgent : $this->userAgent));
    }

    /**
     * Get the properties array.
     *
     * @return array
     */
    public static function getProperties()
    {
        return self::$properties;
    }

    /**
     * Prepare the version number.
     *
     * @todo Remove the error supression from str_replace() call.
     *
     * @param string $ver The string version, like "2.6.21.2152";
     *
     * @return float
     */
    public function prepareVersionNo($ver)
    {
        $ver = str_replace(array('_', ' ', '/'), '.', $ver);
        $arrVer = explode('.', $ver, 2);

        if (isset($arrVer[1])) {
            $arrVer[1] = @str_replace('.', '', $arrVer[1]); // @todo: treat strings versions.
        }

        return (float) implode('.', $arrVer);
    }

    /**
     * Check the version of the given property in the User-Agent.
     * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
     *
     * @param string $propertyName The name of the property. See self::getProperties() array
     *                              keys for all possible properties.
     * @param string $type Either self::VERSION_TYPE_STRING to get a string value or
     *                      self::VERSION_TYPE_FLOAT indicating a float value. This parameter
     *                      is optional and defaults to self::VERSION_TYPE_STRING. Passing an
     *                      invalid parameter will default to the this type as well.
     *
     * @return string|float The version of the property we are trying to extract.
     */
    public function version($propertyName, $type = self::VERSION_TYPE_STRING)
    {
        if (empty($propertyName)) {
            return false;
        }

        //set the $type to the default if we don't recognize the type
        if ($type != self::VERSION_TYPE_STRING && $type != self::VERSION_TYPE_FLOAT) {
            $type = self::VERSION_TYPE_STRING;
        }

        $properties = self::getProperties();

        // Check if the property exists in the properties array.
        if (array_key_exists($propertyName, $properties)) {

            // Prepare the pattern to be matched.
            // Make sure we always deal with an array (string is converted).
            $properties[$propertyName] = (array) $properties[$propertyName];

            foreach ($properties[$propertyName] as $propertyMatchString) {

                $propertyPattern = str_replace('[VER]', self::VER, $propertyMatchString);

                // Escape the special character which is the delimiter.
                $propertyPattern = str_replace('/', '\/', $propertyPattern);

                // Identify and extract the version.
                preg_match('/'.$propertyPattern.'/is', $this->userAgent, $match);

                if (!empty($match[1])) {
                    $version = ( $type == self::VERSION_TYPE_FLOAT ? $this->prepareVersionNo($match[1]) : $match[1] );

                    return $version;
                }

            }

        }

        return false;
    }

    /**
     * Retrieve the mobile grading, using self::MOBILE_GRADE_* constants.
     *
     * @return string One of the self::MOBILE_GRADE_* constants.
     */
    public function mobileGrade()
    {
        $isMobile = $this->isMobile();

        if (
            // Apple iOS 3.2-5.1 - Tested on the original iPad (4.3 / 5.0), iPad 2 (4.3), iPad 3 (5.1), original iPhone (3.1), iPhone 3 (3.2), 3GS (4.3), 4 (4.3 / 5.0), and 4S (5.1)
            $this->version('iPad', self::VERSION_TYPE_FLOAT)>=4.3 ||
            $this->version('iPhone', self::VERSION_TYPE_FLOAT)>=3.1 ||
            $this->version('iPod', self::VERSION_TYPE_FLOAT)>=3.1 ||

            // Android 2.1-2.3 - Tested on the HTC Incredible (2.2), original Droid (2.2), HTC Aria (2.1), Google Nexus S (2.3). Functional on 1.5 & 1.6 but performance may be sluggish, tested on Google G1 (1.5)
            // Android 3.1 (Honeycomb)  - Tested on the Samsung Galaxy Tab 10.1 and Motorola XOOM
            // Android 4.0 (ICS)  - Tested on a Galaxy Nexus. Note: transition performance can be poor on upgraded devices
            // Android 4.1 (Jelly Bean)  - Tested on a Galaxy Nexus and Galaxy 7
            ( $this->version('Android', self::VERSION_TYPE_FLOAT)>2.1 && $this->is('Webkit') ) ||

            // Windows Phone 7-7.5 - Tested on the HTC Surround (7.0) HTC Trophy (7.5), LG-E900 (7.5), Nokia Lumia 800
            $this->version('Windows Phone OS', self::VERSION_TYPE_FLOAT)>=7.0 ||

            // Blackberry 7 - Tested on BlackBerry® Torch 9810
            // Blackberry 6.0 - Tested on the Torch 9800 and Style 9670
            $this->is('BlackBerry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)>=6.0 ||
            // Blackberry Playbook (1.0-2.0) - Tested on PlayBook
            $this->match('Playbook.*Tablet') ||

            // Palm WebOS (1.4-2.0) - Tested on the Palm Pixi (1.4), Pre (1.4), Pre 2 (2.0)
            ( $this->version('webOS', self::VERSION_TYPE_FLOAT)>=1.4 && $this->match('Palm|Pre|Pixi') ) ||
            // Palm WebOS 3.0  - Tested on HP TouchPad
            $this->match('hp.*TouchPad') ||

            // Firefox Mobile (12 Beta) - Tested on Android 2.3 device
            ( $this->is('Firefox') && $this->version('Firefox', self::VERSION_TYPE_FLOAT)>=12 ) ||

            // Chrome for Android - Tested on Android 4.0, 4.1 device
            ( $this->is('Chrome') && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT)>=4.0 ) ||

            // Skyfire 4.1 - Tested on Android 2.3 device
            ( $this->is('Skyfire') && $this->version('Skyfire', self::VERSION_TYPE_FLOAT)>=4.1 && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 ) ||

            // Opera Mobile 11.5-12: Tested on Android 2.3
            ( $this->is('Opera') && $this->version('Opera Mobi', self::VERSION_TYPE_FLOAT)>11 && $this->is('AndroidOS') ) ||

            // Meego 1.2 - Tested on Nokia 950 and N9
            $this->is('MeeGoOS') ||

            // Tizen (pre-release) - Tested on early hardware
            $this->is('Tizen') ||

            // Samsung Bada 2.0 - Tested on a Samsung Wave 3, Dolphin browser
            // @todo: more tests here!
            $this->is('Dolfin') && $this->version('Bada', self::VERSION_TYPE_FLOAT)>=2.0 ||

            // UC Browser - Tested on Android 2.3 device
            ( ($this->is('UC Browser') || $this->is('Dolfin')) && $this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 ) ||

            // Kindle 3 and Fire  - Tested on the built-in WebKit browser for each
            ( $this->match('Kindle Fire') ||
            $this->is('Kindle') && $this->version('Kindle', self::VERSION_TYPE_FLOAT)>=3.0 ) ||

            // Nook Color 1.4.1 - Tested on original Nook Color, not Nook Tablet
            $this->is('AndroidOS') && $this->is('NookTablet') ||

            // Chrome Desktop 11-21 - Tested on OS X 10.7 and Windows 7
            $this->version('Chrome', self::VERSION_TYPE_FLOAT)>=11 && !$isMobile ||

            // Safari Desktop 4-5 - Tested on OS X 10.7 and Windows 7
            $this->version('Safari', self::VERSION_TYPE_FLOAT)>=5.0 && !$isMobile ||

            // Firefox Desktop 4-13 - Tested on OS X 10.7 and Windows 7
            $this->version('Firefox', self::VERSION_TYPE_FLOAT)>=4.0 && !$isMobile ||

            // Internet Explorer 7-9 - Tested on Windows XP, Vista and 7
            $this->version('MSIE', self::VERSION_TYPE_FLOAT)>=7.0 && !$isMobile ||

            // Opera Desktop 10-12 - Tested on OS X 10.7 and Windows 7
            // @reference: http://my.opera.com/community/openweb/idopera/
            $this->version('Opera', self::VERSION_TYPE_FLOAT)>=10 && !$isMobile

        ){
            return self::MOBILE_GRADE_A;
        }

        if (
            $this->version('iPad', self::VERSION_TYPE_FLOAT)<4.3 ||
            $this->version('iPhone', self::VERSION_TYPE_FLOAT)<3.1 ||
            $this->version('iPod', self::VERSION_TYPE_FLOAT)<3.1 ||

            // Blackberry 5.0: Tested on the Storm 2 9550, Bold 9770
            $this->is('Blackberry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)>=5 && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)<6 ||

            //Opera Mini (5.0-6.5) - Tested on iOS 3.2/4.3 and Android 2.3
            ( $this->version('Opera Mini', self::VERSION_TYPE_FLOAT)>=5.0 && $this->version('Opera Mini', self::VERSION_TYPE_FLOAT)<=6.5 &&
            ($this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 || $this->is('iOS')) ) ||

            // Nokia Symbian^3 - Tested on Nokia N8 (Symbian^3), C7 (Symbian^3), also works on N97 (Symbian^1)
            $this->match('NokiaN8|NokiaC7|N97.*Series60|Symbian/3') ||

            // @todo: report this (tested on Nokia N71)
            $this->version('Opera Mobi', self::VERSION_TYPE_FLOAT)>=11 && $this->is('SymbianOS')
        ){
            return self::MOBILE_GRADE_B;
        }

        if (
            // Blackberry 4.x - Tested on the Curve 8330
            $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)<5.0 ||
            // Windows Mobile - Tested on the HTC Leo (WinMo 5.2)
            $this->match('MSIEMobile|Windows CE.*Mobile') || $this->version('Windows Mobile', self::VERSION_TYPE_FLOAT)<=5.2

        ){
            return self::MOBILE_GRADE_C;
        }

        //All older smartphone platforms and featurephones - Any device that doesn't support media queries
        //will receive the basic, C grade experience.
        return self::MOBILE_GRADE_C;
    }
}
?>