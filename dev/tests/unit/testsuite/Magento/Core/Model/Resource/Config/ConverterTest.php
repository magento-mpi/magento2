<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Config_Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var DOMDocument
     */
    protected $_source;

    protected function setUp()
    {
        $this->_filePath = __DIR__ . '/_files' . DIRECTORY_SEPARATOR;
        $this->_source = new DOMDocument();
        $this->_model = new Magento_Core_Model_Resource_Config_Converter();
    }

    /**
     * @covers Magento_Core_Model_Resource_Config_Converter::convert
     */
    public function testConvert()
    {
        $this->_source->loadXML(file_get_contents($this->_filePath . 'resources.xml'));
        $convertedFile = include ($this->_filePath . 'resources.php');
        $this->assertEquals($convertedFile, $this->_model->convert($this->_source));
    }
}