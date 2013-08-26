<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Config_Converter
     */
    protected $_model;

    /** @var  DOMDocument */
    protected $_source;

    /** @var  array */
    protected $_targetArray;

    public function setUp()
    {
        $this->_model = new Mage_Widget_Model_Config_Converter();
        $this->_source = new DOMDocument();
        $this->_source->loadXML(
            file_get_contents(__DIR__ . '/_files/widget.xml')
        );
        $this->_targetArray = include(__DIR__ . '/_files/widgetArray.php');
    }

    public function testConvert()
    {
        $result = $this->_model->convert($this->_source);
        $this->assertEquals($this->_targetArray, $result);
    }
}
