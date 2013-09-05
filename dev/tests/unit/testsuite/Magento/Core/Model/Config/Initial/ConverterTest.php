<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Initial_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Initial_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Core_Model_Config_Initial_Converter();
    }

    public function testConvert()
    {
        $fixturePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($fixturePath . 'config.xml'));
        $expectedResult = include $fixturePath . 'converted_config.php';
        $this->assertEquals($expectedResult, $this->_model->convert($dom));
    }
}
