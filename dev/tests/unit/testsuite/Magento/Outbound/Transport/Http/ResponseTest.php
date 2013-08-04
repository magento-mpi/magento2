<?php 
/**
 * Magento_Outbound_Transport_Http_Response
 *  
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Transport_Http_ResponseTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockZndHttpResp;
        
    public function setUp() 
    {
        $this->_mockZndHttpResp = $this->getMockBuilder('Zend_Http_Response')
            ->disableOriginalConstructor()->getMock();
    }
    
    public function testIsSuccessfulTrue() 
    {
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue(299));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertTrue($uut->isSuccessful());
    }

    public function testIsSuccessfulFalse()
    {
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue(301));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertFalse($uut->isSuccessful());
    }
    
    public function testGetStatusCode() 
    {
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue(299));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertSame(299, $uut->getStatusCode());
    }
    
    public function testGetMessage()
    {
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue("A-OK"));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertSame("A-OK", $uut->getMessage());
    }

    public function testGetBody()
    {
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getRawBody')
            ->will($this->returnValue("Raw Body"));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertSame("Raw Body", $uut->getBody());
        $this->assertSame("Raw Body", $uut->getRawBody());
    }
    
    public function testGetHeaders()
    {
        $hdrs = array('key1' => 'va11', 'key2' => 'val2');
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($hdrs));
        $this->_mockZndHttpResp->expects($this->any())
            ->method('getHeader')
            ->will($this->returnValue($hdrs['key1']));
        $uut = new Magento_Outbound_Transport_Http_Response($this->_mockZndHttpResp);
        $this->assertSame($hdrs, $uut->getHeaders());
        $this->assertSame($hdrs['key1'], $uut->getHeader('key1'));
    }
}