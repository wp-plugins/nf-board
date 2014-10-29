<?php
@session_start();
global $wpdb;
$config = $wpdb->get_row("select * from NFB_setup");
if($_POST['zipKind'] == "dong" && !empty($_POST['keyword1'])) $_POST['keyword'] = $_POST['keyword1'];
if(($_POST['zipKind'] == "road" || $_POST['zipKind'] == "post") && !empty($_POST['keyword2'])) $_POST['keyword'] = $_POST['keyword2'];
$zipList = array();
if(!empty($_POST['keyword'])){
	if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)){
		$urlString = 'serviceKey='.$config->zipcode_api_key.'&searchSe='.$_POST['zipKind'].'&srchwrd='.urlencode($_POST['keyword']);
		$requestURL = 'http://openapi.epost.go.kr/postal/retrieveNewAdressService/retrieveNewAdressService/getNewAddressList';
		$url = parse_url($requestURL);
		$host = $url['host'];
		$path = $url['path'];
		$out = "GET {$path}?{$urlString}";
		$fp = @fsockopen($host, 80);
		if(!is_resource($fp)){
			$resultMsg = '검색중 오류가 발생하였습니다. <br/>API Key를 확인해주세요';
		}else{
			$out .= " HTTP/1.1\r\n";
			$out .= "HOST: {$host}\r\n";
			$out .= "Connection:close\r\n\r\n";
			fwrite($fp, $out);
			$httpResponse = '';
			while(!feof($fp)){$httpResponse .= fgets($fp, 51200);}
			fclose($fp);
			preg_match_all("/<successYN>(.*)<\/successYN>/iU", $httpResponse, $searchResult);
			$searchResult = $searchResult[1][0];

			if($searchResult != 'Y'){$resultMsg = '검색된 결과가 없습니다.';}
			else{
				preg_match_all("/<zipNo>(.+?)<\/zipNo>/i", $httpResponse, $resultZip);
				$resultZip = $resultZip[1];
				preg_match_all("/<lnmAdres>(.+?)<\/lnmAdres>/i", $httpResponse, $resultAddress1);
				$resultAddress1 = $resultAddress1[1];
				preg_match_all("/<rnAdres>(.+?)<\/rnAdres>/i", $httpResponse, $resultAddress2);
				$resultAddress2 = $resultAddress2[1];
				$resultMsg = '';
				for($i = 0; $i < sizeof($resultZip); $i++){
					$zipList[] = '
					<tr>
						<td class="vcenter" width="80">
							<h4><span class="label label-default vcenter">'.$resultZip[$i].'</span></h4>
						</td>
						<td>
							<a href="javascript:;" onclick="openerInput(\''.$resultZip[$i].'\', \''.$resultAddress1[$i].'\')">
								<h6>'.$resultAddress2[$i].'<br />'.$resultAddress1[$i].'</h6>
							</a>
						</td>
					</tr>';
				}
			}
			unset($fp, $domain, $port, $error, $errstr, $serviceKey, $urlString, $url, $httpResponse, $searchResult, $resultZip, $resultAddress1, $resultAddress2);
		}
	
	}else{
		$zipData = file("./zipcode");
		while(list($key, $val) = each($zipData)){
			$varray = explode("|", $val);
			$string = $varray[4].$varray[5];
			if(ereg("(".$_POST['keyword'].")", $string)) $zip[$key] = $val;
		}
		$i = 0;
		if(sizeof($zip) > 0){
			$resultMsg = '';
			while(list(, $value) = each($zip)){
				$ziparray = explode("|", $value);
				$address[$i] = $ziparray[2]." ".$ziparray[3]." ".$ziparray[4];
				if(ereg("~", trim($ziparray[5]))){$addr4[$i] = "";}
				else{$addr4[$i] = trim($ziparray[5]);}
				$view_addre[$i] = trim($ziparray[5]);
				$zipcode1[$i] = substr($ziparray[1], 0, 3);
				$zipcode2[$i] = substr($ziparray[1], 4, 3);
				$zipList[] = '
				<tr>
					<td width="80">
						<h4><span class="label label-default">'.$zipcode1[$i].'-'.$zipcode2[$i].'</span></h4>
					</td>
					<td>
						<a href="javascript:;" onclick="openerInput(\''.$zipcode1[$i].'-'.$zipcode2[$i].'\', \''.$address[$i].' '.$view_addre[$i].'\')">
							<h6>'.$address[$i].' '.$view_addre[$i].'</h6>
						</a>
					</td>
				</tr>';
			}
			sort($zipList);
		
		}else{
			$resultMsg = '검색된 결과가 없습니다.';
		}
	}
}
$zipKind = $_POST['zipKind'];
if(!$zipKind) $zipKind = 'dong';
if($zipKind == "dong") $currentTab = 1;
else $currentTab = 2;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko-KR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='viewport' content='width=device-width' />
<title>우편번호 검색</title>
<link rel='stylesheet' href='<?php echo NFB_WEB?>/templates/member/<?php echo $config->skinname?>/css/bootstrap.css' type='text/css'/>
<link rel='stylesheet' href='<?php echo NFB_WEB?>/templates/member/<?php echo $config->skinname?>/css/bootstrap-theme.css' type='text/css'/>
<script type="text/javascript" src="<?php echo includes_url()?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?php echo NFB_WEB?>/templates/member/<?php echo $config->skinname?>/js/bootstrap.js"></script>
<script type="text/javascript">
var now_tab = 1;
function zipSubmit(){
	var type = jQuery("#zipKind").val();
	var msg_tit = "";
	if(type == "dong") msg_tit = "동과 번지";
	else if(type == "road") msg_tit = "도로명과 건물번호";
	else if(type == "post") msg_tit = "우편번호";

	if(type == "dong"){
		if(jQuery("#keyword1").val() == ""){
			jQuery("#errMsg1").html(msg_tit + "를 입력해주세요.").show();
			jQuery("#keyword1").focus();
			return false;
		}
	}else{
		if(jQuery("#keyword2").val() == ""){
			jQuery("#errMsg2").html(msg_tit + "를 입력해주세요.").show();
			jQuery("#keyword2").focus();
			return false;
		}
	}
	jQuery("#zipForm").submit();
}
function tabSelect(num){
	jQuery("#tab_" + now_tab).hide();
	jQuery("#tab_" + num).show();
	jQuery("#zipTab_" + now_tab).removeClass("active");
	jQuery("#zipTab_" + num).addClass("active");
	if(num == 1){
		jQuery("#zipKind").val("dong");
		<?php if(empty($_POST['keyword'])){?>
		jQuery("#keyword1").focus();
		<?php }?>
	}else if(num == 2){
		if(jQuery("input:radio[name=roadKind]:checked").val() == 1){
			jQuery("#zipKind").val("road");
		}else if(jQuery("input:radio[name=roadKind]:checked").val() == 2){
			jQuery("#zipKind").val("post");
		}
		<?php if(empty($_POST['keyword'])){?>
		jQuery("#keyword2").focus();
		<?php }?>
	}
	now_tab = num;
}
function openerInput(zipcode, addr1){
	jQuery("#zipcode", opener.document).attr("value", zipcode);
	jQuery("#addr1", opener.document).attr("value", addr1);
	jQuery("#addr2", opener.document).focus();
	jQuery(opener.document).find("#error_addr").hide()
	self.close();
}
function searchOption(){
	jQuery('#errMsg2').empty().hide();
	if(jQuery("input:radio[name=roadKind]:checked").val() == 1){
		jQuery("#zipKind").val("road");
		jQuery('#roadKind_1').show();
		jQuery('#roadKind_2').hide();
	}else if(jQuery("input:radio[name=roadKind]:checked").val() == 2){
		jQuery("#zipKind").val("post");
		jQuery('#roadKind_1').hide();
		jQuery('#roadKind_2').show();
	}
}
jQuery(function() {
	jQuery("#keyword1").keydown(function() {
		if(jQuery(this).val() != ''){jQuery('#errMsg1').empty().hide();}if(event.keyCode == 13) zipSubmit();
	});
	jQuery("#keyword2").keydown(function() {
		if(jQuery(this).val() != ''){jQuery('#errMsg2').empty().hide();}if(event.keyCode == 13) zipSubmit();
	});
});
window.onload = function(){
	tabSelect(<?php echo $currentTab?>);
	<?php if($currentTab == 2){?>searchOption();<?php }?>
}
</script>

