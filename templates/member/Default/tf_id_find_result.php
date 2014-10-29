<div id="NFBoard_Content">
	<div class="panel panel-info center-block" style="max-width:500px;">
		<div class="panel-heading">
			<h3 class="panel-title">아이디 찾기 결과</h3>
		</div>
		<div class="panel-body text-center">
		<?php if($resultType=="ok"){?>
			<h4>아이디 : <strong><?php echo $user_id;?></strong></h4><br/>
			<a href="<?php echo str_replace("https", "http", get_permalink(get_option('NFB_login_page')));?>" class="btn btn-default" role="button">로그인</a>
		<?php }else{?>
			<h4>가입되지 않은 이메일입니다.</h4><br/>
			<a href="<?php echo str_replace("https", "http", get_permalink(get_option('NFB_id_find_page')));?>" class="btn btn-default" role="button">다시 찾기</a>
		<?php }?>
		</div>
	</div>
</div>