<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");
$real_file_name = $_GET['file'];  // 다운받을때 파일명
$file = NFB_UPLOAD_PATH."NFBoard/".$_GET['filepath'];
$dnurl = iconv("UTF-8", "EUC-KR", $real_file_name);
$dn = "1";
$dn_yn = ($dn)?"attachment":"inline";
$bin_txt = "1";
$bin_txt = ($bin_txt)?"r":"rb";
if(is_file($file)){
	if(eregi("(MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT)){
		header("Content-type: application/octet-stream");
		header("Content-Length: ".filesize("$file"));
		header("Content-Disposition: $dn_yn; filename=$dnurl");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");
	}else{
		header("Content-type: file/unknown");
		header("Content-Length: ".filesize("$file"));
		header("Content-Disposition: $dn_yn; filename=$dnurl");
		header("Content-Description: PHP3 Generated Data");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	$fp = fopen($file, $bin_txt);
	if(!fpassthru($fp))
	fclose($fp);
}else{
	echo "해당 파일이나 경로가 존재하지 않습니다.";
	exit;
}
?>