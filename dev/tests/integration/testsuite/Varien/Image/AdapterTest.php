<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Image
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Image_AdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Adapter classes for test
     *
     * @var array
     */
    protected $_adapters = array(
        'Varien_Image_Adapter_Gd2',
        'Varien_Image_Adapter_Imagemagic'
    );

    /**
     * Add adapters to each data provider case
     *
     * @param array $data
     * @return array
     */
    protected function _prepareData($data)
    {
        $result   = array();
        foreach ($this->_adapters as $adapter) {
            foreach ($data as $row) {
                $row[] = new $adapter;
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Returns fixture image size
     *
     * @return array
     */
    protected function _getFixtureImageSize()
    {
        return array(311, 162);
    }

    /**
     * Compare two colors with some epsilon
     *
     * @param array $colorBefore
     * @param array $colorAfter
     * @return bool
     */
    protected function _compareColors($colorBefore, $colorAfter)
    {
        // get different epsilon for 8 bit (max value = 255) & 16 bit (max value = 65535) images (eps = 0.05%)
        $eps = max($colorAfter) > 255 ? 3300 : 15;

        $result = true;
        foreach ($colorAfter as $i => $v) {
            if (abs($colorBefore[$i] - $v) > $eps) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * Randomly returns fixtures image
     *
     * @return string|null
     */
    protected function _getFixture($type)
    {
        $dir  = __DIR__ . DIRECTORY_SEPARATOR . '_files'. DIRECTORY_SEPARATOR;
        $data = glob($dir . $type);

        if (!empty($data)) {
            $i = isset($data[1]) ? array_rand($data) : 0;
            return $data[$i];
        }

        return null;
    }

    /**
     * Checks is adapter testable.
     * Mark test as skipped if not
     *
     * @param Varien_Image_Adapter_Abstract $adapter
     */
    protected function _isAdapterAvailable($adapter)
    {
        try {
            $adapter->checkDependencies();
        } catch (Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * Checks if all dependencies are loaded
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider adaptersDataProvider
     */
    public function testCheckDependencies($adapter)
    {
        $this->_isAdapterAvailable($adapter);
    }

    public function adaptersDataProvider()
    {
        $data = array();
        foreach ($this->_adapters as $adapter) {
            $data[] = array(new $adapter);
        }
        return $data;
    }

    /**
     * Checks open method
     *
     * @param string $image
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @depends testCheckDependencies
     * @dataProvider openDataProvider
     */
    public function testOpen($image, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);
        $this->assertEquals($this->_getFixtureImageSize(), array(
            $adapter->getOriginalWidth(),
            $adapter->getOriginalHeight()
        ));
    }

    public function openDataProvider()
    {
        return $this->_prepareData(array(
            array($this->_getFixture('magento_test.png'))
        ));
    }

    /**
     * Checks image saving process
     *
     * @param string $image
     * @param array $tempPath (dirName, newName)
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider saveDataProvider
     * @depends testCheckDependencies
     * @depends testOpen
     */
    public function testSave($image, $tempPath, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);
        call_user_func_array(array($adapter, 'save'), $tempPath);
        $tempPath = join('', $tempPath);
        $this->assertTrue(file_exists($tempPath));
        unlink($tempPath);
    }

    public function saveDataProvider()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        return $this->_prepareData(array(
            array(
                $this->_getFixture('magento_test.png'),
                array($dir . uniqid('test_image_adapter'))
            ),
            array(
                $this->_getFixture('magento_test.png'),
                array($dir, uniqid('test_image_adapter'))
            )
        ));
    }

    /**
     * Checks image resizing
     *
     * @param string $image
     * @param array $dims (width, height)
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider resizeDataProvider
     * @depends testCheckDependencies
     * @depends testOpen
     */
    public function testResize($image, $dims, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);
        $adapter->resize($dims[0], $dims[1]);
        $this->assertEquals($dims, array(
            $adapter->getOriginalWidth(),
            $adapter->getOriginalHeight()
        ));
    }

    public function resizeDataProvider()
    {
        return $this->_prepareData(array(
            array(
                $this->_getFixture('magento_test.png'),
                array(150, 70)
            )
        ));
    }

    /**
     * Checks image rotation
     *
     * @param string $image
     * @param int $angle
     * @param array $pixel
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider rotateDataProvider
     * @depends testCheckDependencies
     * @depends testOpen
     */
    public function testRotate($image, $angle, $pixel, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);

        $size = array(
            $adapter->getOriginalWidth(),
            $adapter->getOriginalHeight()
        );

        $colorBefore = $adapter->getColorAt($pixel['x'], $pixel['y']);
        $adapter->rotate($angle);

        $newPixel = $this->_convertCoordinates($pixel, $angle, $size, array(
            $adapter->getOriginalWidth(),
            $adapter->getOriginalHeight()
        ));
        $colorAfter  = $adapter->getColorAt($newPixel['x'], $newPixel['y']);

        $result = $this->_compareColors($colorBefore, $colorAfter);
        $this->assertTrue($result, join(',', $colorBefore) . ' not equals ' . join(',', $colorAfter));
    }

    /**
     * Get pixel coordinates after rotation
     *
     * @param array $pixel ('x' => 0, 'y' => 0)
     * @param int $angle
     * @param array $oldSize (width, height)
     * @param array $size (width, height)
     * @return array
     */
    protected function _convertCoordinates($pixel, $angle, $oldSize, $size)
    {
        $angle  = $angle * pi() / 180;
        $center = array(
            'x' => $oldSize[0] / 2,
            'y' => $oldSize[1] / 2,
        );

        $pixel['x'] -= $center['x'];
        $pixel['y'] -= $center['y'];
        return array(
            'x' => round($size[0]/2 + $pixel['x'] * cos($angle) + $pixel['y'] * sin($angle), 0),
            'y' => round($size[1]/2 + $pixel['y'] * cos($angle) - $pixel['x'] * sin($angle), 0)
        );
    }

    public function rotateDataProvider()
    {
        return $this->_prepareData(array(
            array(
                $this->_getFixture('magento_test.png'),
                45,
                array('x' => 157, 'y' => 35)
            ),
            array(
                $this->_getFixture('magento_test.png'),
                48,
                array('x' => 157, 'y' => 35)
            ),
            array(
                $this->_getFixture('magento_test.png'),
                90,
                array('x' => 250, 'y' => 74)
            ),
            array(
                $this->_getFixture('magento_test.png'),
                180,
                array('x' => 250, 'y' => 74)
            )
        ));
    }

    /**
     * Checks if watermark exists on the right position
     *
     * @param string $image
     * @param string $watermark
     * @param int $width
     * @param int $height
     * @param float $opacity
     * @param string $position
     * @param int $colorX
     * @param int $colorY
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider imageWatermarkDataProvider
     * @depends testCheckDependencies
     * @depends testOpen
     */
    public function testWatermark($image, $watermark, $width, $height, $opacity, $position, $colorX, $colorY, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);
        $pixel = $this->_prepareColor(array('x' => $colorX, 'y' => $colorY), $position, $adapter);

        $colorBefore = $adapter->getColorAt($pixel['x'], $pixel['y']);
        $adapter->setWatermarkWidth($width)
            ->setWatermarkHeight($height)
            ->setWatermarkImageOpacity($opacity)
            ->setWatermarkPosition($position)
            ->watermark($watermark);
        $colorAfter  = $adapter->getColorAt($pixel['x'], $pixel['y']);

        $result  = $this->_compareColors($colorBefore, $colorAfter);
        $message = join(',', $colorBefore) . ' not equals ' . join(',', $colorAfter);
        $this->assertFalse($result, $message);
    }

    public function imageWatermarkDataProvider()
    {
        return $this->_prepareData(array(
            array(
                $this->_getFixture('magento_test.png'),
                $this->_getFixture('watermark.*'),
                50,
                50,
                100,
                Varien_Image_Adapter_Abstract::POSITION_BOTTOM_RIGHT,
                10,
                10
            )
        ));
    }


    /**
     * Randomly set colorX and colorY coordinates according image width and height
     *
     * @param array $pixel ('x' => ..., 'y' => ...)
     * @param string $position
     * @param Varien_Image_Adapter_Abstract $adapter
     * @return array
     */
    protected function _prepareColor($pixel, $position, $adapter)
    {
        switch ($position) {
            case Varien_Image_Adapter_Abstract::POSITION_BOTTOM_RIGHT:
                $pixel['x'] = $adapter->getOriginalWidth()  - mt_rand(0, 50);
                $pixel['y'] = $adapter->getOriginalHeight() - mt_rand(0, 50);
                break;
            case Varien_Image_Adapter_Abstract::POSITION_BOTTOM_LEFT:
                $pixel['x'] = mt_rand(0, 50);
                $pixel['y'] = $adapter->getOriginalHeight() - mt_rand(0, 50);
                break;
            case Varien_Image_Adapter_Abstract::POSITION_TOP_LEFT:
                $pixel['x'] = mt_rand(0, 50);
                $pixel['y'] = mt_rand(0, 50);
                break;
            case Varien_Image_Adapter_Abstract::POSITION_TOP_RIGHT:
                $pixel['x'] = $adapter->getOriginalWidth() - mt_rand(0, 50);
                $pixel['y'] = mt_rand(0, 50);
                break;
            case Varien_Image_Adapter_Abstract::POSITION_STRETCH:
            case Varien_Image_Adapter_Abstract::POSITION_TILE:
                $pixel['x'] = mt_rand(0, $adapter->getOriginalWidth());
                $pixel['y'] = mt_rand(0, $adapter->getOriginalHeight());
                break;
        }
        return $pixel;
    }

    /**
     * Checks crop functionality
     *
     * @param string $image
     * @param int $left
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param Varien_Image_Adapter_Abstract $adapter
     *
     * @dataProvider cropDataProvider
     * @depends testCheckDependencies
     * @depends testOpen
     */
    public function testCrop($image, $left, $top, $right, $bottom, $adapter)
    {
        $this->_isAdapterAvailable($adapter);
        $adapter->open($image);

        $expectedSize = array(
            $adapter->getOriginalWidth()  - $left - $right,
            $adapter->getOriginalHeight() - $top  - $bottom
        );

        $adapter->crop($top, $left, $right, $bottom);

        $newSize = array(
            $adapter->getOriginalWidth(),
            $adapter->getOriginalHeight()
        );

        $this->assertEquals($expectedSize, $newSize);
    }

    public function cropDataProvider()
    {
        return $this->_prepareData(array(
            array(
                $this->_getFixture('magento_test.png'),
                50, 50, 75, 75
            ),
            array(
                $this->_getFixture('magento_test.png'),
                20, 50, 35, 35
            ),
            array(
                $this->_getFixture('magento_test.png'),
                0, 0, 0, 0
            )
        ));
    }
}