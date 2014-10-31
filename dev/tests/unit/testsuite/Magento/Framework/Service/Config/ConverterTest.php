<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Service\Config\Converter
     */
    protected $_converter;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_converter = new \Magento\Framework\Service\Config\Converter();
    }

    /**
     * Test invalid data
     */
    public function testInvalidData()
    {
        $result = $this->_converter->convert(array('invalid data'));
        $this->assertEmpty($result);
    }

    /**
     * Test empty data
     */
    public function testConvertNoElements()
    {
        $result = $this->_converter->convert(new \DOMDocument());
        $this->assertEmpty($result);
    }

    /**
     * Test converting valid data object config
     */
    public function testConvert()
    {
        $expected = [
            'Magento\Tax\Service\V1\Data\TaxRate' => [
            ],
            'Magento\Catalog\Service\Data\V1\Product' => [
                'stock_item' => 'Magento\CatalogInventory\Service\Data\V1\StockItem'
            ],
            'Magento\Customer\Service\V1\Data\Customer' => [
                'custom_1' => 'Magento\Customer\Service\V1\Data\CustomerCustom',
                'custom_2' => 'Magento\CustomerExtra\Service\V1\Data\CustomerCustom2'
            ],
        ];

        $xmlFile = __DIR__ . '/_files/data_object_valid.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->_converter->convert($dom);
        $this->assertEquals($expected, $result);
    }
}
