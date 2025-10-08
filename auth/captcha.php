<?php
session_start();

$captcha_code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ123456789"), 0, 5);
$_SESSION['captcha'] = $captcha_code;

$width = 120;
$height = 40;
$image = imagecreate($width, $height);

$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 5, 30, 12, $captcha_code, $text_color);

header("Content-Type: image/png");
imagepng($image);
imagedestroy($image);
