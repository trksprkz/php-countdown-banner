<?php

date_default_timezone_set('America/Los_Angeles');
include 'GIFEncoder.class.php';
include 'php52-fix.php';

// Check if 'time' key is set in the $_GET array
$time = isset($_GET['time']) ? $_GET['time'] : null;

if ($time === null) {
    // Handle the case when 'time' is not set
    die("Error: 'time' parameter is not set.");
}

$future_date = new DateTime(date('r', strtotime($time)));
$time_now = time();
$now = new DateTime(date('r', $time_now));
$frames = array();
$delays = array();

$image = imagecreatefrompng('images/countdown.png');
$delay = 100; // milliseconds

$font = array(
    'size' => 19.5,
    'angle' => 0,
    'x-offset' => 1,
    'y-offset' => 30,
    'file' => __DIR__ . DIRECTORY_SEPARATOR . 'Futura.ttc',
    'color' => imagecolorallocatealpha($image, 255, 255, 255, 127),
);

for ($i = 0; $i <= 60; $i++) {
    $interval = date_diff($future_date, $now);

    if ($future_date < $now) {
        $image = imagecreatefrompng('images/countdown.png');
        $text = $interval->format('00:00:00:00');
        imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
        ob_start();
        imagegif($image);
        $frames[] = ob_get_contents();
        $delays[] = $delay;
        $loops = 1;
        ob_end_clean();
        break;
    } else {
        $image = imagecreatefrompng('images/countdown.png');
        $text = $interval->format('0%a %H %I %S');
        imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
        ob_start();
        imagegif($image);
        $frames[] = ob_get_contents();
        $delays[] = $delay;
        $loops = 0;
        ob_end_clean();
    }

    $now->modify('+1 second');
}

header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$gif = new AnimatedGif($frames, $delays, $loops);
$gif->display();
