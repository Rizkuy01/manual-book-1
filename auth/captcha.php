<?php
session_start();

// === Generate Captcha Code ===
$captcha_code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $captcha_code;

// === Ukuran Gambar ===
$width = 120;
$height = 45;
$image = imagecreatetruecolor($width, $height);

// === Warna ===
$bg_color    = imagecolorallocate($image, 245, 245, 245); // abu terang
$text_color  = imagecolorallocate($image, 200, 0, 0);     // merah tua
$line_color  = imagecolorallocate($image, 100, 100, 100); // abu tua
$dot_color   = imagecolorallocate($image, 80, 150, 180);  // biru kehijauan

// === Latar belakang ===
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// === Garis Acak ===
for ($i = 0; $i < 6; $i++) {
    imageline(
        $image,
        0, rand(0, $height),
        $width, rand(0, $height),
        $line_color
    );
}

// === Titik-titik Acak ===
for ($i = 0; $i < 250; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $dot_color);
}

// === Tulis Teks (menggunakan font TTF) ===
$font_path = __DIR__ . '/../src/fonts/arial.ttf'; // sesuaikan path font-mu
if (!file_exists($font_path)) {
    // fallback jika font tidak ada
    imagestring($image, 5, 25, 15, $captcha_code, $text_color);
} else {
    $font_size = 18;
    imagettftext(
        $image,
        $font_size,
        rand(-10, 10),
        18,
        33,
        $text_color,
        $font_path,
        $captcha_code
    );
}

// === Output ke Browser ===
header("Content-Type: image/png");
imagepng($image);
imagedestroy($image);
?>
