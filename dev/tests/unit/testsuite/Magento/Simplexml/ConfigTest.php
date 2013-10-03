<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Simplexml;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadString()
    {
        $xml = '<?xml version="1.0"?><config><node>1</node></config>';
        $config = new \Magento\Simplexml\Config;
        $this->assertFalse($config->loadString(''));
        $this->assertTrue($config->loadString($xml));
        $this->assertXmlStringEqualsXmlString($xml, $config->getXmlString());
    }
}
