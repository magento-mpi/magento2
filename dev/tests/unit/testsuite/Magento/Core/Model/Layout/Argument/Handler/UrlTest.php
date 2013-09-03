<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Layout_Argument_Handler_Url
 */
class Magento_Core_Model_Layout_Argument_Handler_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Handler_Url
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_urlModelMock = $this->getMock('Magento_Core_Model_Url', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Layout_Argument_Handler_Url($this->_objectManagerMock,
            $this->_urlModelMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
        unset($this->_urlModelMock);
    }

    public function testProcess()
    {
        $expectedUrl = "http://generated-url.com?___SID=U";

        $path = 'module/controller/action';
        $params = array('___SID' => "U");

        $this->_urlModelMock->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path), $this->equalTo($params))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->process(array('path' => $path, 'params' => $params)));
    }

    public function testProcessWithoutUrlParams()
    {
        $expectedUrl = "http://generated-url.com";

        $path = 'module/controller/action';
        $params = null;

        $this->_urlModelMock->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path), $this->equalTo($params))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->process(array('path' => $path)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Passed value has incorrect format
     */
    public function testProcessIfValueIsNotArray()
    {
        $this->_model->process('*/*/action');
    }
}
