<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_EntryPoint_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Mage_Core_Model_EntryPoint_Http
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Mage_Core_Model_EntryPoint_Http(__DIR__, array(), $this->_objectManagerMock);
    }

    public function testHttpHandlerProcessesRequest()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $requestHandlerMock = $this->getMock('Magento_Http_HandlerInterface');
        $requestHandlerMock->expects($this->once())->method('handle')->with($request, $response);
        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('Mage_Core_Controller_Request_Http', $request),
            array('Mage_Core_Controller_Response_Http', $response),
            array('Magento_Http_Handler_Composite', $requestHandlerMock),
        )));
        $this->_model->processRequest();
    }
}
