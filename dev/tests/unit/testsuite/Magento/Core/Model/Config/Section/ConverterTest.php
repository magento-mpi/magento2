<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Section_Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Core_Model_Config_Section_Converter();
    }

    public function testConvert()
    {
        $data = array(
            'some/config/path1' => 'value1',
            'some/config/path2' => 'value2',
        );
        $expectedResult = array(
            'some' => array(
                'config' => array(
                    'path1' => 'value1',
                    'path2' => 'value2',
                ),
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->convert($data));
    }
}
