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
 * Test class for Mage_Core_Model_Layout_Argument_Handler_Object
 */
class Mage_Core_Model_Layout_Argument_Handler_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_Handler_Object
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;


    protected function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Layout_Argument_Handler_Object(
            array('objectFactory' => $this->_objectFactoryMock)
        );
    }

    public function testProcess()
    {
        $expected = new StdClass();
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('StdClass')
            ->will($this->returnValue(new StdClass()));
        $this->assertEquals($expected, $this->_model->process('StdClass'));
    }
}
