<?php
/**
 * Magento_Install_Model_Config_Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Magento_Install_Model_Config_Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Install_Model_Config_Converter();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . '/_files/install_wizard.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $convertedFile = __DIR__ . '/_files/install_wizard_config.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convert($dom), '', 0, 20);
    }
}
