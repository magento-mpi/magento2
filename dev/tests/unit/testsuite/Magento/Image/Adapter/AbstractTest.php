<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Image
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Image\Adapter\AbstractAdapter.
 */
class Magento_Image_Adapter_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Image\Adapter\AbstractAdapter
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $ioFile = $this->getMock('Magento\Io\File', array('mkdir'));
        $ioFile->expects($this->any())
            ->method('mkdir')
            ->will($this->returnValue(true));

        $data = array('io' => $ioFile);
        $this->_model = $this->getMockForAbstractClass('Magento\Image\Adapter\AbstractAdapter', array($data));
    }

    /**
     * Test _adaptResizeValues with null as a value one of parameters
     *
     * @dataProvider _adaptResizeValuesDataProvider
     */
    public function test_adaptResizeValues($width, $height, $expectedResult)
    {
        $method = new ReflectionMethod($this->_model, '_adaptResizeValues');
        $method->setAccessible(true);

        $result = $method->invoke($this->_model, $width, $height);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function _adaptResizeValuesDataProvider()
    {

        $expected = array(
            'src' => array(
                'x' => 0,
                'y' => 0
            ),
            'dst' => array(
                'x' => 0,
                'y' => 0,
                'width'  => 135,
                'height' => 135
            ),
            'frame' => array(
                'width'  => 135,
                'height' => 135
            )
        );

        return array(
            array(135, null, $expected),
            array(null, 135, $expected),
        );
    }

    /**
     * @dataProvider _prepareDestinationDataProvider
     */
    public function test_prepareDestination($destination, $newName, $expectedResult)
    {
        $property = new ReflectionProperty(get_class($this->_model), '_fileSrcPath');
        $property->setAccessible(true);
        $property->setValue($this->_model, '_fileSrcPath');

        $property = new ReflectionProperty(get_class($this->_model), '_fileSrcName');
        $property->setAccessible(true);
        $property->setValue($this->_model, '_fileSrcName');

        $method = new ReflectionMethod($this->_model, '_prepareDestination');
        $method->setAccessible(true);

        $result = $method->invoke($this->_model, $destination, $newName);

        $this->assertEquals($expectedResult, $result);
    }

    public function _prepareDestinationDataProvider()
    {
        return array(
            array(__DIR__, 'name.txt', __DIR__ . DIRECTORY_SEPARATOR . 'name.txt'),
            array(__DIR__ . DIRECTORY_SEPARATOR . 'name.txt', null, __DIR__ . DIRECTORY_SEPARATOR . 'name.txt'),
            array(null, 'name.txt', '_fileSrcPath' . DIRECTORY_SEPARATOR . 'name.txt'),
            array(null, null, '_fileSrcPath' . DIRECTORY_SEPARATOR . '_fileSrcName'),
        );
    }

}
