<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var Mage_Core_Model_Event_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        /** @todo Implement test logic here */
        
        $this->_model = new Mage_Core_Model_Event_Config_Converter();
    }

    public function testConvert()
    {
        /** @todo Implement test logic here */
        
        $source = null;
        
        $this->_model->convert($source);
    }
}