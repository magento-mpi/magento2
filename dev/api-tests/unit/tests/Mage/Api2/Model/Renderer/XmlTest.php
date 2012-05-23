<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test response renderer XML adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Renderer_XmlTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test render data
     *
     * @dataProvider dataProviderSuccess
     * @param string $encoded
     * @param array|string|float|int|bool $decoded
     */
    public function testRenderData($encoded, $decoded)
    {
        /** @var $adapter Mage_Api2_Model_Renderer_Xml */
        $adapter = Mage::getModel('api2/renderer_xml');

        $xml = $adapter->render($decoded);
        $simpleXml = new SimpleXMLElement($xml);
        $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
        $this->assertEquals($encoded, $xml,
            'Decoded data is not like expected.');
    }

    /**
     * Provides data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return require dirname(__FILE__) . '/_fixtures/xmlDataProviderSuccessTest.php';
    }
}
