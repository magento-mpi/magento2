<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Image
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Image_Adapter_ImageMagickTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider watermarkDataProvider
     */
    public function testWatermark($imagePath, $expectedMessage)
    {
        $this->setExpectedException('LogicException', $expectedMessage);
        $object = new Varien_Image_Adapter_ImageMagick;
        $object->watermark($imagePath);
    }

    /**
     * @return array
     */
    public function watermarkDataProvider()
    {
        return array(
            array('', Varien_Image_Adapter_ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array(__DIR__ . '/not_exists', Varien_Image_Adapter_ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array(__DIR__ . '/_files/invalid_image.jpg', Varien_Image_Adapter_ImageMagick::ERROR_WRONG_IMAGE),
        );
    }
}
