<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Eav_Model_Entity_Attribute_Config_Converter
     */
    protected $_model;

    /**
     * Path to files
     *
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_model = new Magento_Eav_Model_Entity_Attribute_Config_Converter();
        $this->_filePath = realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $path = $this->_filePath . 'attributes.xml';
        $dom->load($path);
        $expectedData = include($this->_filePath . 'attributes.php');
        $this->assertEquals($expectedData, $this->_model->convert($dom));
    }
}