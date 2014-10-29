<div id="NFBoard_Content">
	<div class="panel panel-info center-block" style="max-width:500px;">
		<div class="panel-heading text-center">
			<h3 class="panel-title"><strong><?php echo $current_user->user_login;?></strong>회원님 반갑습니다.</h3>
		</div>
		<div class="panel-body text-center">
			<a href="<?php echo wp_logout_url(home_url());?>" class="btn btn-default" role="button">로그아웃</a>
		</div>
	</div>
</div>