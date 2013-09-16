<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_VersionsCms_Model_Hierarchy_Config_Converter();
    }

    /**
     * @covers Magento_VersionsCms_Model_Hierarchy_Config_Converter::convert
     */
    public function testConvert()
    {
        $basePath = realpath(__DIR__) . '/_files/';
        $path = $basePath . 'menuHierarchy.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'menuHierarchy.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}
