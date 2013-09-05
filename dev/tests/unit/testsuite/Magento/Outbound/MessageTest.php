<?php
/**
 * \Magento\Outbound\Message
 * 
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Outbound_MessageTest extends PHPUnit_Framework_TestCase
{
    public function test() 
    {
        $uut = new \Magento\Outbound\Message('http://localhost', array('key1'=>'val1', 'key2' => 'val2'), "Body");
        // check endpoint url
        $this->assertSame('http://localhost', $uut->getEndpointUrl());
        // check headers
        $rsltHdr = $uut->getHeaders();
        $this->assertSame('val1', $rsltHdr['key1']);
        $this->assertSame('val2', $rsltHdr['key2']);
        // check for body
        $this->assertSame("Body", $uut->getBody());
        // check for default timeout
        $this->assertSame(20, $uut->getTimeout());
    }
}