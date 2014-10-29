<div id="NFBoard_Content">
	<div class="alert alert-danger center-block" style="display:none;" role="alert" id="result_msg"></div>
	<form id="idfindForm" name="idfindForm" class="form-horizontal" role="form" style="max-width:95%;">
		<input type="hidden" id="moveURL" name="moveURL" value="<?php if(!empty($_GET['moveURL'])) echo $_GET['moveURL']; else echo NFB_SITE_URL?>" />
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">이메일</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email" name="email" placeholder="E-mail" tabindex="1">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="button" class="btn btn-primary btn-lg id-find-btn">&nbsp;&nbsp;&nbsp;&nbsp;아이디 찾기&nbsp;&nbsp;&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</div>