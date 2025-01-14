<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate a random string for the captcha
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_string = '';
for ($i = 0; $i < 5; $i++) {
    $captcha_string .= $characters[rand(0, strlen($characters) - 1)];
}

// Store the captcha string in session
$_SESSION['captcha'] = $captcha_string;

// Create the image
$image = imagecreatetruecolor(200, 50);
if (!$image) {
    die('Failed to create image!');
}

// Set the background color
$background_color = imagecolorallocate($image, 255, 255, 255); // white
imagefill($image, 0, 0, $background_color);

// Set the text color
$text_color = imagecolorallocate($image, 0, 0, 0); // black

// Set the font size and path (ensure you have a valid font file)
$font = 'RubikVinyl-Regular.ttf';  // Replace with your font file path
if (!file_exists($font)) {
    die('Font file not found!');
}
$font_size = 30;

// Add the captcha string to the image
imagettftext($image, $font_size, 0, 50, 35, $text_color, $font, $captcha_string);

// Output the image
header('Content-Type: image/png');
imagepng($image);

// Destroy the image in memory
imagedestroy($image);
?>
