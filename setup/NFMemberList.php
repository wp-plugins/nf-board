<?php
$per_page = (empty($_REQUEST['per_page']))?10:$_REQUEST['per_page'];
$paged = (empty($_REQUEST['paged']))?1:intval($_REQUEST['paged']);
$start_pos = ($paged - 1) * $per_page;
$total = $NFB_Member->getCount($s);

$total_pages = ceil($total / $per_page);
$add_args = array("page"=>$_GET['page'], "per_page"=>$per_page, "s"=>$s);

$result = $NFB_Member->getList($orderby, $order, $s, $start_pos, $per_page);
?>
<script type='text/javascript' src='<?php echo NFB_WEB?>inc/js/admin-member.js'></script>
<div class="wrap">

	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>회원 목록</h2>

	<form name="joinForm" id="joinForm" method="post">
	<input type="hidden" name="delNo" id="delNo" value="">
	<input type="hidden" name="skinname" id="skinname" value="<?php echo get_option("NFB_skin")?>">
	<ul class='subsubsub'>
		<li class='all'><a href="admin.php?page=NFMemberList"><span class="displaying-num" style="font-weight:bold;">모두 <span>(<?php echo $total?>)</span></a></li>
	</ul>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="tBatch1" id="tBatch1">
				<option value="-1">일괄 작업</option>
				<option value="remove">선택 삭제</option>
			</select>
			<input type="button" name="btn_batch" id="doaction" class="button-secondary action" value="적용" onclick="batchAction(1);">
		</div>
		<?php
		if(!empty($_GET['order'])){
			switch($_GET['order']){
				case "asc": $order = "desc";break;
				case "desc": $order = "asc";break;
				default: $order = "asc";break;
			}
		}else $order = "asc";

		if(!empty($_GET['orderby'])){
			switch($_GET['orderby']){
				case "user_id": 
					$sorted1 = "sorted";$sorted2 = "sortable";$sorted3 = "sortable";break;
				case "name": 
					$sorted1 = "sortable";$sorted2 = "sorted";$sorted3 = "sortable";break;
				case "reg_date":
					$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sorted";break;
			}
		
		}else{ 
			$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sortable";
		}
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">회원 검색:</label>
			<input type="search" id="user-search-input" name="s" value="<?php if(!empty($s)) echo $s?>" />
			<input type="submit" name="" id="search-submit" class="button-secondary" value="회원 검색"  />
		</p>
	</div>

	<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
	<colgroup>
		<col width="50px" style="padding-left:10px"><col width=""><col width=""><col width=""><col width="">
	</colgroup>
	<thead>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" align="center"><input type="checkbox"></th>
		<th scope="col" width="150" class="manage-column <?php echo $sorted1?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=user_id&#038;order=<?php echo $order?>"><span>아이디</span><span class="sorting-indicator"></span></a></th>
		<th scope="col" width="150" class="manage-column <?php echo $sorted2?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=name&#038;order=<?php echo $order?>"><span>이름</span><span class="sorting-indicator"></span></a></th>
		<th scope="col" class="manage-column">이메일</th>
		<th scope="col" width="160" class="manage-column <?php echo $sorted3?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>가입일</span><span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
		<th scope="col" class="manage-column <?php echo $sorted1?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=user_id&#038;order=<?php echo $order?>"><span>아이디</span><span class="sorting-indicator"></span></a></th>
		<th scope="col" class="manage-column <?php echo $sorted2?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=name&#038;order=<?php echo $order?>"><span>이름</span><span class="sorting-indicator"></span></a></th>
		<th scope="col" class="manage-column">이메일</th>
		<th scope="col" class="manage-column <?php echo $sorted3?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=NFMemberList&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>가입일</span><span class="sorting-indicator"></span></a></th>
	</tr>
	</tfoot>
	<tbody id="the-list">
	<?php
	if($total <= 0){
	?>
	<tr valign="middle">
		<td colspan="5" align="center">등록된 회원이 없습니다.</td>
	</tr>
	<?php
	}else{
		foreach($result as $i => $data){
			if($i % 2 == 0) $tr_css = "class=\"alternate\"";
			else $tr_css = "";
	?>
	<tr <?php echo $tr_css?> valign="top">
		<td scope="row" class="check-column" style="padding-left:10px;"><input type="checkbox" name="check[]" id="check[]" value="<?=$data->uno?>"></td>
		<td>
			<strong><a href="admin.php?page=NFMemberList&uno=<?=$data->uno?>" title="회원정보 편집하기"><?php echo stripslashes($data->user_id)?></a></strong>
			<div class="row-actions"><a href="admin.php?page=NFMemberList&uno=<?=$data->uno?>" title="회원정보 편집하기">편집</a> | <a class='' title='이 회원을 삭제' href="javascript:memberDelete('<?=$data->uno?>');">삭제</a></div>	
		</td>
		<td><?php echo stripslashes($data->name)?></td>
		<td><?php echo $data->email?></td>
		<td><?php echo date("Y-m-d H:i:s", $data->reg_date)?></td>
	</tr>
	<?php 
		}
	}
	?>
	</tbody>
	</table>

	<div class="tablenav bottom">
		<div class="alignleft actions">
			<select name="tBatch2" id="tBatch2">
				<option value="-1">일괄 작업</option>
				<option value="remove">선택 삭제</option>
			</select>
			<input type="button" name="btn_batch" id="doaction" class="button-secondary action" value="적용" onclick="batchAction(2);">
		</div>
	</div>
	</form>
	<?php echo NFB_PageNavi($paged, $total_pages, $add_args)?>
</div>