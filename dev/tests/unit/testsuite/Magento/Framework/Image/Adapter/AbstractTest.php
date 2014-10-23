<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\Image\Adapter\AbstractAdapter.
 */
namespace Magento\Framework\Image\Adapter;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Image\Adapter\AbstractAdapter
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject |\Magento\Framework\Filesystem\Directory\Write
     */
    protected $directoryWriteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject |\Magento\Framework\Filesystem
     */
    protected $filesystemMock;

    protected function setUp()
    {
        $this->directoryWriteMock = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Write',
            array(),
            array(),
            '',
            false
        );
        $this->filesystemMock = $this->getMock(
            'Magento\Framework\Filesystem',
            array('getDirectoryWrite', 'createDirectory'),
            array(),
            '',
            false
        );
        $this->filesystemMock->expects(
            $this->once()
        )->method(
            'getDirectoryWrite'
        )->will(
            $this->returnValue($this->directoryWriteMock)
        );

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Framework\Image\Adapter\AbstractAdapter',
            array($this->filesystemMock)
        );
    }

    protected function tearDown()
    {
        $this->directoryWriteMock = null;
        $this->_model = null;
        $this->filesystemMock = null;
    }

    /**
     * Test adaptResizeValues with null as a value one of parameters
     *
     * @dataProvider adaptResizeValuesDataProvider
     */
    public function testAdaptResizeValues($width, $height, $expectedResult)
    {
        $method = new \ReflectionMethod($this->_model, '_adaptResizeValues');
        $method->setAccessible(true);

        $result = $method->invoke($this->_model, $width, $height);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function adaptResizeValuesDataProvider()
    {

        $expected = array(
            'src' => array('x' => 0, 'y' => 0),
            'dst' => array('x' => 0, 'y' => 0, 'width' => 135, 'height' => 135),
            'frame' => array('width' => 135, 'height' => 135)
        );

        return array(array(135, null, $expected), array(null, 135, $expected));
    }

    /**
     * @dataProvider prepareDestinationDataProvider
     */
    public function testPrepareDestination($destination, $newName, $expectedResult)
    {
        $property = new \ReflectionProperty(get_class($this->_model), '_fileSrcPath');
        $property->setAccessible(true);
        $property->setValue($this->_model, '_fileSrcPath');

        $property = new \ReflectionProperty(get_class($this->_model), '_fileSrcName');
        $property->setAccessible(true);
        $property->setValue($this->_model, '_fileSrcName');

        $method = new \ReflectionMethod($this->_model, '_prepareDestination');
        $method->setAccessible(true);

        $result = $method->invoke($this->_model, $destination, $newName);

        $this->assertEquals($expectedResult, $result);
    }

    public function prepareDestinationDataProvider()
    {
        return array(
            array(__DIR__, 'name.txt', __DIR__ . '/name.txt'),
            array(__DIR__ . '/name.txt', null, __DIR__ . '/name.txt'),
            array(null, 'name.txt', '_fileSrcPath' . '/name.txt'),
            array(null, null, '_fileSrcPath' . '/_fileSrcName')
        );
    }
}
