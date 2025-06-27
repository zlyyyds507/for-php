<?php
session_start();
header('Content-type: image/png');
// 生成4位验证码
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
$code = '';
for ($i = 0; $i < 4; $i++) {
    $code .= $chars[mt_rand(0, strlen($chars)-1)];
}
$_SESSION['captcha'] = $code;
$im = imagecreatetruecolor(100, 36);
$bg = imagecolorallocate($im, 255, 255, 255);
$font_color = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 100, 36, $bg);
for($i=0;$i<4;$i++){
    $line_color = imagecolorallocate($im, mt_rand(100,255), mt_rand(100,255), mt_rand(100,255));
    imageline($im, mt_rand(0,100), mt_rand(0,36), mt_rand(0,100), mt_rand(0,36), $line_color);
}
$fontfile = __DIR__ . '/assets/fonts/arial.ttf';
if(file_exists($fontfile)){
    imagettftext($im, 20, mt_rand(-10,10), 15, 28, $font_color, $fontfile, $code);
} else {
    imagestring($im, 5, 25, 10, $code, $font_color);
}
imagepng($im);
imagedestroy($im);