<?php
/**
 * Magento_Persistent_Model_Persistent_Config_Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Persistent_Model_Persistent_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Persistent_Model_Persistent_Config_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Persistent_Model_Persistent_Config_Converter();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . '/_files/persistent.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $convertedFile = __DIR__ . '/_files/expectedArray.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convert($dom), '', 0, 20);
    }

    public function testConvertBlocks()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . '/_files/persistent.xml';
        $dom->loadXML(file_get_contents($xmlFile));
        $xpath = new DOMXPath($dom);
        $domNodeList = $xpath->query('/config/instances/blocks/reference');
        $convertedFile = __DIR__ . '/_files/expectedBlocksArray.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convertBlocks($domNodeList), '', 0, 20);
    }
}
