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
 * Test class for Mage_Core_Model_Layout_Argument_Handler_Options
 */
class Mage_Core_Model_Layout_Argument_Handler_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_Handler_Options
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;


    protected function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Layout_Argument_Handler_Options(
            array('objectFactory' => $this->_objectFactoryMock)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testProcessIfOptionModelIncorrect()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('StdClass')
            ->will($this->returnValue(new StdClass()));
        $this->_model->process('StdClass');
    }

    public function testProcess()
    {
        $optionArray = array('value' => 'LABEL');
        $optionsModel = $this->getMock(
            'Mage_Core_Model_Option_ArrayInterface',
            array(),
            array(),
            'Option_Array_Model',
            false);
        $optionsModel->expects($this->once())->method('toOptionArray')->will($this->returnValue($optionArray));
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Option_Array_Model')
            ->will($this->returnValue($optionsModel));
        $this->assertEquals($optionArray, $this->_model->process('Option_Array_Model'));
    }
}
