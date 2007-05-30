<?php
/**
 *
 * @file        Image.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Varien_File_Uploader_Image extends Varien_File_Uploader
{

    function __construct($file=null)
    {
        $this->newUploader($file);
    }

    public function resize($width=150, $height=150)
    {
        #
    }

    public function rotate($degrees=45)
    {
        #
    }

    public function flip($type="h")
    {
        #
    }

    public function crop($left="20%", $top="10px", $right="20%", $bottom="10px")
    {
        #
    }

    public function convert($format="jpeg")
    {
        #
    }

    public function addBevel($color="#000000")
    {
        #
    }

    public function addLeftTopBevel($color="#FEFEFE")
    {
        #
    }

    public function addRightBottomBevel($color="#FAFAFA")
    {
        #
    }

    public function addBorder($color="#FF0000")
    {
        #
    }

    public function addFrame($colors=Array('#FFFFFF','#999999','#666666','#000000'), $frameType=1)
    {
        #
    }

    public function addWatermark($position="BL", $absoluteX=null, $absoluteY=null)
    {
        
    }

    public function addReflection($height="10%", $space=0, $color="#FFFFFF", $opacity=60)
    {
        #
    }

    public function addText($string="", $direction="h", $color="#FFFFFF", $visibilityPercent=100, $background=null, $backgroundVisPercent=100, $font=5, $position="TR", $absoluteX=null, $absoluteY=null, $padding=0, $paddingX=null, $paddingY=null, $alignment="C", $lineSpacing=0)
    {
        #
    }

    public function convertToGreyscale()
    {
        #
    }

    public function colorInvert()
    {
        #
    }

    public function colorOverlay($color="#FFFFFF", $percent=50)
    {
        #
    }

    public function setContrast($value=0)
    {
        #
    }

    public function setBrightness($value=0)
    {
        #
    }

    public function setJpegQuality($value=85)
    {
        #
    }

    public function setBgColor("#000000")
    {
        #
    }

    function __destruct()
    {
        #
    }
}
 
// ft:php
// fileformat:unix
// tabstop:4
?>
