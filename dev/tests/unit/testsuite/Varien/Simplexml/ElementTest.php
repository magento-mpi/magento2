<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Simplexml_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testUnsetSelf()
    {
        /** @var $xml Varien_Simplexml_Element */
        $xml = simplexml_load_file(__DIR__ . '/_files/data.xml', 'Varien_Simplexml_Element');
        $this->assertTrue(isset($xml->node3->node4));
        $xml->node3->unsetSelf();
        $this->assertFalse(isset($xml->node3->node4));
        $this->assertFalse(isset($xml->node3));
        $this->assertTrue(isset($xml->node1));
    }
}
