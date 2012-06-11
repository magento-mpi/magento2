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

/**
 * Varien_Object test case.
 */
class Varien_Image_Adapter_ImageMagickTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Image_Adapter_ImageMagick
     */
    protected $_object;

    protected function setUp()
    {
    }

    public function tearDown()
    {
        Magento_Test_Environment::getInstance()->cleanTmpDirOnShutdown();
    }

    /**
     * @dataProvider watermarkDataProvider
     */
    public function testWatermark($imagePath, $expectedResult)
    {
        try {
            $this->_object = new Varien_Image_Adapter_ImageMagick;
            $this->_object->watermark($imagePath);
        } catch (Exception $e) {
            $this->assertContains($e->getMessage(), $expectedResult);
        }
    }

    public function watermarkDataProvider()
    {
        $_tmpPath = Magento_Test_Environment::getInstance()->getTmpDir();
        $imageAbsent = $_tmpPath . DIRECTORY_SEPARATOR . md5(time() + microtime(true)) . '2';
        $imageExists = $_tmpPath . DIRECTORY_SEPARATOR . md5(time() + microtime(true)) . '1';
        touch($imageExists);

        return array(
            array('', Varien_Image_Adapter_ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array($imageAbsent, Varien_Image_Adapter_ImageMagick::ERROR_WATERMARK_IMAGE_ABSENT),
            array($imageExists, Varien_Image_Adapter_ImageMagick::ERROR_WRONG_IMAGE),
        );
    }
}

