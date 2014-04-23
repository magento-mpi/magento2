<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Image
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Image\Adapter;

class ImageMagickTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider watermarkDataProvider
     */
    public function testWatermark($imagePath, $expectedMessage)
    {
        $filesystem =
            $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()->getMock();
        $this->setExpectedException('LogicException', $expectedMessage);
        $object = new \Magento\Framework\Image\Adapter\ImageMagick($filesystem);
        $object->watermark($imagePath);
    }

    /**
     * @return array
     */
    public function watermarkDataProvider()
    {
        return array(
            array('', \Magento\Framework\Image\Adapter\ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array(__DIR__ . '/not_exists', \Magento\Framework\Image\Adapter\ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array(
                __DIR__ . '/_files/invalid_image.jpg',
                \Magento\Framework\Image\Adapter\ImageMagick::ERROR_WRONG_IMAGE
            )
        );
    }
}
