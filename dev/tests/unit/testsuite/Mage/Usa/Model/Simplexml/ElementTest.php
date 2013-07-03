<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Usa_Model_Simplexml_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testXmlentities()
    {
        $xmlElement = new Mage_Usa_Model_Simplexml_Element('<xml></xml>');
        $this->assertEquals('&amp;copy;&amp;', $xmlElement->xmlentities('&copy;&amp;'));
    }
}
