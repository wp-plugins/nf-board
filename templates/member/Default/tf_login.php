<div id="NFBoard_Content">
	<div class="alert alert-danger center-block" style="display:none;" role="alert" id="result_msg"></div>
	<form id="loginForm" name="loginForm" class="form-horizontal" role="form" style="max-width:95%;">
		<input type="hidden" id="moveURL" name="moveURL" value="<?php if(!empty($_GET['moveURL'])) echo $_GET['moveURL']; else echo NFB_SITE_URL?>" />
		<div class="form-group">
			<label for="uid" class="col-sm-2 control-label">아이디</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="uid" name="uid" placeholder="User ID" tabindex="1">
			</div>
		</div>
		<div class="form-group">
			<label for="upass" class="col-sm-2 control-label">비밀번호</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="upass" name="upass" placeholder="Password" tabindex="2">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="button" class="btn btn-primary btn-lg login-btn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;로그인&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
				<p style="padding-top:10px;">
				<?php if(get_option('NFB_join_page')){?><a href="<?php echo get_permalink(get_option('NFB_join_page'))?>" class="btn btn-link" role="button">회원가입</a><?php }?>
				<?php if(get_option('NFB_id_find_page')){?><a href="<?php echo get_permalink(get_option('NFB_id_find_page'))?>" class="btn btn-link" role="button">아이디찾기</a><?php }?>
				<?php if(get_option('NFB_pw_find_page')){?><a href="<?php echo get_permalink(get_option('NFB_pw_find_page'))?>" class="btn btn-link" role="button">비밀번호찾기</a><?php }?>
				</p>
			</div>
		</div>
	</form>
</div>