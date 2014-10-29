<div id="NFBoard_Content">
	<div class="panel panel-info center-block" style="max-width:500px;">
		<div class="panel-heading">
			<h3 class="panel-title">비밀번호 찾기 결과</h3>
		</div>
		<div class="panel-body text-center">
		<?php if($resultType=="ok"){?>
			<h4>이메일 : <strong><?php echo $rows->email;?></strong></h4><br/>
			이메일로 임시비밀번호를 발송해드렸습니다.<br />로그인 후 비밀번호를 재설정하세요.<br/><br/>
			<a href="<?php echo str_replace("https", "http", get_permalink(get_option('NFB_pw_find_page')));?>" class="btn btn-default" role="button">확인</a>
		<?php }else{?>
			<h4>일치하는 회원정보가 없습니다.</h4><br/>
			<a href="<?php echo str_replace("https", "http", get_permalink(get_option('NFB_pw_find_page')));?>" class="btn btn-default" role="button">다시 찾기</a>
		<?php }?>
		</div>
	</div>
</div>