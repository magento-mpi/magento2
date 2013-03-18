<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Simplexml_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testLoadString()
    {
        $xml = '<?xml version="1.0"?><config><node>1</node></config>';
        $config = new Varien_Simplexml_Config;
        $this->assertFalse($config->loadString(''));
        $this->assertTrue($config->loadString($xml));
        $this->assertXmlStringEqualsXmlString($xml, $config->getXmlString());
    }
}
