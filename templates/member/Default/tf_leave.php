<div id="NFBoard_Content">
	<div class="alert alert-danger center-block" style="display:none;" role="alert" id="result_msg"></div>
	<form id="leaveForm" name="leaveForm" class="form-horizontal" role="form" style="max-width:95%;">
		<input type="hidden" id="moveURL" name="moveURL" value="<?php if(!empty($_GET['moveURL'])) echo $_GET['moveURL']; else echo NFB_SITE_URL?>" />
		<div class="form-group">
			<label for="uid" class="col-sm-2 control-label">비밀번호</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="pass" name="pass" placeholder="Password" tabindex="1">
			</div>
		</div>
		<div class="form-group">
			<label for="upass" class="col-sm-2 control-label">비밀번호 확인</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="repass" name="repass" placeholder="Password repeat" tabindex="2">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="button" class="btn btn-primary btn-lg leave-btn">&nbsp;&nbsp;&nbsp;회원탈퇴&nbsp;&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</div>