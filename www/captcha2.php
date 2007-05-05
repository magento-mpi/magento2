<?php
require_once('lib3d.php');

$imgW = 500;
$imgH = 350;
$grainW = $grainH = 6;
$bevel = -20;
if (isset($_GET['key'])) {
    $key = preg_replace("/\W/",'',$_GET['key']);
    $text = $key;
} else {
    $text = 'captcha';
}


$L = new C3DLib(100,30);
$L->mAddTransform('Turn', 'X', -30);
$L->mAddTransform('Turn', 'Z', 10);

$it = imageCreate($imgW, $imgH);
	$itBg = imageColorAllocate($it, 128, 128, 128);
	$itFg = imageColorAllocate($it, 0, 0, 0);
	imageTTFText($it, $imgH*0.52, 0, 0,$imgH*0.825, $itFg, 'cambriab', $text);

$im = imageCreate($imgW, $imgH);
	$imBg = imageColorAllocate($im, 255, 255, 255);
	$imFg = imageColorAllocate($im, 0, 0, 0);

for($x=$grainW; $x <= $imgW; $x += $grainW)
{
	for($y=$grainH; $y <= $imgH; $y += $grainH)
	{
		$L->mFilledPolygon($im, array(
			array($x-$grainW, $y-$grainH, imageColorAt($it, $x-$grainW, $y-$grainH) ? 0 : $bevel),
			array($x, $y-$grainH, imageColorAt($it, $x, $y-$grainH) ? 0 : $bevel),
			array($x, $y, imageColorAt($it, $x, $y) ? 0 : $bevel),
			array($x-$grainW, $y, imageColorAt($it, $x-$grainW, $y) ? 0 : $bevel),
		), $imBg);
		$L->mPolygon($im, array(
			array($x-$grainW, $y-$grainH, imageColorAt($it, $x-$grainW, $y-$grainH) ? 0 : $bevel),
			array($x, $y-$grainH, imageColorAt($it, $x, $y-$grainH) ? 0 : $bevel),
			array($x, $y, imageColorAt($it, $x, $y) ? 0 : $bevel),
			array($x-$grainW, $y, imageColorAt($it, $x-$grainW, $y) ? 0 : $bevel),
		), $imFg);
	}
}

header("Content-type: image/png");
imagePNG($im);

?>