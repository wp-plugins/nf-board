<?php
session_start();

if(!empty($_REQUEST['sid'])){
	session_id($_REQUEST['sid']);
}

//if(empty($_REQUEST['sid']) && empty($_SESSION['authKeySub'])){
	$_SESSION['authKeySub'] = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
//}

header("Content-type: image/png");

$wdth = 105; $hght = 65;
$img = imagecreate($wdth,$hght);
$clr_bckgrnd = imagecolorallocate($img,246,246,246);
imagefilledrectangle($img,0,0,$wdth,$hght,$clr_bckgrnd);

$arg1[0] = rand(30,30);
$arg1[1] = rand(30,30);
$arg1[2] = rand(30,30);
$arg1[3] = rand(30,30);
$arg2[0] = rand(0,0);
$arg2[1] = rand(0,0);
$arg2[2] = rand(0,0);
$arg2[3] = rand(0,0);
$arg3[0] = rand(45,45);
$arg3[1] = rand(45,45);
$arg3[2] = rand(45,45);
$arg3[3] = rand(45,45);
$clr_frgrnd = imagecolorallocate($img,51,50,50);

imagettftext($img, $arg1[0],$arg2[0],4,$arg3[0],$clr_frgrnd, "./arial.ttf", substr($_SESSION['authKeySub'], 0, 1));
imagettftext($img, $arg1[1],$arg2[1],29,$arg3[1],$clr_frgrnd, "./arial.ttf", substr($_SESSION['authKeySub'], 1, 1));
imagettftext($img, $arg1[2],$arg2[2],54,$arg3[2],$clr_frgrnd, "./arial.ttf", substr($_SESSION['authKeySub'], 2, 1));
imagettftext($img, $arg1[3],$arg2[3],79,$arg3[3],$clr_frgrnd, "./arial.ttf", substr($_SESSION['authKeySub'], 3, 1));

imagepng($img);
imagedestroy($img);
?>