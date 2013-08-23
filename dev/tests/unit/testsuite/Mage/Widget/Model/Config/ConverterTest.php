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

    public function setUp()
    {
        $factoryMock = $this->getMock(
            'Magento_Simplexml_Config_Factory', array('create'), array(), '', false
        );
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue(new Magento_Simplexml_Config()));
        $this->_model = new Mage_Widget_Model_Config_Converter($factoryMock);
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'widget.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $convertedFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'widget_config.php';
        $expectedResult = include $convertedFile;
        $this->assertEquals($expectedResult, $this->_model->convert($dom), '', 0, 20);
    }


    /**
     * @param string $xmlData
     * @dataProvider wrongXmlDataProvider
     * @expectedException Exception
     */
    public function testThrowsExceptionWhenXmlHasWrongFormat($xmlData)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xmlData);
        $this->_model->convert($dom);
    }

    /**
     * @return array
     */
    public function wrongXmlDataProvider()
    {
        return array(
            array(
                '<?xml version="1.0"?><widgets>',
            )
        );
    }
}
