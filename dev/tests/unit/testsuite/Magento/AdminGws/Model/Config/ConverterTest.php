<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_AdminGws_Model_Config_Converter
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new Magento_AdminGws_Model_Config_Converter();
        $this->_fixturePath = realpath(__DIR__ )
            . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $dom->load($this->_fixturePath . 'adminGws.xml');
        $actual = $this->_model->convert($dom);
        $expected = require ($this->_fixturePath . 'adminGws.php');
        $this->assertEquals($expected, $actual);
    }
}