</head>
<body style='margin:5;padding:0'>
<div id="NFBoard_Content" class="text-center" style="width:100%;">
	<form name="zipForm" id="zipForm" method="post" action="<?php echo NFB_HOME_URL."/?NFPage=zipcode"; ?>">
	<input type="hidden" name="zipKind" id="zipKind" value="<?php echo $zipKind?>" />

		<div class="panel panel-default" style="margin-top:10px;">
			<div class="panel-heading">우편번호 검색</div>
			<div class="panel-body">

				<p style="margin-top:10px;">

					<ul class="nav nav-tabs" role="tablist">
						<li id="zipTab_1" onclick="tabSelect(1);"><a href="#">지번검색</a></li>
						<li id="zipTab_2" onclick="tabSelect(2);"><a href="#">도로명검색</a></li>
					</ul>
					<br/>

					<div id="tab_1"<?php if($zipKind != "dong"){?> style="display:none;"<?php }?>>
						<div class="input-group">
							<input type="text" name="keyword1" id="keyword1" class="form-control" value="<?php echo $_POST['keyword1']?>" />
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" onclick="zipSubmit();">&nbsp;&nbsp;검색&nbsp;&nbsp;</button>
							</span>
						</div>
						
						<div id="errMsg1" class="alert alert-danger" style="margin-top:5px;display:none;" role="alert"></div>
						<h3><span class="label label-info">
							<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)){?>ex) 고척동 76-406
							<?php }else{?>ex) 논현동, 상계동<?php }?>
						</span></h3>
					</div>

					<div id="tab_2"<?php if($zipKind == "dong"){?> style="display:none;"<?php }?>>

						<label class="checkbox-inline">
							<input type="radio" name="roadKind" value="1" onclick="searchOption();"<?php if(empty($_POST['roadKind']) || $_POST['roadKind'] == 1) echo " checked";?>/> 도로명검색
						</label>
						<label class="checkbox-inline">
							<input type="radio" name="roadKind" value="2"  onclick="searchOption();"<?php if($_POST['roadKind'] == 2) echo " checked";?>/> 우편번호검색
						</label>
						<div class="input-group" style="padding-top:10px;">
							<input type="text" name="keyword2" id="keyword2" class="form-control" value="<?php echo $_POST['keyword2']?>"/>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" onclick="zipSubmit();">&nbsp;&nbsp;검색&nbsp;&nbsp;</button>
							</span>
						</div>
						<div id="errMsg2" class="alert alert-danger" style="margin-top:5px;display:none;" role="alert"></div>
						<h3><span class="label label-info">
							<span id="roadKind_1" <?php if($_POST['roadKind'] == 2) echo "style='display:none;'";?>>ex) 세종대로 10, 중앙로 10-1</span>
							<span id="roadKind_2" <?php if(empty($_POST['roadKind']) || $_POST['roadKind'] == 1) echo "style='display:none;'";?>>ex) 100-801, 152-826</span>
						</span></h3>
					</div>

				</p>

			</div>

			<?php if($_POST['zipKind'] == "dong"){?>
			<?php if($resultMsg!=""){?><div class="well text-center" style="margin:0 10px 20px 10px;font-size:12px;"><?php echo $resultMsg?></div><?php }?>
			<?php if(count($zipList) > 0){?>
			<table class="table table-hover">
				<thead>
				<tr class="active">
					<th colspan="2">검색결과</th>
				</tr>
				</head>
				<tbody>
				<?php for($i = 0; $i < count($zipList); $i++) {echo $zipList[$i];}?>
				</tbody>
			</table>
			<?php }?>
			<?php }?>

			<?php if($_POST['zipKind'] == "road" || $_POST['zipKind'] == "post"){?>
			<?php if($resultMsg!=""){?><div class="well text-center" style="margin:0 10px 0 10px;"><?php echo $resultMsg?></div><?php }?>
			<?php if(count($zipList) > 0){?>
			<table class="table table-hover">
				<thead>
				<tr class="active">
					<th colspan="2">검색결과</th>
				</tr>
				</head>
				<tbody>
				<?php for($i = 0; $i < count($zipList); $i++) {echo $zipList[$i];}?>
				</tbody>
			</table>
			<?php }?>
			<?php }?>

		</div>

	</form>
</div>
</body>
</html>