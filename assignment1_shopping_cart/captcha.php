<?php

session_start();

if (!function_exists("imagecreatetruecolor")) {
    http_response_code(500);
    exit("Enable the GD extension in php.ini.");
}

$code = (string)random_int(10000, 99999);

$_SESSION["captcha_code"] = $code;

$image = imagecreatetruecolor(150, 50);

$background = imagecolorallocate($image, 240, 240, 240);
$textColor = imagecolorallocate($image, 20, 20, 20);
$noiseColor = imagecolorallocate($image, 120, 120, 120);

imagefill($image, 0, 0, $background);

for ($i = 0; $i < 6; $i++) {

    imageline(
        $image,
        random_int(0, 150),
        random_int(0, 50),
        random_int(0, 150),
        random_int(0, 50),
        $noiseColor
    );

}

imagestring($image, 5, 45, 17, $code, $textColor);

header("Content-Type: image/png");
header("Cache-Control: no-store, no-cache, must-revalidate");

imagepng($image);
imagedestroy($image);

?>
