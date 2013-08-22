<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Backend_Model_Menu_Config_Converter();
    }

    public function testConvertIfNodeHasAttribute()
    {
        $basePath = realpath(__DIR__) . '/../../_files/';
        $path = $basePath . 'menu_merged.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'menu_merged.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}
