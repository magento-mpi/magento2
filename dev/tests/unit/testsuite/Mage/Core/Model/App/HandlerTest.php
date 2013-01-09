<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_App_HandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_App_Handler
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    public function setUp()
    {
        $this->_appMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_App_Handler($this->_appMock);
    }

    public function testHandlePassesRequestAndResponseToApplication()
    {
        $requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_appMock->expects($this->once())->method('setRequest')->with($requestMock)->will($this->returnSelf());
        $this->_appMock->expects($this->once())->method('setResponse')->with($responseMock)->will($this->returnSelf());
        $this->_appMock->expects($this->once())->method('run');
        $this->_model->handle($requestMock, $responseMock);
    }
}
