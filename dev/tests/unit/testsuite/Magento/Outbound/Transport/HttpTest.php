<?php
/**
 * Magento_Outbound_Transport_Http
 *  
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Outbound_Transport_HttpTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockVrnHttpAdptrCrl;
    
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockMessage;
        
    public function setUp() 
    {
        $this->_mockVrnHttpAdptrCrl = $this->getMockBuilder('Magento_HTTP_Adapter_Curl')
            ->disableOriginalConstructor()->getMock();
        $this->_mockMessage = $this->getMockBuilder('Magento_Outbound_Message')
            ->disableOriginalConstructor()->getMock();
        $this->_mockMessage->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('header'=>'value')));        
    }

    /**
     * Test case for when http adapter returns null
     * 
     * @expectedException Zend_Http_Exception
     */   
    public function testNullResponse() 
    {
        $uut = new Magento_Outbound_Transport_Http($this->_mockVrnHttpAdptrCrl);
        $this->_mockVrnHttpAdptrCrl->expects($this->any())
            ->method('read')
            ->will($this->returnValue(null));
        $uut->dispatch($this->_mockMessage);
    }
    
    public function testPositive()
    {
        $uut = new Magento_Outbound_Transport_Http($this->_mockVrnHttpAdptrCrl);
        $this->_mockVrnHttpAdptrCrl->expects($this->any())
            ->method('read')
            ->will($this->returnValue("HTTP/2.0 200 OK\nHdrkey: Hdrval\n\nMessage Body"));
        $response = $uut->dispatch($this->_mockMessage);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame("OK", $response->getMessage());
        $this->assertSame(array("Hdrkey" => "Hdrval"), $response->getHeaders());
        $this->assertSame("Message Body", $response->getBody());
    }

    /**
     * @dataProvider timeoutDataProvider
     *
     * @param $timeout
     */
    public function testMessageTimeout($timeout, $expectedTimeout)
    {
        $uut = new Magento_Outbound_Transport_Http($this->_mockVrnHttpAdptrCrl);
        $this->_mockMessage->expects($this->any())
            ->method('getTimeout')
            ->will($this->returnValue($timeout));
        $config = array(
            'verifypeer' => true,
            'verifyhost' => 2,
            'timeout' => $expectedTimeout
        );
        $this->_mockVrnHttpAdptrCrl->expects($this->once())
            ->method('setConfig')
            ->with($config);
        $this->_mockVrnHttpAdptrCrl->expects($this->any())
            ->method('read')
            ->will($this->returnValue("HTTP/2.0 200 OK\nHdrkey: Hdrval\n\nMessage Body"));
        $response = $uut->dispatch($this->_mockMessage);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame("OK", $response->getMessage());
        $this->assertSame(array("Hdrkey" => "Hdrval"), $response->getHeaders());
        $this->assertSame("Message Body", $response->getBody());
    }

    /**
     * DataProvider for testing dispatch with different message timeout.
     *
     * @return array
     */
    public function timeoutDataProvider()
    {
        return array(
            array(0, Magento_Outbound_Message::DEFAULT_TIMEOUT),
            array(null, Magento_Outbound_Message::DEFAULT_TIMEOUT),
            array(5, 5),
            array(Magento_Outbound_Message::DEFAULT_TIMEOUT, Magento_Outbound_Message::DEFAULT_TIMEOUT)
        );
    }
}
