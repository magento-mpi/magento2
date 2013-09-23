<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Export_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Export_Config_Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    public function setUp()
    {
        $this->_filePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_model = new Magento_ImportExport_Model_Export_Config_Converter();
    }

    public function testConvert()
    {
        $testDom = $this->_filePath . 'export.xml';
        $dom = new DOMDocument();
        $dom->load($testDom);
        $expectedArray = include($this->_filePath . 'export.php');
        $this->assertEquals($expectedArray, $this->_model->convert($dom));
    }
}