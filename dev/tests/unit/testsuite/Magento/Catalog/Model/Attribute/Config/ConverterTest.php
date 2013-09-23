<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Attribute_Config_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Catalog_Model_Attribute_Config_Converter();
    }

    public function testConvert()
    {
        $inputData = new DOMDocument();
        $inputData->load(__DIR__ . '/_files/attributes_config_merged.xml');
        $expectedResult = require __DIR__ . '/_files/attributes_config_merged.php';
        $this->assertEquals($expectedResult, $this->_model->convert($inputData));
    }
}
