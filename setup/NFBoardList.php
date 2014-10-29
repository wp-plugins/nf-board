<?php
$per_page = (empty($_REQUEST['per_page']))?10:$_REQUEST['per_page'];
$paged = (empty($_REQUEST['paged']))?1:intval($_REQUEST['paged']);
$start_pos = ($paged - 1) * $per_page;
$total = $NFB_Board->LoadBoardCount();
$total_pages = ceil($total / $per_page);
$add_args = array("page"=>$_GET['page'], "per_page"=>$per_page);

$result = $NFB_Board->LoadBoardList($start_pos, $per_page);	
?>
<script type='text/javascript' src='<?php echo NFB_WEB?>inc/js/admin-board.js'></script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>보드 목록 <input type="submit" class="button-primary" onclick="location.href='admin.php?page=NFBoardAdd';" value="보드 추가하기"></h2>

	<form name="boardList" id="boardList" method="post">
	<input type="hidden" name="delNo" id="delNo" value="">	
	<input type="hidden" name="NFB_HOME_URL" id="NFB_HOME_URL" value="<?php echo NFB_HOME_URL?>" />
	<ul class='subsubsub'>
		<li class='all'><a href="admin.php?page=NFBoardList"><span class="displaying-num" style="font-weight:bold;">모두 <span>(<?php echo $total?>)</span></a></li>
	</ul>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="tBatch1" id="tBatch1">
				<option value="-1">일괄 작업</option>
				<option value="remove">선택 삭제</option>
			</select>
			<input type="button" name="btn_batch" id="doaction" class="button-secondary action" value="적용" onclick="batchAction(1);">
		</div>

		<div class='tablenav-pages one-page'>
			<select id='per_page' name='per_page' onchange="this.form.submit();">
				<option value='10' <?php echo ($per_page=="10")?"selected":""?>>10개출력</option>
				<option value='20' <?php echo ($per_page=="20")?"selected":""?>>20개출력</option>
				<option value='30' <?php echo ($per_page=="30")?"selected":""?>>30개출력</option>
			</select>		
		</div>
		<br class="clear" />
	</div>
	<table class="wp-list-table widefat fixed posts" cellspacing="0" border="0">
		<colgroup>
			<col width="50px" style="padding-left:10px"><col width=""><col width=""><col width=""><col width=""><col width="">
		</colgroup>
		<thead>
		<tr>
			<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
			<th scope="col" width="150" class="manage-column sortable asc"><a><span>보드명</span></a></th>
			<th scope="col" width="" class="manage-column">Shortcode</th>
			<th scope="col" width="110" class="manage-column">스킨</th>
			<th scope="col" width="150" class="manage-column">권한</th>
			<th scope="col" width="140" class="manage-column">등록일자</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
			<th scope="col" width="150" class="manage-column sortable asc"><a><span>보드명</span></a></th>
			<th scope="col" width="" class="manage-column">Shortcode</th>
			<th scope="col" width="110" class="manage-column">스킨</th>
			<th scope="col" width="150" class="manage-column">권한</th>
			<th scope="col" width="140" class="manage-column">등록일자</th>
		</tr>
		</tfoot>
		<tbody id="the-list">
		<?php
		if($total <= 0){
		?>
		<tr valign="middle">
			<td colspan="6" align="center">생성된 보드가 없습니다.</td>
		</tr>
		<?php
		}else{
			foreach($result as $i => $data){
				if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
				else $alternate_class = "";
		?>
		<tr <?php echo $alternate_class?> valign="middle">
			<td scope="row" class="check-column" style="padding-left:10px;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->b_no?>"></td>
			<td>
				<a class="row-title" href="admin.php?page=NFBoardAdd&b_no=<?php echo $data->b_no?>" title="수정"><?php echo $data->b_name?></a>
				<div class="row-actions"><span class='edit'><a href="admin.php?page=NFBoardAdd&b_no=<?php echo $data->b_no?>" title="이 보드 편집하기">편집</a> | </span><span class='trash'><a class='submitdelete' title='이 보드을 삭제' href="javascript:boardDelete('<?php echo $data->b_no?>');">삭제</a></span></div>
			</td>
			<td>
				<span style="padding-left:13px;">보드</span> <input type="text" value="[NFB_Board bname=<?php echo $data->b_name?>]" style="width:80%;background-color:#ffffff;" readonly><br>
				<span>최근글</span> <input type="text" value="[NFB_Latest bname=<?php echo $data->b_name?>]" style="width:80%;background-color:#ffffff;" readonly>
			</td>
			<td><?php echo $data->b_skin?></td>
			<td>
				읽기 : <?php if($data->b_read_lv == 'administrator') echo "관리자"; else if($data->b_read_lv == 'author') echo "회원"; else if($data->b_read_lv == 'all') echo "비회원";?><br/>
				쓰기 : <?php if($data->b_write_lv == 'administrator') echo "관리자"; else if($data->b_write_lv == 'author') echo "회원"; else if($data->b_write_lv == 'all') echo "비회원";?><br/>
				댓글 : <?php if($data->b_comment_lv == 'administrator') echo "관리자"; else if($data->b_comment_lv == 'author') echo "회원"; else if($data->b_comment_lv == 'all') echo "비회원";?>
			</td>
			<td><abbr title="<?php echo date("Y-m-d",$data->b_regdate)?>"><?php echo date("Y-m-d", $data->b_regdate)?></abbr></td>
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