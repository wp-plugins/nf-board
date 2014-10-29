<div id="NFBoard_Content">
	<div class="panel panel-info center-block" style="max-width:500px;">
		<div class="panel-heading">
			<h3 class="panel-title">회원탈퇴 결과</h3>
		</div>
		<div class="panel-body text-center">
		<?php if($resultType=="ok"){?>
			<h3>회원탈퇴가 완료되었습니다.</h3><br/>
			<h4>아이디 : <strong><?php echo $user_id;?></strong></h4><br/>
			<a href="<?php echo str_replace("https", "http", NFB_SITE_URL);?>" class="btn btn-default" role="button">확인</a>
		<?php }else if($resultType=="mismatch"){?>
			<h4>회원 비밀번호가 일치하지 않습니다.</h4><br/>
			<a href="<?php echo str_replace("https", "http", get_permalink(get_option('NFB_leave_page')));?>" class="btn btn-default" role="button">다시 입력</a>
		<?php }else{?>
			<h4>일반회원이 아닙니다.</h4><br/>
			<a href="<?php echo str_replace("https", "http", NFB_SITE_URL);?>" class="btn btn-default" role="button">확인</a>
		<?php }?>
		</div>
	</div>
</div>