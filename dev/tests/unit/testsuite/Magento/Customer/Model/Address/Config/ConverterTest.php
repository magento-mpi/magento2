<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Address_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Model_Address_Config_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Customer_Model_Address_Config_Converter();
    }

    public function testConvert()
    {
        $inputData = new DOMDocument();
        $inputData->load(__DIR__ . '/_files/formats_merged.xml');
        $expectedResult = require __DIR__ . '/_files/formats_merged.php';
        $this->assertEquals($expectedResult, $this->_model->convert($inputData));
    }
}
