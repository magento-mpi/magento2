<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Usa_Model_Simplexml_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testXmlentities()
    {
        $xmlElement = new Magento_Usa_Model_Simplexml_Element('<xml></xml>');
        $this->assertEquals('&amp;copy;&amp;', $xmlElement->xmlentities('&copy;&amp;'));
    }
}
