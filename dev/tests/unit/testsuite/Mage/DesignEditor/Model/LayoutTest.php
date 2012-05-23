<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    public function testSanitizeLayout()
    {
        $data = file_get_contents(__DIR__ . '/_files/sanitize.xml');
        $xml = new Varien_Simplexml_Element($data);
        Mage_DesignEditor_Model_Layout::sanitizeLayout($xml);
        $this->assertStringMatchesFormatFile(__DIR__ . '/_files/sanitize_expected.txt', $xml->asNiceXml());
    }
}
