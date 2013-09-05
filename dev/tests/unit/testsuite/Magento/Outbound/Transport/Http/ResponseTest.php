<?php 
/**
 * \Magento\Outbound\Transport\Http\Response
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
    public function testIsSuccessfulTrue() 
    {
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 299 OK");
        $this->assertTrue($uut->isSuccessful());
    }

    public function testIsSuccessfulFalse()
    {
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 301 Moved Permanently");
        $this->assertFalse($uut->isSuccessful());
    }
    
    public function testGetStatusCode() 
    {
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 299 OK");
        $this->assertSame(299, $uut->getStatusCode());
    }
    
    public function testGetMessage()
    {
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 299 A-OK");
        $this->assertSame("A-OK", $uut->getMessage());
    }

    public function testGetBody()
    {
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 200 OK\nHdrkey: Hdrval\n\nRaw Body");
        $this->assertSame("Raw Body", $uut->getBody());
    }
    
    public function testGetHeaders()
    {
        $hdrs = array('Key1' => 'val1', 'Key2' => 'val2');
        $uut = new \Magento\Outbound\Transport\Http\Response("HTTP/2.0 200 OK\nkey1: val1\nkey2: val2\n\nMessage Body");
        $this->assertEquals($hdrs, $uut->getHeaders());
        $this->assertEquals($hdrs['Key1'], $uut->getHeader('Key1'));
    }
}
