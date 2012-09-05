<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Layout_Argument_ProcessorConfig
 */
class Mage_Core_Model_Layout_Argument_ProcessorConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_ProcessorConfig
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout_Argument_ProcessorConfig();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param $type
     * @expectedException InvalidArgumentException
     * @dataProvider getArgumentHandlerByTypeWithNonStringTypeDataProvider
     */
    public function testGetArgumentHandlerByTypeWithNonStringType($type)
    {
        $this->_model->getArgumentHandlerByType($type);
    }

    public function getArgumentHandlerByTypeWithNonStringTypeDataProvider()
    {
        return array(
            'int value' => array(10),
            'object value' => array(new StdClass()),
            'null value' => array(null),
            'boolean value' => array(false),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetArgumentHandlerByTypeWithInvalidType()
    {
        $this->_model->getArgumentHandlerByType('dummy_type');
    }

    /**
     * @param string $type
     * @param string $className
     * @dataProvider getArgumentHandlerByTypeWithValidTypeDataProvider
     */
    public function testGetArgumentHandlerByTypeWithValidType($type, $className)
    {
        $this->assertEquals($className, $this->_model->getArgumentHandlerByType($type));
    }

    public function getArgumentHandlerByTypeWithValidTypeDataProvider()
    {
        return array(
            'object'  => array('object', 'Mage_Core_Model_Layout_Argument_Handler_Object'),
            'options' => array('options', 'Mage_Core_Model_Layout_Argument_Handler_Options')
        );
    }
}
