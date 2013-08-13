<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var Enterprise_Queue_Model_Event_Config_Converter
     */
    protected $_model;

    protected function setUp()
    {
        /** @todo Implement test logic here */
        
        $this->_model = new Enterprise_Queue_Model_Event_Config_Converter();
    }

    public function testConvert()
    {
        /** @todo Implement test logic here */

        $source = null;

        $this->_model->convert($source);
    }
}