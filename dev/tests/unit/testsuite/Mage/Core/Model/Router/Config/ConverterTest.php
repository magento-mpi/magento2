<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Router_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Router_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Router_Config_Converter();
    }

    public function testConvert()
    {
        $basePath = realpath(__DIR__) . '/_files/';
        $path = $basePath . 'router.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);
        $expectedData = include($basePath . 'router.php');
        $this->assertEquals($expectedData, $this->_model->convert($domDocument));
    }
}