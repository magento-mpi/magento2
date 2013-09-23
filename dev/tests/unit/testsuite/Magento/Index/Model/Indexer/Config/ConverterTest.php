<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_Indexer_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Index_Model_Indexer_Config_Converter();
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config_Converter::convert
     */
    public function testConvert()
    {
        $basePath = realpath(__DIR__) . '/_files/';
        $path = $basePath . 'indexers.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'indexers.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}
