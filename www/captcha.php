<?php

session_start();

$key = $_GET['key'];
for ($i=0, $text=''; $i<5; $i++) $text .= chr(rand(ord('A'), ord('Z')));
$_SESSION['captcha'][$key] = $text;

header('Content-type: image/jpg');
echo `convert -size 200x120 xc:lightblue -font Bookman-DemiItalic -pointsize 32 -fill blue -draw "text 10,20 '$text'" -fill yellow -draw "path 'M 5,5 L 135,5 M 5,10 L 135,10 M 5,15 L 135,15'" -trim jpg:- | convert -wave 4x70 -swirl 10 jpg:- jpg:-`;
