<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_ProductTypes_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_ProductTypes_Config_Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_model = new Magento_Catalog_Model_ProductTypes_Config_Converter();
        $this->_filePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvertIfNodeNotExist()
    {
        $source = $this->_filePath . 'product_types.xml';
        $dom = new DOMDocument();
        $dom->load($source);
        $expected = include($this->_filePath . 'product_types.php');
        $this->assertEquals($expected, $this->_model->convert($dom));
    }
}