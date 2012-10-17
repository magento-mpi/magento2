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
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager_Zend', array('create'), array(), '', false);
        $this->_model = new Mage_Core_Model_Layout_Argument_Handler_Object($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_model);
    }

    public function testProcess()
    {
        $expected = new StdClass();
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('StdClass')
            ->will($this->returnValue(new StdClass()));
        $this->assertEquals($expected, $this->_model->process('StdClass'));
    }
}
