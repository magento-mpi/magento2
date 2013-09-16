<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_WebsiteRestriction_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_WebsiteRestriction_Model_Config_Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_model = new Magento_WebsiteRestriction_Model_Config_Converter();
        $this->_filePath = realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $dom->load($this->_filePath . 'webrestrictions.xml');
        $actual = $this->_model->convert($dom);
        $expected = require($this->_filePath . 'webrestrictions.php');
        $this->assertEquals($expected, $actual);
    }
}